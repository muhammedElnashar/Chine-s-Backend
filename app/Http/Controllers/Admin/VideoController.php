<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVideoRequest;
use App\Models\Course;
use App\Models\Level;
use App\Models\Video;
use Illuminate\Http\Request;
use Aws\S3\S3Client;
use Illuminate\Support\Str;


class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Course $course, Level $level)
    {
        $videos = $level->videos()->paginate(5);

        return view('videos.index', compact('course', 'level', 'videos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Course $course,Level $level)
    {
        return view('videos.create', compact('course','level'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVideoRequest $request, Course $course, Level $level)
    {
        $videosData = $request->input('videos');

        foreach ($videosData as $videoData) {
            $level->videos()->create([
                'title' => $videoData['title'],
                'video_url' => $videoData['path'],
                'duration' => $videoData['duration'],
            ]);
        }

        return redirect()->route('courses.levels.videos.index', [$course, $level])
            ->with('success', 'Videos uploaded successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Video $video)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course, Level $level, Video $video)
    {
        return view('videos.edit', compact('course', 'level', 'video'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course, Level $level, Video $video)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $video->update([
            'title' => $request->title,
        ]);

        return redirect()->route('courses.levels.videos.index', [$course, $level])
            ->with('success', 'Video updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course, Level $level, Video $video)
    {
        // Delete file from S3 if applicable
        $key = str_replace(config('filesystems.disks.s3.url'), '', $video->video_url);

        try {
            $s3Client = new S3Client([
                'region' => config('filesystems.disks.s3.region'),
                'version' => 'latest',
                'credentials' => [
                    'key' => config('filesystems.disks.s3.key'),
                    'secret' => config('filesystems.disks.s3.secret'),
                ],
            ]);

            $s3Client->deleteObject([
                'Bucket' => config('filesystems.disks.s3.bucket'),
                'Key' => $key,
            ]);
        } catch (\Exception $e) {
            // Log the error but continue with deletion from database
            \Log::error('Failed to delete video from S3: ' . $e->getMessage());
        }

        $video->delete();

        return redirect()->route('courses.levels.videos.index', [$course, $level])
            ->with('success', 'Video deleted successfully!');
    }


    public function getMultipartUploadUrls(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
            'filetype' => 'required|string',
            'parts' => 'required|integer|min:1|max:10000',
        ]);

        $allowedTypes = ['video/mp4', 'video/avi', 'video/mov', 'video/mkv', 'video/quicktime', 'video/x-matroska'];
        if (!in_array($request->filetype, $allowedTypes)) {
            return response()->json(['error' => 'Unsupported file type.'], 422);
        }

        $s3Client = new S3Client([
            'region' => config('filesystems.disks.s3.region'),
            'version' => 'latest',
            'credentials' => [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
        ]);

        $bucket = config('filesystems.disks.s3.bucket');
        $key = 'videos/' . Str::uuid() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $request->filename);

        $result = $s3Client->createMultipartUpload([
            'Bucket' => $bucket,
            'Key' => $key,
            'ContentType' => $request->filetype,
        ]);

        $uploadId = $result['UploadId'];

        $parts = [];
        for ($i = 1; $i <= $request->parts; $i++) {
            $command = $s3Client->getCommand('UploadPart', [
                'Bucket' => $bucket,
                'Key' => $key,
                'UploadId' => $uploadId,
                'PartNumber' => $i,
            ]);
            // Increasing link duration to 60 minutes
            $presignedUrl = (string) $s3Client->createPresignedRequest($command, '+60 minutes')->getUri();
            $parts[] = [
                'partNumber' => $i,
                'url' => $presignedUrl,
            ];
        }

        return response()->json([
            'uploadId' => $uploadId,
            'key' => $key,
            'parts' => $parts,
        ]);
    }

    public function completeMultipartUpload(Request $request)
    {
        $request->validate([
            'uploadId' => 'required|string',
            'key' => 'required|string',
            'parts' => 'required|array',
            'parts.*.PartNumber' => 'required|integer',
            'parts.*.ETag' => 'required|string',
        ]);

        $s3Client = new S3Client([
            'region' => config('filesystems.disks.s3.region'),
            'version' => 'latest',
            'credentials' => [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
        ]);

        $bucket = config('filesystems.disks.s3.bucket');

        try {
            $result = $s3Client->completeMultipartUpload([
                'Bucket' => $bucket,
                'Key' => $request->key,
                'UploadId' => $request->uploadId,
                'MultipartUpload' => [
                    'Parts' => $request->parts,
                ],
            ]);

            $s3Url = $result['Location'];

            // Get the public URL in the expected format for your application
            $publicUrl = config('filesystems.disks.s3.url') . '/' . $request->key;

            return response()->json([
                'message' => 'Upload completed',
                'location' => $s3Url,
                'path' => $request->key,
                'url' => $publicUrl
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function abortMultipartUpload(Request $request)
    {
        $request->validate([
            'uploadId' => 'required|string',
            'key' => 'required|string',
        ]);

        $s3Client = new S3Client([
            'region' => config('filesystems.disks.s3.region'),
            'version' => 'latest',
            'credentials' => [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
        ]);

        $bucket = config('filesystems.disks.s3.bucket');

        try {
            $s3Client->abortMultipartUpload([
                'Bucket' => $bucket,
                'Key' => $request->key,
                'UploadId' => $request->uploadId,
            ]);

            return response()->json(['message' => 'Upload aborted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
