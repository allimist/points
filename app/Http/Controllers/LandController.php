<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class LandController extends Controller
{

    /**
     * Update the user's profile information.
     */
    public function go()
    {
        $user_id = Auth::id();
        if(empty($user_id)){
            echo 'User not logged in';
            die;
        }

        $land_id = \request('id');
//        echo 'resource id: '.$resource_id.'<br>';
//        echo 'user id: '. $user_id.'<br>';

        // -- energy
        $currency_id = 1;
        $balance = \App\Models\Balance::where('user_id', $user_id)->where('currency_id', $currency_id)->first();
        $balance->value = $balance->value - 1;
        $balance->save();

        //location
//        $currency_id = 6;
//        $balance = \App\Models\Balance::where('user_id', $user_id)->where('currency_id', $currency_id)->first();
//        $balance->value = (int)$resource_id;
//        $balance->save();
//        echo 'moved '.$resource_id;


        Auth::user()->land_id = $land_id;
        Auth::user()->posx = 50;
        Auth::user()->posy = 50;
        Auth::user()->save();


        $msg = 'Moved to location '.$land_id;

        \Alert::add('info', $msg);
        return Redirect::route('dashboard')->with('status', $msg);
    }

    public function goPosition()
    {
        $user_id = Auth::id();
        if(empty($user_id)){
            echo 'User not logged in';
            die;
        }

        Auth::user()->posx = \request('x');
        Auth::user()->posy = \request('y');
        Auth::user()->active_at = now();
        Auth::user()->save();

    }


}
