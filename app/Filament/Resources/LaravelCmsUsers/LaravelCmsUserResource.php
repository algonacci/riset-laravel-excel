<?php

namespace App\Filament\Resources\LaravelCmsUsers;

use App\Filament\Resources\LaravelCmsUsers\Pages\CreateLaravelCmsUser;
use App\Filament\Resources\LaravelCmsUsers\Pages\EditLaravelCmsUser;
use App\Filament\Resources\LaravelCmsUsers\Pages\ListLaravelCmsUsers;
use App\Filament\Resources\LaravelCmsUsers\Pages\ViewLaravelCmsUser;
use App\Filament\Resources\LaravelCmsUsers\Schemas\LaravelCmsUserForm;
use App\Filament\Resources\LaravelCmsUsers\Schemas\LaravelCmsUserInfolist;
use App\Filament\Resources\LaravelCmsUsers\Tables\LaravelCmsUsersTable;
use App\Models\LaravelCmsUser;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LaravelCmsUserResource extends Resource
{
    protected static ?string $model = LaravelCmsUser::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'CMS Users';

    protected static ?string $pluralModelLabel = 'CMS Users';

    public static function form(Schema $schema): Schema
    {
        return LaravelCmsUserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LaravelCmsUserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LaravelCmsUsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLaravelCmsUsers::route('/'),
            'create' => CreateLaravelCmsUser::route('/create'),
            'view' => ViewLaravelCmsUser::route('/{record}'),
            'edit' => EditLaravelCmsUser::route('/{record}/edit'),
        ];
    }
}
