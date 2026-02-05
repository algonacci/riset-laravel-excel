<?php

namespace App\Filament\Resources\LaravelCmsUsers\Pages;

use App\Filament\Resources\LaravelCmsUsers\LaravelCmsUserResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLaravelCmsUser extends ViewRecord
{
    protected static string $resource = LaravelCmsUserResource::class;

    public function getTitle(): string
    {
        return 'Detail CMS User';
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
