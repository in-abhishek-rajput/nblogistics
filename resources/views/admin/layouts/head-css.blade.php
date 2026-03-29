{{-- resources/views/admin/layouts/head-css.blade.php --}}
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">

{{-- Favicon --}}
<link href="{{ asset('admin/img/favicon.ico') }}" rel="icon">

{{-- Google Fonts --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">

{{-- Icon Fonts --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

{{-- Library Styles --}}
<link href="{{ asset('lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
<link href="{{ asset('lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet" />

<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

{{-- Bootstrap --}}
<link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

{{-- Main Styles --}}
<link href="{{ asset('css/style.css') }}" rel="stylesheet">

@stack('styles')