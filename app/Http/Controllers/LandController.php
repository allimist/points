<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Currency;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use Illuminate\Http\Response;


class LandController extends Controller
{

    /*
    public function select()
    {
        $user_id = Auth::id();
        if(empty($user_id)){
            echo 'User not logged in';
            die;
        }


        $lands = \App\Models\Land::select('id','name')->get();
        foreach ($lands as $land) {
            $landArray[$land->id] = $land->name;
        }


        foreach ($lands as $land) {
            if($land->id == Auth::user()->land_id) {
                echo $land->name.' - ['.$land->id.']<br>';
            } else {
                echo '<a href="/land/go?id='.$land->id.'">'.$land->name.' - ['.$land->id.'] </a><br>';
            }
        }

        //back
        echo '<a href="/dashboard">Back</a>';
    }
*/

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
        Auth::user()->posx = 930;
        Auth::user()->posy = 350;
        Auth::user()->save();


//        $msg = 'Moved to location '.$land_id;
//        \Alert::add('info', $msg);
//        return Redirect::route('dashboard')->with('status', $msg);
        //return Redirect::controller('App\Http\Controllers\PlayController@play');
        return Redirect::route('play');
    }
    public function portal()
    {
        $user_id = Auth::id();
        if(empty($user_id)){
            echo 'User not logged in';
            die;
        }

        $resource_id = \request('resource_id');

        // -1 energy
        $currency_id = 1;
        $balance = \App\Models\Balance::where('user_id', $user_id)->where('currency_id', $currency_id)->first();
        $balance->value = $balance->value - 1;
        $balance->save();

        $portal = \App\Models\Portal::where('resource_id', $resource_id)->first();

        Auth::user()->land_id = $portal->land_id;
        Auth::user()->posx = $portal->posx;
        Auth::user()->posy = $portal->posy;
        Auth::user()->save();
        return Redirect::route('play');
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

//        $data = [
//            'user_id' => $user_id,
//            'posx' => \request('x'),
//            'posy' => \request('y'),
//        ];
//        event(new \App\Events\YourCustomEvent($data));



    }

    public function apiList()
    {
        $lands = \App\Models\Land::select('id','name')->get();
        foreach ($lands as $land) {
//            $landArray[$land->id] = $land->name;
            $landArray[] = $land->getAttributes();
        }
        return response()->json($landArray);
    }

    public function moveFarm()
    {
        $user_id = Auth::id();
        $farm_id = \request('farm_id');
        if(empty($user_id)){
            echo 'User not logged in';
            die;
        }
        //if owner of the land
//        echo 'location: '.Auth::user()->land_id.'<br>';
        $land = \App\Models\Land::where('owner_id', $user_id)->where('id', Auth::user()->land_id)->first();
        if(empty($land || $user_id == 1)){
            echo 'You are not the owner of this land';
            die;
        }
        //if farm exista on this land
        $farm = \App\Models\Farm::where('land_id', Auth::user()->land_id)->where('id', $farm_id)->first();
        if(empty($farm)){
            echo 'Farm not found on this land';
            die;
        }

        $farm->posx = \request('x');
        $farm->posy = \request('y');
        $farm->save();

        $data = [
            'status' => 'ok',
            'message' => 'Position saved',
            'extra' => 'Position saved',
        ];
        return response()->json($data);

////        echo 'setting position';
//        echo 'farm_id: '.\request('farm_id').'<br>';
//        echo 'x: '.\request('x');
//        echo 'y: '.\request('y');
//
//
//        die;

//        $msg = 'Position set to '.$_GET['x'].', '.$_GET['y'];
//        \Alert::add('info', $msg);
//        return Redirect::route('dashboard')->with('status', $msg);
    }

    public function addFarm()
    {
        $user_id = Auth::id();
        if(empty($user_id)){
            echo 'User not logged in';
            die;
        }
        //if owner of the land
        $land = \App\Models\Land::where('owner_id', $user_id)->where('id', Auth::user()->land_id)->first();
        if(empty($land || $user_id == 1)){
            echo 'You are not the owner of this land';
            die;
        }

        $resource = \App\Models\Resource::where('id', \request('resource_id'))->first();

        $farm = new \App\Models\Farm();
        $farm->resource_id = \request('resource_id');
        $farm->land_id = Auth::user()->land_id;
        $farm->posx = Auth::user()->posx;
        $farm->posy = Auth::user()->posy;
        $farm->health = $resource->health;
//        $farm->user_id = $user_id;
        $farm->save();

        return Redirect::route('play');

    }

    public function setFarm()
    {
        $user_id = Auth::id();
        if(empty($user_id)){
            echo 'User not logged in';
            die;
        }
        //if owner of the land
        $land = \App\Models\Land::where('owner_id', $user_id)->where('id', Auth::user()->land_id)->first();
        if(empty($land || $user_id == 1)){
            echo 'You are not the owner of this land';
            die;
        }

        //if currency is buildable
        $currency_id = \request('currency_id');
        $currency = Currency::where('id', $currency_id)->first();
        if(empty($currency->resource_id)){
            echo 'Currency not buildable';
            die;
        }

        //check if not reach the limit
        $landType = \App\Models\LandType::where('id', $land->type_id)->first();
        $limit = 0;
        foreach ($landType->farms as $farm) {
            if($farm['resource'] == $currency->resource_id){
                $limit = $farm['value'];
                break;
            }
        }

        if($limit == 0){
            echo 'You cant build this resource on this land';
            die;
        }
        //check how much exist on this land
        $farms_amount = \App\Models\Farm::where('land_id', Auth::user()->land_id)->where('resource_id', $currency->resource_id)->count();

        echo 'Limit: '.$farms_amount.'/'.$limit.'<br>';

        if($farms_amount >= $limit){
            echo 'You reach the limit of this resource on this land';
            die;
        }
//        die;

        //if has this currency in balance decrease balance
        $balance = \App\Models\Balance::where('user_id', $user_id)->where('currency_id', $currency_id)->first();
        if(empty($balance)){
            echo 'You do not have this currency';
            die;
        }
        if($balance->value < 1){
            echo 'You do not have enough currency';
            die;
        }
        $balance->value = $balance->value - 1;
        $balance->save();

//        dd($currency->getAttributes());

        $farm = new \App\Models\Farm();
        $farm->resource_id = $currency->resource_id;
        $farm->land_id = Auth::user()->land_id;
        $farm->user_id = $user_id;
        $farm->posx = \request('x');
        $farm->posy = \request('y');
        $farm->is_public = false;
        $farm->health = ($currency->health)??0;
        $farm->save();

        return Redirect::route('play');

    }
    public function pickFarm()
    {
        $user_id = Auth::id();
        if(empty($user_id)){
            echo 'User not logged in';
            die;
        }
        //if owner of the land
        $land = \App\Models\Land::where('owner_id', $user_id)->where('id', Auth::user()->land_id)->first();
        if(empty($land || $user_id == 1)){
            echo 'You are not the owner of this land';
            die;
        }
        //if farm is picable
        $farm_id = \request('farm_id');
        $farm = \App\Models\Farm::where('id', $farm_id)->first();
        if(empty($farm)){
            echo 'Farm not found';
            die;
        }

        $currency = Currency::where('resource_id', $farm->resource_id)->first();
        if(empty($currency)){
            echo 'Farm is picable - Currency not found';
            die;
        }

        echo 'Farm is picable';
        echo 'Currency: '.$currency->name;

        $farm->delete();


        if(!empty($farm->user_id)){
            $user_id = $farm->user_id;
        }

        //add balance
        $balance = \App\Models\Balance::where('user_id', $user_id)->where('currency_id', $currency->id)->first();
        if(empty($balance)){
            $balance = new \App\Models\Balance();
            $balance->user_id = $user_id;
            $balance->currency_id = $currency->id;
            $balance->value = 1;
            $balance->save();
        } else {
            $balance->value = $balance->value + 1;
            $balance->save();
        }

        return Redirect::route('play');

    }


    public function apiGridSave()
    {

        $user_id = Auth::id();
        if(empty($user_id)){
            echo 'User not logged in';
            die;
        }
        if ($user_id != 1) {
            echo 'You are not the owner of this game';
            die;
        }


        $land_id = request('land_id');
        $grid = request('grid');

//        dd($grid);

        $land = \App\Models\Land::where('id', $land_id)->first();
//        dd($land);
        $landType = \App\Models\LandType::where('id', $land->type_id)->first();
//        dd($landType);
        $landType->grid = $grid;
        echo "length of grid: ".sizeof($grid)."<br>";
        $landType->save();
    }


}
