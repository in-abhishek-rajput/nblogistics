@extends('admin.layouts.master')

@section('title', 'Trip Documents')
@section('page_title', 'Trip Documents')

@section('content')

    <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-2">
        <a href="{{ route('trips.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Back to Trips
        </a>
        <a href="#" class="navbar-brand d-flex me-4">
            <h1 class="mb-0 fs-5">Trip Documents — #{{ $tripId }}</h1>
        </a>
    </nav>

    <div class="container-fluid pt-4 px-4">
        <div class="bg-light rounded-top p-4 shadow-sm">
            <livewire:admin.trip.document-wizard :tripId="$tripId" :step="$step" />
        </div>
    </div>

@endsection
