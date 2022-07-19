# Filament Sentry

A basic auth scaffolding for Filament utilizing Filament Breezy, Filament Shield and custom User Resources.

## Installation

Install the package via composer

```bash
composer require awcodes/filament-sentry
```

## Filament Breezy

Publish config file. This will publish Sentry's version of Breezy's config with stronger default password rules. You are free to modify this however you see fit.

```bash
php artisan vendor:publish --tag=filament-sentry-config
```

## Filament Shield
Install Shield

```bash
php artisan vendor:publish --tag=filament-shield-migrations
php artisan vendor:publish --tag=filament-shield-seeder
```

Open the `Database\Seeders\ShieldSettingSeeder.php` file and update the $settingKeys as needed.

```bash
php artisan migrate
php artisan db:seed --class=ShieldSettingSeeder
```

Add the Spatie\Permission\Traits\HasRoles trait to your User model(s):

```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    // ...
}
```

## Seeder Reference (Optional)

In `database/seeders/DatabaseSeeder.php` or where appropriate:

```php

$this->call(ShieldSettingSeeder::class);
Artisan::call('shield:generate');

\App\Models\User::factory()->create([
    'name' => 'Tony Stark',
    'email' => 'i.am@ironman.com',
])->assignRole('super_admin');
```