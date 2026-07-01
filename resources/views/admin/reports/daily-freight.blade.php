@extends('admin.layouts.master')

@section('title', 'Daily Freight Report')

@section('content')
    <div class="container-fluid pt-4 px-4">
        <div class="bg-light rounded-top p-4 shadow-sm">
            <livewire:admin.reports.daily-freight-report />
        </div>
    </div>
@endsection
