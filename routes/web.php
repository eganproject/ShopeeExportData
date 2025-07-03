<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerformaProdukController;
use App\Http\Controllers\ComparePerformaController;

Route::get('/', function () {
    return view('index');
});

Route::group(['prefix' => 'performa-produk'], function () {
    Route::get('/', [PerformaProdukController::class, 'index'])->name('performa_produk.index');
    Route::post('/import', [PerformaProdukController::class, 'import'])->name('performa_produk.import');
    Route::post('/getcountdata', [PerformaProdukController::class, 'getcountdata'])->name('performa_produk.getcountdata');
    Route::post('/reset-data', [PerformaProdukController::class, 'resetData'])->name('performa_produk.resetData');
    Route::post('/get-performa-produk', [PerformaProdukController::class, 'getPerformaProduk'])->name('performa_produk.getPerformaProduk');
    Route::get('/lists', [PerformaProdukController::class, 'lists'])->name('performa_produk.lists');
    Route::get('/lists/get-data', [PerformaProdukController::class, 'getListData'])->name('performa_produk.dataList');
    Route::get('/compare', [ComparePerformaController::class, 'index'])->name('comparePerforma.index');
    Route::post('/compare/reset-data', [ComparePerformaController::class, 'resetData'])->name('comparePerforma.resetData');
    Route::post('/compare/getcountdata', [ComparePerformaController::class, 'getCountData'])->name('comparePerforma.getCountData');
    Route::post('/compare/import_1', [ComparePerformaController::class, 'importOne'])->name('comparePerforma.importOne');
    Route::post('/compare/import_2', [ComparePerformaController::class, 'importTwo'])->name('comparePerforma.importTwo');
});
