@extends('admin.layouts.master')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
    <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-2">
        <a href="#" class="navbar-brand d-flex me-4">
            <h1 class="mb-0 fs-5">Dashboard</h1>
        </a>
    </nav>

    <div class="container-fluid pt-4 px-4">
        {{-- Livewire Dashboard Component --}}
        <livewire:admin.dashboard />
    </div>

@endsection