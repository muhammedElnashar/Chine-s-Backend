@extends('layouts.app')

@section('title', 'Sections Files')

@section('content')
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xl">
            <div class="card mx-auto" style="border-radius: 25px;">
                <div class="card-header border-0 pt-6 d-flex justify-content-between align-items-center">
                    <h3 class="card-title fw-bolder">Sections Files</h3>
                    <div>
                        <a href="{{ route('courses.levels.index', $course) }}" class="btn btn-light" style="border-radius: 20px;">Back to Sections</a>
                        <a href="{{ route('courses.levels.files.create', [$course, $level]) }}" class="btn btn-primary ms-3" style="border-radius: 20px;">Add New File</a>
                    </div>
                </div>

                <div class="card-body pt-0">
                    @if($files->count())
                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th>name</th>
                                <th>File Url</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($files as $file)
                                <tr>
                                    <td>{{ $file->name }}</td>
                                    <td>
                                        @if($file->path)
                                            <a href="{{\Illuminate\Support\Facades\Storage::disk('s3')->url($file->path) }}" target="_blank" class="link-primary">Show File</a>
                                        @else
                                            No URL
                                        @endif
                                    </td>

                                    <td class="text-end pe-4">
                                        <form action="{{ route('courses.levels.files.destroy', [$course, $level, $file]) }}" method="POST" class="d-inline-block" >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-primary delete-btn btn-sm" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <div class="mt-4">
                            {{ $files->links() }}
                        </div>
                    @else
                        <p class="text-center text-muted">No Files found for this level.</p>
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
