<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandController;
//use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ServiceUseController;

use App\Http\Controllers\SSEController;

use App\Http\Controllers\PlayController;
use App\Http\Controllers\Admin\SettingCrudController;




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

Route::get('/avatar', function () {
    return view('avatar');
})->middleware(['auth', 'verified'])->name('avatar');



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
//Route::get('/api/play/load', [PlayController::class,  'apiLoad']);
Route::get('/api/land/list', [LandController::class,  'apiList']);
Route::post('/api/land/grid/save', [LandController::class,  'apiGridSave']);

Route::get('/api/service-use/select',    [ServiceUseController::class,  'ApiSelect']);
Route::get('/api/service-use/claim',    [ServiceUseController::class,  'ApiClaim']);
Route::get('/api/service-use/sell',     [ServiceUseController::class,  'ApiSell']);


Route::get('/farm/move',         [LandController::class,  'moveFarm']);
//for admin not limmited amount
Route::get('/farm/add',          [LandController::class,  'addFarm']);
//for player decrease balance
Route::get('/farm/set',          [LandController::class,  'setFarm']);
Route::get('/farm/pick',         [LandController::class,  'pickFarm']);

Route::crud('admin/setting', 'App\Http\Controllers\Admin\SettingCrudController');





require __DIR__.'/auth.php';
