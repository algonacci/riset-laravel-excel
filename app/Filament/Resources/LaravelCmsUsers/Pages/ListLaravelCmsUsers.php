<?php

namespace App\Filament\Resources\LaravelCmsUsers\Pages;

use App\Exports\LaravelCmsUsersExport;
use App\Exports\LaravelCmsUsersTemplateExport;
use App\Filament\Resources\LaravelCmsUsers\LaravelCmsUserResource;
use App\Imports\LaravelCmsUsersImport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Text;
use Maatwebsite\Excel\Facades\Excel;

class ListLaravelCmsUsers extends ListRecords
{
    protected static string $resource = LaravelCmsUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),

            // Export Action
            Action::make('export')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return Excel::download(new LaravelCmsUsersExport, 'laravel_cms_users_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
                }),

            // Import Action with Modal
            Action::make('import')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
                ->modalHeading('Import Data Excel')
                ->modalDescription('Upload file Excel (.xlsx, .xls) untuk import data Laravel CMS Users.')
                ->modalSubmitActionLabel('Import Data')
                ->form([
                    FileUpload::make('file')
                        ->label('File Excel')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->maxSize(2048)
                        ->required()
                        ->helperText('Format yang didukung: .xlsx, .xls (Max 2MB)'),
                ])
                ->action(function (array $data) {
                    $file = $data['file'];
                    
                    try {
                        Excel::import(new LaravelCmsUsersImport, storage_path('app/public/' . $file));
                        
                        // Delete temporary file after import
                        unlink(storage_path('app/public/' . $file));
                        
                        // Show Filament notification
                        \Filament\Notifications\Notification::make()
                            ->title('Import Berhasil')
                            ->body('Data Laravel CMS Users berhasil diimport!')
                            ->success()
                            ->send();
                    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                        unlink(storage_path('app/public/' . $file));
                        
                        $failures = $e->failures();
                        $errors = [];
                        
                        foreach ($failures as $failure) {
                            $errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Import Gagal')
                            ->body(implode(', ', $errors))
                            ->danger()
                            ->send();
                    } catch (\Exception $e) {
                        if (file_exists(storage_path('app/public/' . $file))) {
                            unlink(storage_path('app/public/' . $file));
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Import Gagal')
                            ->body('Terjadi kesalahan: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            // Download Template Action
            Action::make('template')
                ->label('Download Template')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action(function () {
                    return Excel::download(new LaravelCmsUsersTemplateExport, 'laravel_cms_users_template.xlsx');
                }),
        ];
    }
}
