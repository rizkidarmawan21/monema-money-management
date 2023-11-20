<?php

use App\Http\Controllers\KaryawanController;

Route::prefix('karyawan')->name('karyawan.')->group(function () {
    Route::controller(KaryawanController::class)->group(function () {
        Route::get('/', 'indexKaryawan')->name('index');
        Route::get('get', 'getData')->name('get-data');
        Route::post('create', 'createData')->name('create');
        Route::put('update/{id}', 'updateData')->name('update');
        Route::delete('delete/{id}', 'deleteData')->name('delete');
    });
});
