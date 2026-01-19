<?php

namespace App\Providers;


use App\Helpers\ApplicationHelper;
use App\Helpers\BiltyHelper;
use App\Helpers\CloudDocStorage;
use App\Helpers\CommonHelper;
use App\Helpers\DistributorHelper;
use App\Helpers\DriverHelper;
use App\Helpers\ExpenseHelper;
use App\Helpers\InvoiceHelper;
use App\Helpers\PartyHelper;
use App\Helpers\ProfileHelper;
use App\Helpers\ReportHelper;
use App\Helpers\ResignationHelper;
use App\Helpers\TripHelper;
use App\Helpers\TruckHelper;
use App\Helpers\UserHelper;
use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->app->singleton('common-helper', CommonHelper::class);
        $this->app->singleton('bilty-helper', BiltyHelper::class);
        $this->app->singleton('driver-helper', DriverHelper::class);
        $this->app->singleton('expense-helper', ExpenseHelper::class);
        $this->app->singleton('invoice-helper', InvoiceHelper::class);
        $this->app->singleton('party-helper', PartyHelper::class);
        $this->app->singleton('profile-helper', ProfileHelper::class);
        $this->app->singleton('report-helper', ReportHelper::class);
        $this->app->singleton('trip-helper', TripHelper::class);
        $this->app->singleton('truck-helper', TruckHelper::class);
        // $this->app->singleton('user-helper', UserHelper::class);
    }
}
