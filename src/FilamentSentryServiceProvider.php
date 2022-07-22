<?php

namespace FilamentSentry;

use App\Models\User;
use Livewire\Livewire;
use Illuminate\View\View;
use Filament\Facades\Filament;
use Filament\PluginServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use FilamentSentry\Observers\UserObserver;
use FilamentSentry\Resources\UserResource;

class FilamentSentryServiceProvider extends PluginServiceProvider
{
    protected array $resources = [
        UserResource::class,
    ];

    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-sentry')
            ->hasConfigFile(['filament-sentry', 'filament-breezy'])
            ->hasViews();
    }

    public function boot()
    {
        parent::boot();

        if (config('filament-sentry.email_new_users')) {
            User::observe(UserObserver::class);
        }

        if (config('filament-sentry.unguard_super_admin')) {
            Gate::before(function ($user, $ability) {
                return $user->hasRole('super_admin') ? true : null;
            });
        }
    }
}
