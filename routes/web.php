<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerformaProdukController;
use App\Http\Controllers\ComparePerformaController;
use App\Http\Controllers\CompareSalesController;
use App\Http\Controllers\KategoriProdukController;

Route::get('/', function () {
    return view('index');
});

Route::group(['prefix' => 'performa-produk'], function () {
    Route::get('/', [PerformaProdukController::class, 'index'])->name('performa_produk.index');
    Route::get('/kategori', [KategoriProdukController::class, 'index'])->name('performa_produk.kategori');
    Route::get('/kategori/get-product-codes/{id}', [KategoriProdukController::class, 'getProductCodes'])->name('performa_produk.getProductCodes');
    Route::post('/kategori/add', [KategoriProdukController::class, 'store'])->name('performa_produk.createKategori');
    Route::post('/kategori/add-product', [KategoriProdukController::class, 'storeProduct'])->name('performa_produk.createProductCode');
    Route::delete('/kategori/delete-product-code/{id}', [KategoriProdukController::class, 'deleteProductCode'])->name('performa_produk.deleteProductCode');
    Route::get('/kategori/get', [KategoriProdukController::class, 'lists'])->name('performa_produk.lists');
    Route::post('/kategori/detail/import-csv', [KategoriProdukController::class, 'importCsv'])->name('performa_produk.importCsv');
    Route::post('/kategori/detail/delete/{id}', [KategoriProdukController::class, 'destroyAll'])->name('performa_produk.destroyall');
    Route::get('/kategori/detail/{id}', [KategoriProdukController::class, 'show'])->name('performa_produk.showCategori');
    Route::delete('/kategori/delete/{id}', [KategoriProdukController::class, 'destroy'])->name('performa_produk.destroyKategori');
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
    Route::get('/compare/detail/{kode_produk}', [ComparePerformaController::class, 'show'])->name('comparePerforma.importTwo');
    Route::post('/compare/detail/getDataTable', [ComparePerformaController::class, 'getDataTable']);
    Route::get('/compare/detail/getDataTable/{kode_produk}', [ComparePerformaController::class, 'show'])->name('comparePerforma.importTwo');
    Route::get('/compare-sales', [CompareSalesController::class, 'index'])->name('compareSales.index');
    Route::post('/compare-sales/import', [CompareSalesController::class, 'import'])->name('compareSales.import');
    Route::post('/compare-sales/reset', [CompareSalesController::class, 'reset'])->name('compareSales.reset');
    Route::post('/compare-sales/chart', [CompareSalesController::class, 'chart'])->name('compareSales.chart');
    Route::post('/compare-sales/top-sales', [CompareSalesController::class, 'getTop10Sales'])->name('compareSales.top-sales');
    Route::get('/compare-sales/kategori', [CompareSalesController::class, 'kategori'])->name('compareSales.kategori');
    Route::get('/compare-sales/kategori/{id}', [CompareSalesController::class, 'show'])->name('compareSales.show');
});
