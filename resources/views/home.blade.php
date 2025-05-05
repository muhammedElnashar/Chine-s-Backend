@extends('layouts.app')

@section('title')
    Dashboard
@endsection

@push('css')
    <style>
        .card {
            width: 380px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 15px;
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.2);
        }

        .transaction-badge {
            font-size: 0.9rem;
            width: 4.5rem;
            height: 4.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
        }
        h2{
            font-size: 20rem;
            font-weight: bold;
        }
    </style>
@endpush

{{--@section('content')--}}

{{--    <div class="container my-5">--}}
{{--        <div class="row g-5">--}}
{{--            <!-- Total Courses -->--}}
{{--            <div class="col-md-4">--}}
{{--                <div class="card text-white bg-primary shadow-lg position-relative">--}}
{{--                    <div class="card-header mx-auto fw-bold fs-1 py-4">--}}
{{--                        Total Courses--}}
{{--                    </div>--}}
{{--                    <div class="card-body d-flex align-items-center justify-content-center">--}}
{{--                        <h2 class="card-title text-center fw-bold">{{ $courses }}</h2>--}}
{{--                    </div>--}}
{{--                    <div class="transaction-badge bg-dark text-dark rounded-circle position-absolute top-0 start-100 translate-middle p-2 shadow">--}}
{{--                        <i class="fas fa-book fs-1"></i>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <!-- Total Users -->--}}
{{--            <div class="col-md-4">--}}
{{--                <div class="card text-white bg-success shadow-lg position-relative">--}}
{{--                    <div class="card-header mx-auto fw-bold fs-1 py-4">--}}
{{--                        Total Users--}}
{{--                    </div>--}}
{{--                    <div class="card-body d-flex align-items-center justify-content-center">--}}
{{--                        <h2 class="card-title text-center fw-bold">{{ $users }}</h2>--}}
{{--                    </div>--}}
{{--                    <div class="transaction-badge bg-dark rounded-circle position-absolute top-0 start-100 translate-middle p-2 shadow">--}}
{{--                        <i class="fas fa-users fs-1"></i>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <!-- Total Categories -->--}}
{{--            <div class="col-md-4">--}}
{{--                <div class="card text-white bg-info shadow-lg position-relative">--}}
{{--                    <div class="card-header mx-auto fw-bold fs-1 py-4">--}}
{{--                        Total Categories--}}
{{--                    </div>--}}
{{--                    <div class="card-body d-flex align-items-center justify-content-center">--}}
{{--                        <h2 class="card-title text-center fw-bold ">{{ $category }}</h2>--}}
{{--                    </div>--}}
{{--                    <div class="transaction-badge bg-dark text-white rounded-circle position-absolute top-0 start-100 translate-middle p-2 shadow">--}}
{{--                        <i class="fas fa-list-alt fs-1"></i>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--@endsection--}}
