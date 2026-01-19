@extends('admin.layouts.master')

@section('title', 'Trucks')

@section('content')
    {{-- resources/views/admin/layouts/topbar.blade.php --}}
    <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-2">
        <a href="#" class="navbar-brand d-flex me-4">
            <h1 class="mb-0 fs-5">Trucks</h1>
        </a>
    </nav>

    <!-- Trucks Management Start -->
    <div class="container-fluid pt-4 px-4">
        <div class="bg-light rounded-top p-4 shadow-sm">
            <div class="row mb-3">
                <div class="col-sm-4 mb-3">
                    <div class="border p-3 rounded d-flex align-items-center gap-3">
                        <label>ALL TRUCKS <i class="bi bi-info-circle"></i></label>
                        <h3 class="text-primary mb-0">{{ $total_trucks }}</h3>
                    </div>
                </div>
                <div class="col-sm-4 mb-3">
                    <div class="border p-3 rounded d-flex align-items-center gap-3">
                        <label>MY TRUCKS <i class="bi bi-info-circle"></i></label>
                        <h3 class="text-success mb-0">{{ $total_self_trucks }}</h3>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="border p-3 rounded d-flex align-items-center gap-3">
                        <label>MARKET TRUCKS <i class="bi bi-info-circle"></i></label>
                        <h3 class="text-danger mb-0">{{ $total_market_trucks }}</h3>
                    </div>
                </div>
            </div>
            <livewire:admin.truck.list-trucks />
        </div>
    </div>
    <!-- Trucks Management End -->

@endsection
