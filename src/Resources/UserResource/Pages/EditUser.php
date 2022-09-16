<?php

namespace FilamentSentry\Resources\UserResource\Pages;

use FilamentSentry\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public Collection $permissions;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $nonPermissionsFilter = ['name', 'email', 'password'];

        $this->permissions = collect($data)->filter(function ($permission, $key) use ($nonPermissionsFilter) {
            return ! in_array($key, $nonPermissionsFilter) && Str::contains($key, '_');
        })->keys();

        return Arr::only($data, $nonPermissionsFilter);
    }

    protected function afterSave(): void
    {
        $permissionModels = collect();
        $this->permissions->each(function ($permission) use ($permissionModels) {
            $permissionModels->push(Permission::firstOrCreate(
                ['name' => $permission],
            ));
        });

        $this->record->syncPermissions($permissionModels);
    }
}
