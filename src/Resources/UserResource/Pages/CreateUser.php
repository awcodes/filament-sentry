<?php

namespace FilamentSentry\Resources\UserResource\Pages;

use FilamentSentry\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
