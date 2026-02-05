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
use Illuminate\Http\UploadedFile;
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

            // Import Action with Modal - TRUE STATELESS
            Action::make('import')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
                ->modalHeading('Import Data Excel')
                ->modalDescription('Upload file Excel (.xlsx, .xls) untuk import data. Stateless - file diproses langsung di memory.')
                ->modalSubmitActionLabel('Import Data')
                ->form([
                    FileUpload::make('file')
                        ->label('File Excel')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->maxSize(2048)
                        ->required()
                        ->helperText('Format: .xlsx, .xls (Max 2MB). Stateless - tidak disimpan ke disk.')
                        ->storeFiles(false), // JANGAN simpan ke disk!
                ])
                ->action(function (array $data) {
                    try {
                        // Ambil file dari state (temporary file di memory)
                        $fileState = $data['file'];
                        
                        // FileUpload dengan storeFiles(false) akan return path ke temporary file
                        if (is_array($fileState)) {
                            $fileState = $fileState[0];
                        }
                        
                        // Buat UploadedFile instance dari temporary file
                        $uploadedFile = new UploadedFile(
                            $fileState,
                            'import.xlsx',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            null,
                            true // test mode = true (file sudah valid)
                        );
                        
                        // Import langsung dari UploadedFile (stateless - tidak simpan ke disk)
                        Excel::import(new LaravelCmsUsersImport, $uploadedFile);
                        
                        // Notifikasi sukses
                        \Filament\Notifications\Notification::make()
                            ->title('Import Berhasil')
                            ->body('Data Laravel CMS Users berhasil diimport!')
                            ->success()
                            ->send();
                            
                    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                        $failures = $e->failures();
                        $errors = [];
                        
                        foreach ($failures as $failure) {
                            $errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Import Gagal')
                            ->body(implode(', ', array_slice($errors, 0, 5)))
                            ->danger()
                            ->persistent()
                            ->send();
                            
                    } catch (\Exception $e) {
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
