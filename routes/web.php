<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExcelDemoController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Excel Demo Routes
Route::get('/excel-demo', [ExcelDemoController::class, 'index'])->name('excel.index');
Route::get('/excel-export', [ExcelDemoController::class, 'export'])->name('excel.export');
Route::post('/excel-import', [ExcelDemoController::class, 'import'])->name('excel.import');
Route::get('/excel-template', [ExcelDemoController::class, 'downloadTemplate'])->name('excel.template');
