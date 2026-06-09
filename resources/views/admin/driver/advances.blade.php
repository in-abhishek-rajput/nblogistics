@extends('admin.layouts.master')

@section('title', 'Advance History')
@section('page_title', 'Advance History')

@section('content')

    <div class="container-fluid pt-4 px-4">
        <livewire:admin.driver.advance-history :driver="$driverId" />
    </div>

@endsection
