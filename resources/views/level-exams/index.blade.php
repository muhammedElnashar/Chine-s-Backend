@extends("layouts.app")

@section('title')
    Level Exams
@endsection

@push("css")
    <style>
        .filter-form {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .filter-form input[type="text"],
        .filter-form input[type="date"] {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 200px;
        }
    </style>
@endpush

@section('content')
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xl">
            <div class="card mx-auto" style="border-radius: 25px">
                <div class="card-header border-0 pt-6">
                    <div class="card-title"></div>
                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                            <a href="{{ route('courses.levels.exams.create', [$course, $level]) }}" class="btn btn-primary"
                               style="border-radius: 20px">Add Exam</a>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <!-- Filter Form -->
                    <div class="filter-form">
                        <form method="GET" action="{{ route('courses.levels.exams.index', [$course, $level]) }}">
                            <input type="text" name="title" placeholder="Search by Title" value="{{ request('title') }}" />
                            <input type="date" name="date" value="{{ request('date') }}" />
                            <button type="submit" class="btn btn-primary">
                                <span class="indicator-label">Filter</span>
                            </button>
                            <button type="button" class="btn btn-secondary ms-2"
                                    onclick="window.location.href='{{ route('courses.levels.exams.index', [$course, $level]) }}'">Clear Filter
                            </button>
                        </form>
                    </div>

                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_customers_table">
                        <thead>
                        <tr class="text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                            <th class="min-w-150px">Title</th>
                            <th class="min-w-300px">Description</th>
                            <th class="min-w-150px">Created At</th>
                            <th class="min-w-100px text-center">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($exams as $exam)
                            <tr>
                                <td>{{ $exam->title }}</td>
                                <td>{{ Str::limit($exam->description, 50) }}</td>
                                <td>{{ $exam->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <div class="d-flex justify-content-center flex-shrink-0">
                                        <a href="{{ route('courses.levels.exams.show', [$course, $level, $exam]) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-2" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('courses.levels.exams.edit', [$course, $level, $exam]) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-2" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="post" action="{{ route('courses.levels.exams.destroy', [$course, $level, $exam]) }}">
                                            @csrf
                                            @method("DELETE")
                                            <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-primary deleted-btn btn-sm" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div>
                        {{ $exams->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("script")
    <script>
        $(document).ready(function () {
            $('.deleted-btn').click(function (e) {
                let form = $(this).parents('form');
                e.preventDefault();
                Swal.fire({
                    title: 'هل أنت متأكد من حذف الامتحان؟',
                    text: "لن تتمكن من التراجع عن هذا!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'لا، تراجع',
                    confirmButtonText: 'نعم، تأكيد الحذف'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
