@extends('admin.layouts.master')

@section('title', 'Salary Details')
@section('page_title', 'Salary Details')

@section('content')

    <div class="container-fluid pt-4 px-4">
        <livewire:admin.driver.salary-details :driver="$driverId" />
    </div>

@endsection
