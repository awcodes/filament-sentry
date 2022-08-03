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
php artisan shield:install --fresh
```

Add the `Spatie\Permission\Traits\HasRoles` or `BezhanSalleh\FilamentShield\Traits\HasFilamentShield` trait to your User model(s):

```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use BezhanSalleh\FilamentShield\Traits\HasFilamentShield;

class User extends Authenticatable
{
    use HasFilamentShield; // or HasRoles

    // ...
}
```

## Emailing new users with password reset link

Filament Sentry has the ability to email new users with a password reset link so they can make their password more accessible to them. This really should only be needed if you have disabled registration for your app.

This functionality can be turned on and off in the config file. Use the 'noreply' setting to set the email address that will be used as the sender of the email sent to new users.

```php
    'noreply' => 'example@example.com',
    'email_new_users' => true
```

## Unguarding Super Admin

Filament Sentry unguards users with the super_admin role allowing them to bypass policies and have full control over the system. If you do not want super_admins to have this priviledge you can disable it in the config.

```php
    'unguard_super_admin' => false,
```

## Seeder Reference (Optional)

In `database/seeders/DatabaseSeeder.php` or where appropriate:

```php
/** Additial roles */
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Artisan;

$admin = Role::create(['name' => 'admin'])
    ->givePermissionTo(Permission::where('name', 'not like', '%_role')->get());
$editor = Role::create(['name' => 'editor'])
    ->givePermissionTo(Permission::where('name', 'not like', '%_role')->where('name', 'not like', '%_user')->get());

Artisan::call('shield:generate');

\App\Models\User::withoutEvents(function() {
    \App\Models\User::factory()->create([
        'name' => 'Tony Stark',
        'email' => 'i.am@ironman.com',
    ])->assignRole('super_admin');
});

\App\Models\User::withoutEvents(function() {
    \App\Models\User::factory()->create([
        'name' => 'Pepper Pots',
        'email' => 'pepper.pots@ironman.com',
    ])->assignRole('admin');
});
```