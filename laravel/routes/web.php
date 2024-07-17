<?php

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('image-upload', [BaseController::class, 'index'])->name('image.create');
Route::post('image-upload', [BaseController::class, 'store'])->name('image.store');
Route::delete('image-upload/{id}', [BaseController::class, 'deleteImage'])->name('image.delete');

Route::get('image-upload/processing/{id}/{data}', [BaseController::class, 'loading'])->name('loading');
Route::get('image-show/{id}', [BaseController::class, 'show'])->name('image.show');
