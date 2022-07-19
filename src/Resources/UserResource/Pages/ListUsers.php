<?php

namespace FilamentSentry\Resources\UserResource\Pages;

use FilamentSentry\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;
}
