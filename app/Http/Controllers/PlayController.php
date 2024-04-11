<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PlayController extends Controller
{

    public function play()
    {
        $user_id = Auth::id();
        if(empty($user_id)){
            echo 'User not logged in';
            die;
        }


//        $lands = \App\Models\Land::select('id','name')->get();
//        foreach ($lands as $land) {
//            $landArray[$land->id] = $land->name;
//        }
//
//
//        foreach ($lands as $land) {
//            if($land->id == Auth::user()->land_id) {
//                echo $land->name.' - ['.$land->id.']<br>';
//            } else {
//                echo '<a href="/land/go?id='.$land->id.'">'.$land->name.' - ['.$land->id.'] </a><br>';
//            }
//        }
//
//        //back
//        echo '<a href="/dashboard">Back</a>';


        return view('play');


    }

    public function apiLoad()
    {
        $user_id = Auth::id();
        if (empty($user_id)) {
            echo 'User not logged in';
            die;
        }

        $currency = \App\Models\Currency::get();
        foreach ($currency as $c) {
            $currencyArray[$c->id] = $c->name;
        }

        $balance = \App\Models\Balance::where('user_id', $user_id)->get();
        foreach ($balance as $b) {
            $balanceArray[$b->currency_id] = $b->value;
        }


        $resource = \App\Models\Resource::get();
        //dd($resource);
        foreach ($resource as $r) {
            $resourceArray[$r->id] = [
                'name' => $r->name,
                'size' => $r->size,
                'img' => $r->image,
                'img_hover' => $r->image_hover
            ];
        }


        $farms = \App\Models\Farm::where('land_id',Auth::user()->land_id)->orderBy('resource_id')->get();
        $farmsArray = [];
        $farmsServiceArray = [];
        foreach ($farms as $farm) {
            $farmsArray[$farm->id] = $farm->getAttributes();
            //            echo $resourceArray[$farm->resource_id]['name'].'['.$farm->id.']<br>';
            //            echo '<img src="'.$resourceArray[$farm->resource_id]['img'].'" width="50" height="50"><br>';

            $service_use = \App\Models\ServiceUse::where('user_id', $user_id)
                ->where('farm_id', $farm->id)
                ->orderBy('id', 'desc')
                ->first();

            $services = \App\Models\Service::where('resource_id', $farm->resource_id)->get();
            if(sizeof($services) == 1){
                $farmsArray[$farm->id]['is_single'] = true;
            } else {
                $farmsArray[$farm->id]['is_single'] = false;
            }

            $serviceArray = [];
            foreach ($services as $service) {
                $farmsServiceArray[$farm->id] = $service->getAttributes();
                if(!empty($service_use) && $service->id == $service_use->service_id){
                    $farmsServiceArray[$farm->id]['in_use'] = true;
                }
            }


        }

        //avatar
        $avatarsArray = [];
        $avatars = \App\Models\Avatar::get();
        foreach ($avatars as $avatar) {
            $avatarsArray[$avatar->id] = ['name' => $avatar->name, 'img' => $avatar->image];
        }
//                        dd($avatarArray);


//$lands = \App\Models\Land::select('id','name')->get();
//foreach ($lands as $land) {
//    $landArray[$land->id] = $land->name;
//}

        $heroland = \App\Models\Land::where('id', Auth::user()->land_id)->first();
//dd($land->image);


        $data = [];
        $data['land_id'] = Auth::user()->land_id;
        $data['map'] = $heroland->image;
        $data['posx'] = Auth::user()->posx;
        $data['posy'] = Auth::user()->posy;
//        $data['heroland'] = $heroland;
        $data['avatar_id'] = Auth::user()->avatar_id;
        $data['currency'] = $currencyArray;
        $data['balance'] = $balanceArray;
        $data['avatars'] = $avatarsArray;
        $data['resource'] = $resourceArray;

        $data['farms'] = $farmsArray;
//        $data['service'] = $farmsServiceArray;


        return response()->json($data);
    }

}
