@extends('admin.layouts.master')

@section('title', 'Bilty List')
@section('page_title', 'Bilty List')

@section('content')
    <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-2">
        <a href="#" class="navbar-brand d-flex me-4">
            <h1 class="mb-0 fs-5">Bilty List</h1>
        </a>
        <a href="{{ route('trips.index') }}" class="btn btn-outline-secondary ms-auto">
            <i class="bi bi-arrow-left me-1"></i>Back to Trips
        </a>
    </nav>

    <div class="container-fluid pt-4 px-4">
        <div class="bg-light rounded-top p-4 shadow-sm">
            <livewire:admin.bilty.bilty-list />
        </div>
    </div>
@endsection
