@extends('admin.layouts.master')

@section('title', 'Trips')

@section('content')
    {{-- resources/views/admin/layouts/topbar.blade.php --}}
    <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-2">
        <a href="#" class="navbar-brand d-flex me-4">
            <h1 class="mb-0 fs-5">Trips</h1>
        </a>
    </nav>

    <!-- Trips Management Start -->
    <div class="container-fluid pt-4 px-4">
        <div class="bg-light rounded-top p-4 shadow-sm">
            <div class="row mb-3">
                <div class="col-sm-3 mb-3">
                    <div class="border p-3 rounded d-flex align-items-center gap-3">
                        <label>ALL TRIPS <i class="bi bi-info-circle"></i></label>
                        <h3 class="text-primary mb-0">{{ $total_trips }}</h3>
                    </div>
                </div>
                <div class="col-sm-3 mb-3">
                    <div class="border p-3 rounded d-flex align-items-center gap-3">
                        <label>PENDING <i class="bi bi-info-circle"></i></label>
                        <h3 class="text-warning mb-0">{{ $pending_trips }}</h3>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="border p-3 rounded d-flex align-items-center gap-3">
                        <label>ONGOING <i class="bi bi-info-circle"></i></label>
                        <h3 class="text-info mb-0">{{ $ongoing_trips }}</h3>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="border p-3 rounded d-flex align-items-center gap-3">
                        <label>COMPLETED <i class="bi bi-info-circle"></i></label>
                        <h3 class="text-success mb-0">{{ $completed_trips }}</h3>
                    </div>
                </div>
            </div>
            <livewire:admin.trip.list-trips />
        </div>
    </div>
    <!-- Trips Management End -->

@endsection
