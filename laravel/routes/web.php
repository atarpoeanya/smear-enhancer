<?php

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BaseController::class, 'index'])->name('image.create');
Route::post('image-upload', [BaseController::class, 'store'])->name('image.store');
Route::delete('image-upload/{id}', [BaseController::class, 'deleteImage'])->name('image.destroy');

Route::get('image-upload/processing/{id}/{data}', [BaseController::class, 'loading'])->name('loading');
Route::get('image-show/{id}', [BaseController::class, 'show'])->name('image.show');
Route::get('download/{id}', [BaseController::class, 'downloadFile'])->name('image.download');
Route::get('download-original/{id}', [BaseController::class, 'downloadOriginalFile'])->name('image-original.download');

Route::get('updateChart', [BaseController::class, 'updateChartElement'])->name('chart.update');
Route::post('revaluateMetric', [BaseController::class, 'evaluteMetric'])->name('metric.update');

Route::get('test', [BaseController::class, 'thisIsATest'])->name('testing.show');
Route::post('test', [BaseController::class, 'pingingTest'])->name('testing.ping');
