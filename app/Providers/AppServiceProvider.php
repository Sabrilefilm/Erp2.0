<?php

namespace App\Providers;

use App\Models\Createur;
use App\Models\User;
use App\Policies\CreateurPolicy;
use App\Policies\UserPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.default');
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Createur::class, CreateurPolicy::class);
    }
}
