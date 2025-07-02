<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerformaProdukController;

Route::get('/', function () {
    return view('index');
});

Route::group(['prefix' => 'performa-produk'], function () {
    Route::get('/', [PerformaProdukController::class, 'index'])->name('performa_produk.index');
    Route::get('/lists', [PerformaProdukController::class, 'lists'])->name('performa_produk.lists');
    Route::post('/import', [PerformaProdukController::class, 'import'])->name('performa_produk.import');
    Route::post('/getcountdata', [PerformaProdukController::class, 'getcountdata'])->name('performa_produk.getcountdata');
    Route::post('/reset-data', [PerformaProdukController::class, 'resetData'])->name('performa_produk.resetData');
    Route::get('/lists/get-data', [PerformaProdukController::class, 'getListData'])->name('performa_produk.dataList');
});
