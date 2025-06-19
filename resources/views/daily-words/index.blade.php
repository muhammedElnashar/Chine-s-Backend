@extends("layouts.app")

@section('title')
    Daily Audio Words
@endsection

@push("css")
    <style>
        .filter-form {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

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
                            <a href="{{ route('words.create') }}" class="btn btn-primary" style="border-radius: 20px">
                                Add Audio Word
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-0">
                    <!-- Filter Form -->
                    <div class="filter-form">
                        <form method="GET" action="{{ route('words.index') }}">
                            <input type="date" name="date" value="{{ request('date') }}"/>
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <button type="button" class="btn btn-secondary ms-2"
                                    onclick="window.location.href='{{ route('words.index') }}'">Clear Filter</button>
                        </form>
                    </div>

                    <table class="table align-middle table-row-dashed fs-6 gy-5">
                        <thead>
                        <tr class="text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                            <th>Date</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th class="text-center" >Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($exercises as $exercise)
                            <tr>
                                <td>{{ $exercise->date }}</td>
                                <td>{{ $exercise->title }}</td>
                                <td>{{ $exercise->description }}</td>
                                <td >
                                    <div class="d-flex justify-content-center flex-shrink-0">
                                    <a href="#"
                                       class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-2"
                                       data-bs-toggle="modal"
                                       data-bs-target="#modal_words_{{ $exercise->id }}"
                                       title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <form method="post" action="{{ route('words.destroy', $exercise->id) }}">
                                        @csrf
                                        @method("DELETE")
                                        <button type="submit"
                                                class="btn btn-icon btn-bg-light btn-active-color-primary deleted-btn btn-sm ms-2">
                                            <span class="svg-icon svg-icon-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                     viewBox="0 0 24 24" fill="none">
                                                    <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z"
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

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <!-- Modals -->
                    @foreach($exercises as $exercise)
                        <div class="modal fade" id="modal_words_{{ $exercise->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered mw-900px">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h2>Audio Words</h2>
                                        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                                            <span class="svg-icon svg-icon-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                     fill="none">
                                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                                          transform="rotate(-45 6 17.3137)" fill="black"/>
                                                    <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                                          transform="rotate(45 7.41422 6)" fill="black"/>
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="modal-body py-lg-10 px-lg-10">
                                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                                            <thead>
                                            <tr class="text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                                <th>Audio</th>
                                                <th>Meaning</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($exercise->audioWords as $word)
                                                <tr>
                                                    <td>
                                                        <audio controls>
                                                            <source src="{{ Storage::disk("s3")->url($word->audio_file) }}"
                                                                    type="audio/mpeg">
                                                            Your browser does not support the audio element.
                                                        </audio>
                                                    </td>
                                                    <td>{{ $word->word_meaning }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">No audio words available.</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-start mt-5">
                        {{ $exercises->links() }}
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
                    title: 'هل انت متأكد من الحذف؟',
                    text: "لن تتمكن من التراجع عن هذا!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'لا , تراجع',
                    confirmButtonText: 'نعم , تأكيد الحذف'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
