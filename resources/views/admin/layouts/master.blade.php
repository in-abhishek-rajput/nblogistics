{{-- resources/views/admin/layouts/master.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <title>@yield('title')</title>
    @include('admin.layouts.head-css')
    @livewireStyles
</head>

<body>
    <div class="container-fluid position-relative bg-white d-flex p-0">

        {{-- Spinner --}}
        <div id="spinner"
            class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
        </div>

        {{-- Sidebar --}}
        @include('admin.layouts.sidebar')

        {{-- Content --}}
        <div class="content">

            {{-- Topbar --}}
            {{-- @include('admin.layouts.topbar') --}}

            {{-- Page Content --}}
            @yield('content')

            {{-- Footer --}}
            @include('admin.layouts.footer')
        </div>

        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top">
            <i class="bi bi-arrow-up"></i>
        </a>
    </div>

    @include('admin.layouts.vendor-scripts')
     @livewireScripts
    @stack('scripts')
</body>

</html>