<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

    public function boot()
    {
        Validator::extend('current_email', function ($attribute, $value, $parameters, $validator) {
            return $value === \Illuminate\Support\Facades\Auth::user()->email;
        });
    
        Validator::replacer('current_email', function ($message, $attribute, $rule, $parameters) {
            return 'Email saat ini tidak valid.';
        });
    }

    
}
