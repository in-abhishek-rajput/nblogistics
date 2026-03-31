@extends('admin.layouts.master')
@section('title', 'Trip Expenses')
@section('content')
    {{-- resources/views/admin/layouts/topbar.blade.php --}}
    <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-2">
        <a href="#" class="navbar-brand d-flex me-4">
            <h1 class="mb-0 fs-5">Trip Expenses</h1>
        </a>
    </nav>
    <div class="container-fluid pt-4 px-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Trip Expenses</h4>
                    </div>
                    <div class="card-body">
                        @livewire('admin.trip.list-expenses')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
