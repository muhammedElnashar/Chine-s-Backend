<head><base href="">
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="{{{asset("assets/plugins/global/plugins.bundle.css")}}}" rel="stylesheet" type="text/css" />
    <link href="{{asset("assets/css/style.bundle.css")}}" rel="stylesheet" type="text/css" />


    @stack('css')
</head>
