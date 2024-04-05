<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandController;
//use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ServiceUseController;

use App\Http\Controllers\SSEController;

// Add this inside the routes file
Route::get('/stream', [SSEController::class, 'streamData']);


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
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Route::get('/resource/claim',      [ResourceController::class,  'claim']);
Route::get('/service-use/claim',    [ServiceUseController::class,  'claim']);
Route::get('/service-use/select',   [ServiceUseController::class,  'select']);
Route::get('/service-use/sell',     [ServiceUseController::class,  'sell']);
Route::get('/service-use/orders',   [ServiceUseController::class, 'orders']);
Route::get('/service-use/select-order',   [ServiceUseController::class, 'selectOrder']);
Route::get('/service-use/buy',      [ServiceUseController::class,  'buy']);

Route::get('/land/go',              [LandController::class,  'go']);
Route::get('/position/go',          [LandController::class,  'goPosition']);


require __DIR__.'/auth.php';
