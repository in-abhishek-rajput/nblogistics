{{-- resources/views/admin/layouts/sidebar.blade.php --}}
<!-- Sidebar Start -->
<div class="sidebar pe-0">
    <nav class="navbar bg-light navbar-light px-1">
        <!-- <a href="index.html" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i class="fa fa-hashtag me-2"></i>DASHMIN</h3>
                </a> -->
        <div class="d-flex align-items-center ms-3 mb-3">
            <div class="position-relative">
                <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                <div
                    class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1">
                </div>
            </div>
            <div class="ms-3">
                <h6 class="mb-0">Name</h6>
                <span>9876543210</span>
            </div>
        </div>
        <div class="navbar-nav w-100">
            <a href="{{ route('dashboard') }}" class="nav-item nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="fa fa-address-book me-2"></i>Dashbaord</a>
            <a href="{{ route('parties.index') }}" class="nav-item nav-link {{ request()->routeIs('parties.*') ? 'active' : '' }}"><i class="fa fa-address-book me-2"></i>Parties</a>
            <a href="{{ route('trips.index') }}" class="nav-item nav-link {{ request()->routeIs('trips.*') ? 'active' : '' }}"><i class="fa fa-map me-2"></i>Trips</a>
            <a href="{{ route('drivers.index') }}" class="nav-item nav-link {{ request()->routeIs('drivers.*') ? 'active' : '' }}"><i class="fa fa-users me-2"></i>Drivers</a>
            <a href="{{ route('trucks.index') }}" class="nav-item nav-link {{ request()->routeIs('trucks.*') ? 'active' : '' }}"><i class="fa fa-truck me-2"></i>Trucks</a>
            <a href="{{ route('trip-expenses') }}" class="nav-item nav-link {{ request()->routeIs('trip-expenses.*') ? 'active' : '' }}"><i class="fa fa-receipt me-2"></i>Expenses</a>
            <a href="{{ route('invoices.index') }}" class="nav-item nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}"><i class="fa fa-file-invoice me-2"></i>Invoices</a>
            <a href="{{ route('builty.index') }}" class="nav-item nav-link {{ request()->routeIs('builty.*') ? 'active' : '' }}"><i class="fa fa-list me-2"></i>Builty</a>
            <a href="{{ route('reports.index') }}" class="nav-item nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"><i class="fa fa-file me-2"></i>Reports</a>
            <a href="{{ route('profile.index') }}" class="nav-item nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"><i class="fa fa-id-card me-2"></i>Profile</a>
        </div>

        <div class="mt-5 text-center ms-3">
            <h4>N. B. Logistics</h4>
            <label><i class="bi bi-shield-fill-check text-success me-1"></i>100% Safe & Secure</label>
        </div>
    </nav>
</div>
<!-- Sidebar End -->
