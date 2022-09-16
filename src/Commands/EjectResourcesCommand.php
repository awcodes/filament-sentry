<?php

namespace FilamentSentry\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class EjectResourcesCommand extends Command
{
    public $signature = 'sentry:eject-resources {--force}';

    public $description = 'Eject Filament Sentry\'s User resources into your app.';

    public $filesystem;

    public function handle()
    {
        $this->filesystem = new Filesystem();

        if ($this->checkIfCanBeEjected() && ! $this->option('force')) {
            $this->comment('Resources appear to already be ejected.');
            if (!$this->confirm('Would you like to force eject the resources?', false)) {
                $this->info('Canceling resource ejection.');
                return Command::FAILURE;
            }
        }

        $this->ejectResources();

        return Command::SUCCESS;
    }

    protected function checkIfCanBeEjected()
    {
        return $this->filesystem->exists(app_path('Filament/UserResource.php'));
    }

    protected function ejectResources()
    {
        if (! $this->filesystem->exists(config_path('filament-sentry.php'))) {
            $this->call('vendor:publish', ['--tag' => 'filament-sentry-config']);
        }

        $this->filesystem->ensureDirectoryExists(app_path('Filament/Resources'));
        $output = app_path('Filament/Resources');

        /** Copy files */
        $this->filesystem->copy(__DIR__ . '/../Resources/UserResource.php', $output . '/UserResource.php');
        $this->filesystem->copyDirectory(__DIR__ . '/../Resources/UserResource', $output . '/UserResource');

        /** Replace namespaces and update config */
        $this->replaceInFile('FilamentSentry\\Resources', 'App\\Filament\\Resources', $output . '/UserResource.php');
        $this->replaceInFile('FilamentSentry\\Resources', 'App\\Filament\\Resources', $output . '/UserResource/Pages/CreateUser.php');
        $this->replaceInFile('FilamentSentry\\Resources', 'App\\Filament\\Resources', $output . '/UserResource/Pages/EditUser.php');
        $this->replaceInFile('FilamentSentry\\Resources', 'App\\Filament\\Resources', $output . '/UserResource/Pages/ListUsers.php');
        $this->replaceInFile('\\FilamentSentry\\Resources\\UserResource::class', 'App\\Filament\\Resources\\UserResource::class', base_path('config/filament-sentry.php'));
    }

    protected function replaceInFile(string $search, string $replace, string $file)
    {
        file_put_contents(
            $file,
            str_replace($search, $replace, file_get_contents($file))
        );
    }
}
