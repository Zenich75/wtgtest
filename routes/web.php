<?php

use App\Http\Controllers\Viewer\ImportController;
use App\Http\Controllers\Viewer\OfferController;
use App\Http\Controllers\Viewer\PropertyController;
use App\Http\Controllers\Viewer\ReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('viewer')->name('viewer.')->group(function () {
    Route::get('/imports', [ImportController::class, 'index'])->name('imports.index');
    Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
    Route::get('/offers', [OfferController::class, 'index'])->name('offers.index');
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
});
