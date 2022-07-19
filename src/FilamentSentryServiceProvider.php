<?php

namespace FilamentSentry;

use Livewire\Livewire;
use Illuminate\View\View;
use Filament\Facades\Filament;
use Filament\PluginServiceProvider;
use FilamentSentry\Resources\UserResource;
use Spatie\LaravelPackageTools\Package;

class FilamentSentryServiceProvider extends PluginServiceProvider
{
    protected array $resources = [
        UserResource::class,
    ];

    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-sentry')
            ->hasConfigFile(['filament-breezy']);
    }
}
