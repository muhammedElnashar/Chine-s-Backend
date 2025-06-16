<?php
namespace App\Services;

use Aws\S3\S3Client;

class VideoService
{
    public static function generatePresignedUrl(string $key, int $minutes = 120): string
    {
        $s3Client = new S3Client([
            'region' => config('filesystems.disks.s3.region'),
            'version' => 'latest',
            'credentials' => [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
        ]);

        $command = $s3Client->getCommand('GetObject', [
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => $key,
        ]);

        $request = $s3Client->createPresignedRequest($command, '+' . $minutes . ' minutes');

        return (string) $request->getUri();
    }
}
