<?php

namespace App\Http\Controllers;

use App\Exports\LaravelCmsUsersExport;
use App\Imports\LaravelCmsUsersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelDemoController extends Controller
{
    /**
     * Show demo page with import/export forms
     */
    public function index()
    {
        return view('excel-demo');
    }

    /**
     * Export Laravel CMS Users to Excel
     */
    public function export()
    {
        return Excel::download(new LaravelCmsUsersExport, 'laravel_cms_users_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    /**
     * Import Laravel CMS Users from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:2048',
        ]);

        try {
            Excel::import(new LaravelCmsUsersImport, $request->file('file'));
            
            return back()->with('success', 'Data Laravel CMS Users berhasil diimport!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            
            foreach ($failures as $failure) {
                $errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            
            return back()->with('error', 'Import gagal: ' . implode('<br>', $errors));
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel for import
     */
    public function downloadTemplate()
    {
        return Excel::download(new class implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            public function headings(): array
            {
                return ['name', 'email', 'password'];
            }

            public function array(): array
            {
                return [
                    ['John Doe', 'john@example.com', 'password123'],
                    ['Jane Smith', 'jane@example.com', 'password123'],
                ];
            }
        }, 'laravel_cms_users_template.xlsx');
    }
}
