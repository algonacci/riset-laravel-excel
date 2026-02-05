<?php

namespace App\Exports;

use App\Models\LaravelCmsUser;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaravelCmsUsersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;

    public function query()
    {
        return LaravelCmsUser::query()->select('id', 'name', 'email', 'created_at', 'deleted_at');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama',
            'Email',
            'Tanggal Dibuat',
            'Tanggal Dihapus',
        ];
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

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
