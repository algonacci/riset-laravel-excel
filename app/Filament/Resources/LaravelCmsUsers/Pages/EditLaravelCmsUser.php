<?php

namespace App\Filament\Resources\LaravelCmsUsers\Pages;

use App\Filament\Resources\LaravelCmsUsers\LaravelCmsUserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLaravelCmsUser extends EditRecord
{
    protected static string $resource = LaravelCmsUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
