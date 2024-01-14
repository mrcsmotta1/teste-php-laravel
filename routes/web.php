<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportFile\ImportController;

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

Route::get('/', function () {
    return view('index');
})->name('index');

Route::get('/import/upload', [ImportController::class, 'showUploadForm'])->name('import.upload.form');
Route::post('/import/upload', [ImportController::class, 'processUpload'])->name('import.upload.process');
