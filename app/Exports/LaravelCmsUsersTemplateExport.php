<?php

namespace App\Exports;

use App\Models\LaravelCmsUser;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Schema;

class LaravelCmsUsersTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        // Ambil nama kolom dari tabel database secara dinamis
        $columns = Schema::getColumnListing('laravel_cms_users');
        
        // Filter kolom yang tidak perlu diisi manual (timestamp, id, dll)
        $excludedColumns = ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token'];
        
        return array_values(array_filter($columns, function($column) use ($excludedColumns) {
            return !in_array($column, $excludedColumns);
        }));
    }

    public function array(): array
    {
        // Generate sample data berdasarkan struktur tabel
        $headings = $this->headings();
        
        $sampleData = [
            $this->generateSampleRow($headings, 1),
            $this->generateSampleRow($headings, 2),
            $this->generateSampleRow($headings, 3),
        ];
        
        return $sampleData;
    }

    private function generateSampleRow(array $columns, int $index): array
    {
        $row = [];
        
        foreach ($columns as $column) {
            $row[] = $this->getSampleValue($column, $index);
        }
        
        return $row;
    }

    private function getSampleValue(string $column, int $index): string
    {
        // Generate sample value berdasarkan nama kolom
        return match($column) {
            'name' => "User Example {$index}",
            'email' => "user{$index}@example.com",
            'password' => 'password123',
            'email_verified_at' => now()->format('Y-m-d H:i:s'),
            default => "sample_{$column}_{$index}",
        };
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4B5563']],
            ],
        ];
    }
}
