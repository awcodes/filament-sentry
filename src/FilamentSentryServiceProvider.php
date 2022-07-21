<?php

namespace FilamentSentry;

use App\Models\User;
use Livewire\Livewire;
use Illuminate\View\View;
use Filament\Facades\Filament;
use FilamentSentry\Observers\UserObserver;
use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;
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
            ->hasCommands([
                Commands\PublishResources::class,
            ]);
    }

    public function boot()
    {
        parent::boot();

        User::observe(UserObserver::class);
    }
}
