@extends('admin.layouts.master')

@section('title', 'Parties Report')

@section('content')
    <div class="container-fluid pt-4 px-4">
        <div class="bg-light rounded-top p-4 shadow-sm">
            <livewire:admin.reports.parties-report />
        </div>
    </div>
@endsection
