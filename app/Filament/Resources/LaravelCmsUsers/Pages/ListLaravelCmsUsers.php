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
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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
                ->modalDescription('Upload file Excel (.xlsx, .xls) untuk import data.')
                ->modalSubmitActionLabel('Import Data')
                ->form([
                    FileUpload::make('file')
                        ->label('File Excel')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel'
                        ])
                        ->maxSize(2048)
                        ->required()
                        ->helperText('Format: .xlsx, .xls (Max 2MB)')
                        ->disk('local')
                        ->directory('tmp'),
                ])
                ->action(function (array $data) {
                    $filePath = null;
                    
                    try {
                        // Get file path from FileUpload component
                        $filePath = is_array($data['file']) ? $data['file'][0] : $data['file'];
                        
                        // Get the full storage path
                        $storagePath = Storage::disk('local')->path($filePath);
                        
                        // Check if file exists in storage
                        if (!Storage::disk('local')->exists($filePath)) {
                            throw new \Exception('File tidak ditemukan di storage: ' . $filePath);
                        }
                        
                        // Create a temporary uploaded file for Excel import
                        $tempFile = new TemporaryUploadedFile(
                            $storagePath,
                            'local'
                        );
                        
                        // Import using Laravel Excel
                        Excel::import(new LaravelCmsUsersImport, $tempFile);
                        
                        // Delete the temporary file
                        Storage::disk('local')->delete($filePath);
                        
                        // Success notification
                        \Filament\Notifications\Notification::make()
                            ->title('Import Berhasil')
                            ->body('Data berhasil diimport!')
                            ->success()
                            ->send();
                            
                    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                        if ($filePath) {
                            Storage::disk('local')->delete($filePath);
                        }
                        
                        $errors = [];
                        foreach ($e->failures() as $failure) {
                            $errors[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Validasi Gagal')
                            ->body(implode('<br>', array_slice($errors, 0, 3)))
                            ->danger()
                            ->persistent()
                            ->send();
                            
                    } catch (\Exception $e) {
                        if ($filePath) {
                            Storage::disk('local')->delete($filePath);
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Import Gagal')
                            ->body($e->getMessage())
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
