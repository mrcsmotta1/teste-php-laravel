<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportFile\ImportController;
use App\Http\Controllers\ImportFile\ProcessQueueController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['web'])->group(function () {
    Route::get('/', function () {
        return view('index');
    })->name('index');

    Route::get('/import/upload', [ImportController::class, 'showUploadForm'])->name('import.upload.form');
    Route::post('/import/upload', [ImportController::class, 'processUpload'])->name('import.upload.process');

    Route::get('/import/check-queue', [ProcessQueueController::class, 'checkQueue'])->name('import.check.queue');

    Route::get('/import/process-queue', [ProcessQueueController::class, 'showProcessQueueForm'])->name('import.process.queue.form');
    Route::post('/import/process-queue', [ProcessQueueController::class, 'processQueue'])->name('import.process.queue');
});
