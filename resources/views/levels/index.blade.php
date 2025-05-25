@extends("layouts.app")

@section('title', 'Levels for ' . $course->title)

@section('content')
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xl">
            <div class="card mx-auto" style="border-radius: 25px;">
                <div class="card-header border-0 pt-6 d-flex justify-content-between align-items-center">
                    <h3 class="card-title fw-bolder">Levels for Course: {{ $course->title }}</h3>
                    <div class="card-toolbar">
                        <a href="{{ route('courses.levels.create', $course) }}" class="btn btn-primary" style="border-radius: 20px">Add Level</a>
                        <a href="{{ route('courses.index') }}" class="btn btn-light ms-2" style="border-radius: 20px">Back to Courses</a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    @if($levels->count())
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_levels_table">
                            <thead>
                            <tr class="text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th>Title</th>
                                <th>Position</th>
                                <th>Price</th>
                                <th>Is Free</th>
                                <th class="text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="fw-bold text-gray-600">
                            @foreach($levels as $level)
                                <tr>
                                    <td>{{ $level->title }}</td>
                                    <td>{{ $level->position  }}</td>
                                    <td>{{ $level->price}}</td>
                                    @if($level->is_free)
                                        <td>Yes</td>
                                    @else
                                        <td>No</td>

                                    @endif


                                    <td class="text-center">
                                        <div class="d-flex justify-content-center flex-shrink-0">
                                            <a href="{{ route('courses.levels.exams.index', [$course, $level]) }}" class="btn btn-bg-light btn-active-color-primary btn-sm ms-2">Exam</a>
                                            <a href="{{ route('courses.levels.videos.index', [$course, $level]) }}" class="btn btn-bg-light btn-active-color-primary btn-sm ms-2">Videos</a>
                                            <a href="{{ route('courses.levels.edit', [$course, $level]) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm ms-2">
                                            <span class="svg-icon svg-icon-2">
																				<svg xmlns="http://www.w3.org/2000/svg"
                                                                                     width="24" height="24"
                                                                                     viewBox="0 0 24 24" fill="none">
																					<path opacity="0.3"
                                                                                          d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z"
                                                                                          fill="black"/>
																					<path
                                                                                        d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z"
                                                                                        fill="black"/>
																				</svg>
																			</span>
                                            </a>
                                            <form method="POST" action="{{ route('courses.levels.destroy', [$course, $level]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm ms-2 delete-btn">
                                                        <span class="svg-icon svg-icon-3">
                                                            <!-- SVG icon for delete -->
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                                <path d="M5 9c0-.55.45-1 1-1h10c.55 0 1 .45 1 1v9c0 1.66-1.34 3-3 3H8c-1.66 0-3-1.34-3-3V9z" fill="black"/>
                                                                <path opacity="0.5" d="M5 5c0-.55.45-1 1-1h10c.55 0 1 .45 1 1v0c0 .55-.45 1-1 1H6c-.55 0-1-.45-1-1z" fill="black"/>
                                                                <path opacity="0.5" d="M9 4c0-.55.45-1 1-1h4c.55 0 1 .45 1 1v0H9z" fill="black"/>
                                                            </svg>
                                                        </span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div>{{ $levels->links() }}</div>
                    @else
                        <p class="text-center text-muted">No levels found for this course.</p>
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
