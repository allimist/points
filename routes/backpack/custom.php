<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

//user role
//dd(Auth::user());
//dd(backpack_user()->hasRole('writer'));


Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('currency', 'CurrencyCrudController');
    Route::crud('balance', 'BalanceCrudController');
    Route::crud('resource', 'ResourceCrudController');
    Route::crud('land', 'LandCrudController');
    Route::crud('farm', 'FarmCrudController');
    Route::crud('service', 'ServiceCrudController');
    Route::crud('service-use', 'ServiceUseCrudController');
    Route::crud('avatar', 'AvatarCrudController');
    Route::crud('order', 'OrderCrudController');
    Route::crud('user', 'UserCrudController');
    Route::crud('userdata', 'UserdataCrudController');
    Route::crud('land-type', 'LandTypeCrudController');
//    Route::crud('setting', 'SettingCrudController');
}); // this should be the absolute last line of this file
