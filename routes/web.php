<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandController;
//use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ServiceUseController;
use App\Http\Controllers\WalletController;

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

Route::get('/map', function () {
    return view('map');
})->middleware(['auth', 'verified'])->name('map');



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
Route::get('/land/portal',          [LandController::class,  'portal']);
//Route::get('/land/select',          [LandController::class,  'select']);
Route::get('/position/go',          [LandController::class,  'goPosition']);//hero go


//Route::get('/play',                 [PlayController::class,  'play']);
Route::get('/play', function () {
    return view('play');
})->middleware(['auth', 'verified'])->name('play');

//Route::get('/land/list', 'LandController@list');
//Route::get('/api/play/load', [PlayController::class,  'apiLoad']);
Route::get('/api/land/list', [LandController::class,  'apiList']);
Route::get('/api/land/select', [LandController::class,  'apiSelectLand']);
Route::post('/api/land/grid/save', [LandController::class,  'apiGridSave']);

Route::get('/api/service-use/select',    [ServiceUseController::class,  'ApiSelect']);
Route::get('/api/service-use/claim',    [ServiceUseController::class,  'ApiClaim']);
Route::get('/api/service-use/sell',     [ServiceUseController::class,  'ApiSell']);
Route::get('/api/service-use/orders',   [ServiceUseController::class, 'apiOrders']);
Route::get('/api/service-use/buy',      [ServiceUseController::class,  'apiBuy']);

Route::get('/api/farm/attack',          [ServiceUseController::class,  'apiFarmAttack']);

//Route::get('/api/service-use/select-order',   [ServiceUseController::class, 'ApiSelectOrder']);


Route::get('/farm/move',         [LandController::class,  'moveFarm']);
//for admin not limmited amount
Route::get('/farm/add',          [LandController::class,  'addFarm']);
//for player decrease balance
Route::get('/farm/set',          [LandController::class,  'setFarm']);
Route::get('/farm/pick',         [LandController::class,  'pickFarm']);




Route::get('/wallet', function () {
    return view('wallet');
})->middleware(['auth', 'verified'])->name('wallet');

Route::get('/api/wallet/add', [WalletController::class,  'add']);
Route::post('/api/wallet/add', [WalletController::class,  'add']);
Route::post('/wallet/withdraw', [WalletController::class,  'withdraw']);
Route::post('/wallet/deposit', [WalletController::class,  'deposit']);

//Route::get('/transactions', function () {
//    return view('transactions');
//})->middleware(['auth', 'verified'])->name('transactions');




Route::crud('admin/setting', 'App\Http\Controllers\Admin\SettingCrudController');





require __DIR__.'/auth.php';
