    <!DOCTYPE html>
<html>
<!--begin::Head-->
@include('layouts._head')
<!--end::Head-->
<!--begin::Body-->
<body id="kt_body"
      class="header-fixed print-content-only header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed"
      style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px">
@include('partials.alert')

<!--begin::Main-->
<!--begin::Root-->
<div class="d-flex flex-column flex-root">
    <!--begin::Page-->
    <div class="page d-flex flex-row flex-column-fluid">
        <!--begin::Aside-->
        @include("layouts._sidebar")
        <!--end::Aside-->
        <!--begin::Wrapper-->
        <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
            <!--begin::Header-->
            @include("layouts._header")
            <!--end::Header-->
            <!--begin::Content-->
            @yield('content')
            <!--end::Content-->
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Page-->
</div>
<!--end::Root-->
<!--begin::Drawers-->

<!--end::Main-->
@include("layouts._footer")
</body>
<!--end::Body-->
</html>
