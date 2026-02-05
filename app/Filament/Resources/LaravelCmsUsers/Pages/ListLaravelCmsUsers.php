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

            // Import Action with Modal - Stateless (Memory)
            Action::make('import')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
                ->modalHeading('Import Data Excel')
                ->modalDescription('Upload file Excel (.xlsx, .xls) untuk import data. Stateless - file diproses di memory.')
                ->modalSubmitActionLabel('Import Data')
                ->form([
                    FileUpload::make('file')
                        ->label('File Excel')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->maxSize(2048)
                        ->required()
                        ->helperText('Format: .xlsx, .xls (Max 2MB). File diproses langsung di memory tanpa disimpan ke disk.')
                        ->disk('local') // Simpan temporary di local disk
                        ->directory('temp-imports'),
                ])
                ->action(function (array $data) {
                    $filePath = $data['file'];
                    
                    try {
                        // Baca file dari storage temporary
                        $fullPath = storage_path('app/' . $filePath);
                        
                        // Import langsung dari file temporary
                        Excel::import(new LaravelCmsUsersImport, $fullPath);
                        
                        // Hapus file temporary setelah import
                        if (file_exists($fullPath)) {
                            unlink($fullPath);
                        }
                        
                        // Notifikasi sukses
                        \Filament\Notifications\Notification::make()
                            ->title('Import Berhasil')
                            ->body('Data Laravel CMS Users berhasil diimport!')
                            ->success()
                            ->send();
                            
                    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                        // Cleanup file temporary
                        $fullPath = storage_path('app/' . $filePath);
                        if (file_exists($fullPath)) {
                            unlink($fullPath);
                        }
                        
                        $failures = $e->failures();
                        $errors = [];
                        
                        foreach ($failures as $failure) {
                            $errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Import Gagal')
                            ->body(implode(', ', array_slice($errors, 0, 5))) // Max 5 errors
                            ->danger()
                            ->persistent()
                            ->send();
                            
                    } catch (\Exception $e) {
                        // Cleanup file temporary
                        $fullPath = storage_path('app/' . $filePath);
                        if (file_exists($fullPath)) {
                            unlink($fullPath);
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Import Gagal')
                            ->body('Terjadi kesalahan: ' . $e->getMessage())
                            ->danger()
                            ->persistent()
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
