<?php

namespace App\Providers;

use App\Models\Distributors;
use App\Models\Resignations;
use App\Repositories\ApplicationRepository;
use App\Repositories\BiltyRepository;
use App\Repositories\CommonRepository;
use App\Repositories\DistributorRepository;
use App\Repositories\DriverRepository;
use App\Repositories\ExpenseRepository;
use App\Repositories\EyeReviewDocumentsRepository;
use App\Repositories\EyeReviewDetailsRepository;
use App\Repositories\EyeReviewRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\PartyRepository;
use App\Repositories\PatientDetailsRepository;
use App\Repositories\ReportRepository;
use App\Repositories\ResignationRepository;
use App\Repositories\RoleRepository;
use App\Repositories\StentRegistryRepository;
use App\Repositories\TripRepository;
use App\Repositories\TruckRepository;
use App\Repositories\TrustRepository;
use App\Repositories\UserRepository;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->app->singleton('role-repo', RoleRepository::class);
        $this->app->bind('bilty-repo', BiltyRepository::class);
        $this->app->bind('driver-repo', DriverRepository::class);
        $this->app->bind('expense-repo', ExpenseRepository::class);
        $this->app->bind('invoice-repo', InvoiceRepository::class);
        $this->app->bind('party-repo', PartyRepository::class);
        $this->app->bind('report-repo', ReportRepository::class);
        $this->app->bind('trip-repo', TripRepository::class);
        $this->app->bind('truck-repo', TruckRepository::class);
    }
}
