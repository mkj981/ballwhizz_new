<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('view-profile', fn ($admin) => true);
        Gate::define('manage-blog', fn ($admin) => $admin->role === 'super_admin');
        Gate::define('manage-pages', fn ($admin) => in_array($admin->role, ['super_admin', 'editor']));
    }
}
