<?php

namespace App\Filament\Resources\LaravelCmsUsers\Pages;

use App\Filament\Resources\LaravelCmsUsers\LaravelCmsUserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLaravelCmsUsers extends ListRecords
{
    protected static string $resource = LaravelCmsUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
