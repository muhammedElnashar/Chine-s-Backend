@extends('layouts.app')

@section('title', 'Videos of Level: ' . $level->title)

@section('content')
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xl">
            <div class="card mx-auto" style="border-radius: 25px;">
                <div class="card-header border-0 pt-6 d-flex justify-content-between align-items-center">
                    <h3 class="card-title fw-bolder">Videos for Level: {{ $level->title }}</h3>
                    <div>
                        <a href="{{ route('courses.levels.index', $course) }}" class="btn btn-light" style="border-radius: 20px;">Back to Levels</a>
                        <a href="{{ route('courses.levels.videos.create', [$course, $level]) }}" class="btn btn-primary ms-3" style="border-radius: 20px;">Add New Video</a>
                    </div>
                </div>

                <div class="card-body pt-0">
                    @if($videos->count())
                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th>Title</th>
                                <th>Video Url</th>
                                <th>Duration</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($videos as $video)
                                <tr>
                                    <td>{{ $video->title }}</td>
                                    <td>
                                        @if($video->video_url)
                                            <a href="{{"https://chines-app-courses.s3.us-east-1.amazonaws.com/". $video->video_url }}" target="_blank" class="link-primary">Watch Video</a>
                                        @else
                                            No URL
                                        @endif
                                    </td>
                                    <td>{{ $video->duration }}</td>

                                    <td class="text-end pe-4">
                                        <a href="{{ route('courses.levels.videos.edit', [$course, $level, $video]) }}" class="btn btn-sm btn-warning me-2" style="border-radius: 20px;">Edit</a>
                                        <form action="{{ route('courses.levels.videos.destroy', [$course, $level, $video]) }}" method="POST" class="d-inline-block" >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm delete-btn btn-danger" style="border-radius: 20px;">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <div class="mt-4">
                            {{ $videos->links() }}
                        </div>
                    @else
                        <p class="text-center text-muted">No videos found for this level.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function(e){
                e.preventDefault();
                let form = this.closest('form');
                Swal.fire({
                    title: 'هل أنت متأكد من الحذف؟',
                    text: "لا يمكنك التراجع عن هذا!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء',
                }).then(result => {
                    if(result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
