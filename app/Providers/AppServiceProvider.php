<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\FinancialYears;
use App\Models\AppointmentQueries;
use App\Models\Locations;

class AppServiceProvider extends ServiceProvider
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
        $years = FinancialYears::all();
        $qcount = AppointmentQueries::where('status_id', 1)->get()->count();
        $locations = Locations::where('status_id', 2)->get();
        View::share(\compact('years', 'qcount', 'locations'));
    }
}
