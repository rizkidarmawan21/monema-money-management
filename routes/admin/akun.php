<?php

use App\Http\Controllers\AkunSaldoController;

Route::prefix('akun')->name('akun.')->group(function () {
    Route::controller(AkunSaldoController::class)->group(function () {
        Route::get('/', 'indexAkun')->name('index');
        Route::get('get', 'getData')->name('get-data');
        Route::post('create', 'createData')->name('create');
        Route::put('update/{id}', 'updateData')->name('update');
        Route::delete('delete/{id}', 'deleteData')->name('delete');
    });
});
