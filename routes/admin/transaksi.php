<?php

use App\Http\Controllers\PengeluaranController;

Route::prefix('transaksi')->name('transaksi.')->group(function () {

    Route::controller(PengeluaranController::class)->prefix('keluar')->name('keluar.')->group(function () {
        Route::get('/', 'indexPengeluaran')->name('index');
        Route::get('get', 'getData')->name('get-data');
        Route::post('create', 'createData')->name('create');
        Route::put('update/{id}', 'updateData')->name('update');
        Route::delete('delete/{id}', 'deleteData')->name('delete');
    });
});
