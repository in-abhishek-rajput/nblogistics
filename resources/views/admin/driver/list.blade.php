@extends('admin.layouts.master')

@section('title', 'Drivers')
@section('page_title', 'Drivers')

@section('content')

    {{-- resources/views/admin/layouts/topbar.blade.php --}}
    <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-2">
        <a href="#" class="navbar-brand d-flex me-4">
            <h1 class="mb-0 fs-5">Drivers</h1>
        </a>
    </nav>
    <!-- Sale & Revenue Start -->
    <div class="container-fluid pt-4 px-4">
        <div class="bg-light rounded-top p-4 shadow-sm">
            <div class="row mb-3">
                <div class="col col-sm-6">
                    <div class="border p-3 rounded d-flex align-items-center gap-3">
                        <label>TOTAL DRIVER BALANCE <i class="bi bi-info-circle"></i></label>
                        <h3 class="text-primary mb-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" fill="currentColor"
                                class="bi bi-currency-rupee" viewBox="0 0 14 20">
                                <path
                                    d="M4 3.06h2.726c1.22 0 2.12.575 2.325 1.724H4v1.051h5.051C8.855 7.001 8 7.558 6.788 7.558H4v1.317L8.437 14h2.11L6.095 8.884h.855c2.316-.018 3.465-1.476 3.688-3.049H12V4.784h-1.345c-.08-.778-.357-1.335-.793-1.732H12V2H4z" />
                            </svg>
                            {{ number_format($total_opening_balance,2) }}
                        </h3>
                    </div>
                </div>
                <!-- <div class="col col-sm-4">
                                        <div class="border p-3 rounded d-flex align-items-center gap-3">
                                            <label>MARKET TRUCKS <i class="bi bi-info-circle"></i></label>
                                            <h3 class="text-success mb-0">0</h3>
                                        </div>
                                    </div> -->
            </div>

            <!-- Dynamic Drivers List -->
            <livewire:admin.driver.list-drivers />
        </div>

    </div>
    <!-- Sale & Revenue End -->
    <script>
        Livewire.on('driverAdded', () => {
            new bootstrap.Modal(document.getElementById('addDriverModal')).hide();
        });

        Livewire.on('driverUpdated', () => {
            new bootstrap.Modal(document.getElementById('editDriverModal')).hide();
        });
    </script>
@endsection
