{{-- resources/views/admin/layouts/topbar.blade.php --}}
<nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-2">
    <a href="#" class="navbar-brand d-flex me-4">
        <h1 class="mb-0 fs-5">@yield('page_title')</h1>
    </a>

    <div class="ms-auto d-flex gap-2">
        <a class="btn btn-sm btn-primary px-3" data-bs-toggle="modal" data-bs-target="#addParty">
            <i class="bi bi-plus me-1"></i>Add Party
        </a>

        <a data-bs-toggle="offcanvas" href="#offcanvasExample" class="btn btn-sm btn-success px-3">
            <i class="bi bi-plus me-1"></i>Add Trip
        </a>
    </div>
</nav>
