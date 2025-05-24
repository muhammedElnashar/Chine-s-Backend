@extends("layouts.app")

@section('title')
    Courses
@endsection

@section('content')
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xl">
            <div class="card mx-auto" style="border-radius: 25px">
                <div class="card-header border-0 pt-6">
                    <div class="card-title"></div>
                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('courses.create') }}" class="btn btn-primary" style="border-radius: 20px">Add Course</a>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_courses_table">
                        <thead>
                        <tr class="text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                            <th>Title</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Image</th>
                            <th class="text-center">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="fw-bold text-gray-600">
                        @foreach($courses as $course)
                            <tr>
                                <td>{{ $course->title }}</td>
                                <td>{{ \Illuminate\Support\Str::words($course->description, 10, '...') }}</td>
                                <td>{{ $course->type }}</td>
                                <td>{{ $course->price }}</td>
                                <td>
                                    @if($course->image)
                                        <img src="{{ asset('storage/' . $course->image) }}" width="50" height="50" alt="Course Image">
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center flex-shrink-0">
                                        <a href="{{ route('courses.levels.index', $course) }}" class="btn  btn-bg-light btn-active-color-primary btn-sm ms-2">Manage Levels</a>
                                        <a href="{{route('courses.edit', $course)}}"
                                           class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm ms-2">
                                            <!--begin::Svg Icon | path: icons/duotune/art/art005.svg-->
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
                                            <!--end::Svg Icon-->

                                        </a>
                                        <form method="post" action="{{route('courses.destroy', $course)}}">
                                            @csrf
                                            @method("DELETE")
                                            <button type="submit"  class="btn btn-icon btn-bg-light btn-active-color-primary delete-btn btn-sm ms-2" >
                                                <span class="svg-icon svg-icon-3">
																				<svg xmlns="http://www.w3.org/2000/svg"
                                                                                     width="24" height="24"
                                                                                     viewBox="0 0 24 24" fill="none">
																					<path
                                                                                        d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z"
                                                                                        fill="black"/>
																					<path opacity="0.5"
                                                                                          d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z"
                                                                                          fill="black"/>
																					<path opacity="0.5"
                                                                                          d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z"
                                                                                          fill="black"/>
																				</svg>
																			</span>
                                            </button>
                                        </form>

                                    </div>
{{--                                    <a href="{{ route('courses.edit', $course) }}" class="btn btn-primary btn-sm me-1">Edit</a>--}}
{{--                                    <form method="POST" action="{{ route('courses.destroy', $course) }}" style="display:inline-block;">--}}
{{--                                        @csrf--}}
{{--                                        @method('DELETE')--}}
{{--                                        <button type="submit" class="btn btn-danger btn-sm delete-btn">Delete</button>--}}
{{--                                    </form>--}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div>{{ $courses->links() }}</div>
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
