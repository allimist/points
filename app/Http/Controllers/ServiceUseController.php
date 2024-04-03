<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use function Symfony\Component\Translation\t;

class ServiceUseController extends Controller
{

    /**
     * Update the user's profile information.
     */
    public function claim()
    {

        $land_owner_percentage = 0.01;

        $user_id = Auth::id();
        if(empty($user_id)){
            echo 'User not logged in';
            die;
        }

        $farm_id = \request('farm_id');
        $service_id = \request('service_id');

        echo 'user id: '. $user_id.'<br>';
        echo 'farm id: '.$farm_id.'<br>';
        echo 'service id: '. $service_id.'<br>';

        $currencies = \App\Models\Currency::all();
        foreach ($currencies as $currency){
            $currencyArray[$currency->id] = $currency->name;
        }
        $service = \App\Models\Service::find($service_id);


        //VALIDATE IF USER can use service
        $claim = false;
        $service_use = \App\Models\ServiceUse::where('user_id', $user_id)->where('farm_id', $farm_id)->where('service_id', $service_id)
            ->orderBy('id','desc')->first();
        if(!empty($service_use)){

            //check if not claimed
            if($service_use->claimed_at == null){

                //if time to claim is 0, claim it
                if(strtotime($service_use->created_at->addSeconds($service->time)) -strtotime(now()) > 0 ){
//                    $service_use->claimed_at = now();
//                    $service_use->save();
//                    echo 'Service claimed';
//                    die;
                    echo 'item not ready';
                    die;
                } else {
                    $claim = true;
                    echo 'item ready';
                }

//                $diff=strtotime($service_use->created_at->addSeconds($service->time))-strtotime(now());
//                echo $diff;
//                echo 'created time: '.$service_use->created_at.'<br>';
//                echo 'work time: '.$service->time.'<br>';
//                echo 'sum :'.$service_use->created_at->addSeconds($service->time).'<br>';
//                echo 'now: '.now().'<br>';




            }

            //check if $service_use claimed_at + service reload < now tell not ready with time

//            $date1=$service_use->claimed_at->addSeconds($service->reload)
//            $date2=date_create("2013-12-12");
//            dd($service->reload);
//            if(empty($service->reload)){
//                $service->reload = 0;
//            }
            if($service->reload == 0){
                $claimed_at = $service_use->claimed_at;
            } else {
                $claimed_at = $service_use->claimed_at->addSeconds($service->reload);
            }
            $diff=strtotime($claimed_at)-strtotime(now());
            echo $diff;
            echo 'claim time: '.$service_use->claimed_at.'<br>';
            echo 'reload time: '.$service->reload.'<br>';
            echo 'sum :'.$claimed_at.'<br>';
            echo 'now: '.now().'<br>';
//            die;
//            echo $diff->format("%R%a days");



//            if($service_use->claimed_at->addSeconds($service->reload) < now()){

            if(strtotime($claimed_at)-strtotime(now()) > 0){
                echo 'claim time: '.$service_use->claimed_at.'<br>';
                echo 'reload time: '.$service->reload.'<br>';
                echo 'sum :'.$service_use->claimed_at->addSeconds($service->reload).'<br>';
                echo 'now: '.now().'<br>';
                echo 'Service not ready';
                die;
            }





//            echo 'Service already used';
//            die;
        }

        //VALIDATE IF USER HAS ENOUGH BALANCE
        $validBalance = true;
        $balance = \App\Models\Balance::where('user_id', $user_id)->get();
        foreach ($balance as $b){
            $balanceArray[$b->currency_id] = $b->value;
        }
        if(!$claim){
            foreach ($service->cost as $cost){

                if(empty($balanceArray[$cost['resource']])){
                    $balanceArray[$cost['resource']] = 0;
                }

                echo $currencyArray[$cost['resource']].' Cost: '.$cost['value'].' have: '.$balanceArray[$cost['resource']].'<br>';
                if($balanceArray[$cost['resource']] < $cost['value']){
                    echo 'Not enough '.$currencyArray[$cost['resource']].' balance';
                    $validBalance = false;
                    die;
                }
            }
            echo 'Enough balance<br>';

            //subtract balance
            foreach ($service->cost as $cost){
                $balance = \App\Models\Balance::where('user_id', $user_id)->where('currency_id', $cost['resource'])->first();
                $balance->value = $balance->value - $cost['value'];
                $balance->save();
            }
        }


        //if time == 0 add balance or claim = true
        if($service->time == 0 || $claim){

            //land owner
            $land_owner_id = 0;
            $farm = \App\Models\Farm::find($farm_id);
//            dd($farm->land_id);
            $land = \App\Models\Land::find($farm->land_id);
            if($land->owner_id){
                $land_owner_id = $land->owner_id;
            }

            foreach ($service->revenue as $revenue){
                $balance = \App\Models\Balance::where('user_id', $user_id)->where('currency_id', $revenue['resource'])->first();
                if(empty($balance)){
                    $balance = new \App\Models\Balance();
                    $balance->user_id = $user_id;
                    $balance->currency_id = $revenue['resource'];
                    $balance->value = $revenue['value'];
                    $balance->save();
                } else {
                    $balance->value = $balance->value + $revenue['value'];
                    $balance->save();
                }

                if($land_owner_id){
                    $balance = \App\Models\Balance::where('user_id', $land_owner_id)->where('currency_id', $revenue['resource'])->first();
                    if(empty($balance)){
                        $balance = new \App\Models\Balance();
                        $balance->user_id = $land_owner_id;
                        $balance->currency_id = $revenue['resource'];
                        $balance->value = ($revenue['value'] * $land_owner_percentage);
                        $balance->save();
                    } else {
//                        dd($balance->value + ($revenue['value'] * $land_owner_percentage));
                        $balance->value = $balance->value + ($revenue['value'] * $land_owner_percentage);
//                        dd($balance->value);
                        $balance->save();
                    }
                }


            }
        }

        if(!$claim){
            //add service use
            $service_use = new \App\Models\ServiceUse();
            $service_use->user_id = $user_id;
            $service_use->farm_id = $farm_id;
            $service_use->service_id = $service_id;
            $service_use->amount = 1;
            if($service->time == 0) {
                $service_use->claimed_at = now();
                echo 'Service claimed<br>';
            } else {
                echo 'Service started<br>';
            }
            $service_use->save();
        } else {
            $service_use->claimed_at = now();
            $service_use->save();
            echo 'Service claimed<br>';
//            die;
        }



        return Redirect::route('dashboard')->with('status', 'balance-updated');
    }

    public function select()
    {
        $farm = \App\Models\Farm::find(\request('farm_id'));
        $services = \App\Models\Service::where('resource_id', $farm->resource_id)->get();
        $currencies = \App\Models\Currency::all();
        $resource = \App\Models\Resource::find($farm->resource_id);

        foreach ($currencies as $currency){
            $currencyArray[$currency->id] = $currency->name;
        }

        $user_id = Auth::user()->id;
        $balance = \App\Models\Balance::where('user_id', $user_id)->get();
        foreach ($balance as $b) {
            $balanceArray[$b->currency_id] = $b->value;
        }
        foreach ($currencyArray as $key => $value) {
            if(!empty($balanceArray[$key])) {
                echo $value.':'.$balanceArray[$key].' | ';
            }
        }
        echo '<br><br>';
        echo '<h1>'.$resource->name.'</h1>';
        echo '<br><br>';


        foreach ($services as $service){
            echo $service->name.'[#'.$service->id.']<br>';
            echo 'Revenue:<br>';
            foreach ($service->revenue as $revenue){
                echo $revenue['value'].' '.$currencyArray[$revenue['resource']].'<br>';
            }
            echo 'Cost:<br>';
            foreach ($service->cost as $cost){
                echo $cost['value'].' '.$currencyArray[$cost['resource']].'<br>';
            }
            echo ' <a href="/service-use/claim?farm_id='.$farm->id.'&service_id='.$service->id.'">Start</a><br>';
            echo '<br>';
        }

        die;
//        return view('service-use.select', ['services' => $services]);
    }


}
