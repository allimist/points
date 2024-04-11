<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandController;
//use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ServiceUseController;

use App\Http\Controllers\SSEController;

use App\Http\Controllers\PlayController;




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
Route::get('/land/select',          [LandController::class,  'select']);
Route::get('/position/go',          [LandController::class,  'goPosition']);//hero go


//Route::get('/play',                 [PlayController::class,  'play']);
Route::get('/play', function () {
    return view('play');
})->middleware(['auth', 'verified'])->name('play');

//Route::get('/land/list', 'LandController@list');
Route::get('/api/play/load', [PlayController::class,  'apiLoad']);
Route::get('/api/land/list', [LandController::class,  'apiList']);
Route::get('/api/service-use/select',    [ServiceUseController::class,  'ApiSelect']);
Route::get('/api/service-use/claim',    [ServiceUseController::class,  'ApiClaim']);

Route::get('/position/set',          [LandController::class,  'setPosition']);//save farm pos
Route::get('/farm/add',          [LandController::class,  'addFarm']);//add new farm







require __DIR__.'/auth.php';
