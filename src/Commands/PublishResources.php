<?php

namespace FilamentSentry\Commands;

use Illuminate\Console\Command;

class PublishResources extends Command
{
    // use Concerns\CanManipulateFiles;
    // use Concerns\CanBackupAFile;

    public $signature = 'sentry:publish {--fresh}';

    public $description = "Publishes Filament Sentry's User Resources into your own app.";

    public function handle(): int
    {
        $this->alert('This is an Alert');
        $this->info('This is an Info');
        $this->error('This is an Error');
        $this->line('This is a Line');
        $this->table(
            ['Name', 'Email'],
            \App\Models\User::all(['name', 'email'])->toArray()
        );
        return self::SUCCESS;

        $this->alert('Following operations will be performed:');
        $this->info('- Publishes core package config');
        $this->info('- Publishes core package migration');
        $this->warn('  - On fresh applications database will be migrated');
        $this->warn('  - You can also force this behavior by supplying the --fresh option');
        $this->info('- Discovers filament resources and generates Permissions and Policies accordingly');
        $this->info('- Publishes Resources & Pages');

        $confirmed = $this->confirm('Do you wish to continue?', true);

        if ($this->CheckIfAlreadyInstalled() && !$this->option('fresh')) {
            $this->comment('Seems you have already installed the Core package!');
            $this->comment('You should run `trov:install --fresh` instead to refresh the Core package tables and setup Trov.');

            if ($this->confirm('Run `trov:install --fresh` instead?', false)) {
                $this->install(true);
            }

            return self::INVALID;
        }

        if ($confirmed) {
            $this->install($this->option('fresh'));
        } else {
            $this->comment('`trov:install` command was cancelled.');
        }

        return self::SUCCESS;
    }
}
