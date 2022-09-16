# Filament Sentry

A basic auth scaffolding for Filament utilizing [Filament Breezy](https://github.com/jeffgreco13/filament-breezy), [Filament Shield](https://github.com/bezhanSalleh/filament-shield) and custom User Resources.

## Installation

Install the package via composer

```bash
composer require awcodes/filament-sentry
```

Publish config files.

This will publish Sentry's config and Sentry's version of Breezy's config with stronger default password rules. You are free to modify this however you see fit.

```bash
php artisan vendor:publish --tag=filament-sentry-config
```

Add the `HasFilamentShield` trait to your User model(s):

```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use BezhanSalleh\FilamentShield\Traits\HasFilamentShield;

class User extends Authenticatable
{
    use HasFilamentShield; // or HasRoles

    // ...
}
```

Install Shield

```bash
php artisan shield:install --fresh
```

## Emailing new users with password reset link

Filament Sentry has the ability to email new users with a password reset link when they are created so they can make their password more accessible to them. *This really should only be needed if you have disabled registration for your app.*

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

## Modifying the User Resource

To modify the User Resource you will need to eject the resource and pages into your app. This will publish the resource / pages to `app/Filament/Resources` and update the `user_resource` setting in Sentry's config file.

```bash
php artisan sentry:eject-resources
```

> **Note**
> Any additional fields added to the user will need to be added to the $nonPermissionsFilter in both the CreateUser and EditUser resource pages.

```php
// CreateUser.php
protected function mutateFormDataBeforeCreate(array $data): array
{
    $nonPermissionsFilter = ['name', 'email', 'password', 'bio', 'etc'];
    ...
}

// EditUser.php
protected function mutateFormDataBeforeSave(array $data): array
{
    $nonPermissionsFilter = ['name', 'email', 'password', 'bio', 'etc'];
    ...
}
```

## Seeder Reference (Optional)

In `database/seeders/DatabaseSeeder.php` or where appropriate:

```php
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;

$admin = Role::create(['name' => 'admin'])
    ->givePermissionTo(Permission::where('name', 'not like', '%_role')->get());
$editor = Role::create(['name' => 'editor'])
    ->givePermissionTo(Permission::where('name', 'not like', '%_role')->where('name', 'not like', '%_user')->get());

Artisan::call('shield:generate');

User::withoutEvents(function() {
    User::factory()->create([
        'name' => 'Tony Stark',
        'email' => 'i.am@ironman.com',
    ])->assignRole('super_admin');

    User::factory()->create([
        'name' => 'Pepper Pots',
        'email' => 'pepper.pots@ironman.com',
    ])->assignRole('admin');
});
```