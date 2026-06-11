@extends('admin.layouts.master')

@section('title', 'Driver Salary')
@section('page_title', 'Driver Salary Management')

@section('content')

    <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-2">
        <a href="#" class="navbar-brand d-flex me-4">
            <h1 class="mb-0 fs-5">Driver Salary Management</h1>
        </a>
        <a href="{{ route('drivers.index') }}" class="btn btn-outline-secondary ms-auto">
            <i class="bi bi-arrow-left"></i> Back to Drivers
        </a>
    </nav>
    
    <div class="container-fluid pt-4 px-4">
        <div class="bg-light rounded-top p-4 shadow-sm">
            <!-- Salary Livewire Component -->
            <livewire:admin.driver.salary />
        </div>
    </div>

@endsection
