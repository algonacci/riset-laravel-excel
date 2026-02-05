<?php

namespace App\Filament\Resources\LaravelCmsUsers\Tables;

use App\Exports\LaravelCmsUsersExport;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;

class LaravelCmsUsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('export')
                        ->label('Export Selected')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function ($records) {
                            $recordIds = $records->pluck('id')->toArray();
                            
                            return Excel::download(
                                new class($recordIds) implements \Maatwebsite\Excel\Concerns\FromQuery, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithMapping, \Maatwebsite\Excel\Concerns\ShouldAutoSize, \Maatwebsite\Excel\Concerns\WithStyles {
                                    use \Maatwebsite\Excel\Concerns\Exportable;
                                    
                                    protected $recordIds;
                                    
                                    public function __construct($recordIds)
                                    {
                                        $this->recordIds = $recordIds;
                                    }
                                    
                                    public function query()
                                    {
                                        return \App\Models\LaravelCmsUser::query()
                                            ->whereIn('id', $this->recordIds)
                                            ->select('id', 'name', 'email', 'created_at', 'deleted_at');
                                    }
                                    
                                    public function headings(): array
                                    {
                                        return ['ID', 'Nama', 'Email', 'Tanggal Dibuat', 'Tanggal Dihapus'];
                                    }
                                    
                                    public function map($user): array
                                    {
                                        return [
                                            $user->id,
                                            $user->name,
                                            $user->email,
                                            $user->created_at ? $user->created_at->format('d-m-Y H:i:s') : '-',
                                            $user->deleted_at ? $user->deleted_at->format('d-m-Y H:i:s') : '-',
                                        ];
                                    }
                                    
                                    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
                                    {
                                        return [
                                            1 => ['font' => ['bold' => true, 'size' => 12]],
                                        ];
                                    }
                                },
                                'laravel_cms_users_selected_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
                            );
                        }),
                ]),
            ]);
    }
}
