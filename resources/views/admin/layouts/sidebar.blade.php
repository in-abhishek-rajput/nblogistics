{{-- resources/views/admin/layouts/sidebar.blade.php --}}
<!-- Sidebar Start -->
<div class="sidebar pe-0">
    <nav class="navbar bg-light navbar-light px-1">
        <div class="d-flex align-items-center ms-3 mb-3">
            <div class="position-relative">
                <img class="rounded" src="img/logo.png" alt="" style="height: 40px;">
                <div
                    class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1">
                </div>
            </div>
            <div class="ms-3">
                <h6 class="mb-0">{{ auth()->check() ? auth()->user()->name : 'Name' }}</h6>
                <span>{{ auth()->check() ? auth()->user()->mobile : '---' }}</span>
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
            <a href="{{ route('builty.index') }}" class="nav-item nav-link {{ request()->routeIs('builty.*') ? 'active' : '' }}"><i class="fa fa-list me-2"></i>Bilty</a>
            <div class="dropdown dropstart">
                <a href="#" class="nav-item nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" data-bs-toggle="dropdown"><i class="fa fa-file me-2"></i>Reports</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('reports.daily-freight') }}">Daily Freight Report</a></li>
                    <li><a class="dropdown-item" href="{{ route('reports.trips') }}">Trips Report</a></li>
                    <li><a class="dropdown-item" href="{{ route('reports.trucks') }}">Trucks Report</a></li>
                    <li><a class="dropdown-item" href="{{ route('reports.drivers') }}">Drivers Report</a></li>
                    <li><a class="dropdown-item" href="{{ route('reports.parties') }}">Parties Report</a></li>
                </ul>
            </div>
            <a href="{{ route('profile.index') }}" class="nav-item nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"><i class="fa fa-id-card me-2"></i>Profile</a>
        </div>

        <div class="mt-5 text-center ms-3">
            <h4>N. B. Logistics</h4>
            <label><i class="bi bi-shield-fill-check text-success me-1"></i>100% Safe & Secure</label>
        </div>
    </nav>
</div>
<!-- Sidebar End -->
