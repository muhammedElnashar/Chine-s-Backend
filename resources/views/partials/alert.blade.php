<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('success'))
    <script>
        Swal.fire({
            title: 'تم بنجاح',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonText: 'حسنًا',
            timer: 1500,
        });

    </script>
@endif

@if (session('error'))
    <script>
        Swal.fire({
            title: 'خطأ',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonText: 'حسنًا',
            timer: 1500,

        });
    </script>
@endif

@if (session('warning'))
    <script>
        Swal.fire({
            title: 'تحذير',
            text: '{{ session('warning') }}',
            icon: 'warning',
            confirmButtonText: 'حسنًا',
            timer: 1500,

        });
    </script>
@endif<?php
