<?php

namespace App\Filament\Resources\LaravelCmsUsers\Pages;

use App\Filament\Resources\LaravelCmsUsers\LaravelCmsUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLaravelCmsUser extends CreateRecord
{
    protected static string $resource = LaravelCmsUserResource::class;
}
