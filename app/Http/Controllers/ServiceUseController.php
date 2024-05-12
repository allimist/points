<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Balance;
use App\Models\Resource;
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


    public function balance($currencyArray){

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
        echo '<br>';


    }


    public function Apibalance($currencyArray){

        $user_id = Auth::user()->id;
        $balance = \App\Models\Balance::where('user_id', $user_id)->where('value', '>' , 0)->orderBy('currency_id')->get();
        $balance_string = '';
        foreach ($balance as $b) {
//            $balanceArray[$b->currency_id] = $b->value;
//            $balance_string .= $b->value . ' ' . $currencyArray[$b->currency_id] . ' | ';
//            echo '<img class="resources" src= "/storage/'.$value['img'].'"> '.$balanceArray[$key].' | ';
            $balance_string .= '<img class="resources" data-id="'.$b->currency_id.'" src= "/storage/'.$currencyArray[$b->currency_id]['img'].'"> ';
            $balance_string .=  floor($b->value).' | ';

        }
        $balance_string = substr($balance_string, 0, -2);
//        return $balanceArray;
        return $balance_string;

    }

    public function ApiClaim()
    {

        $land_owner_percentage = 0.01;

        $user_id = Auth::id();
        if(empty($user_id)){
            echo 'User not logged in';
            die;
        }

        $farm_id = \request('farm_id');
        $service_id = \request('service_id');
        $amount = \request('amount');

        //test if amout is valid
        if(empty($amount)){
            $amount = 1;
        }

        $currency = \App\Models\Currency::get();
        foreach ($currency as $c) {
            $currencyArray[$c->id] = ['name'=>$c->name, 'img'=>$c->image];
        }
        $service = \App\Models\Service::find($service_id);
        $resource = Resource::where('id',$service->resource_id)->first();

        //check service level
        if(!empty($service->skill_id)){
//            if(!empty($resource->skill_id)){
                $skillUser = \App\Models\SkillUser::where('skill_id',$service->skill_id)->where('user_id', $user_id)->first();
                if(empty($skillUser)){
//                    $data = [
//                        'status' => 'error',
//                        'message' => 'No skill user',
//                        'extra' => 'No skill user',
//                    ];
//                    return response()->json($data);
                    $skillUser = new \App\Models\SkillUser();
                    $skillUser->skill_id = $service->skill_id;
                    $skillUser->user_id = $user_id;
                    $skillUser->level = 0;
                    $skillUser->xp = 0;
                    $skillUser->save();
                }
                if($skillUser->level < $service->level){
                    $data = [
                        'status' => 'error',
                        'message' => 'Level not enough',
                        'extra' => 'Level not enough',
                    ];
                    return response()->json($data);
                }
//            }
        }

        //amount allow on market only
        if($service->resource_id != 3 ){
            $amount = 1;
        }

        //if it task validate in user tasks
        if($service->resource_id == 5){
//            $task_ids = [];
//            if(!empty(Auth::user()->task_ids)){
//                $task_ids = json_decode(Auth::user()->task_ids);
//            }
            $user = Auth::user();
            if(empty($user->task_ids)){
                $data = [
                    'status' => 'error',
                    'message' => 'No tasks',
                    'extra' => 'No tasks',
                ];
                return response()->json($data);
            }
            $task_ids = json_decode($user->task_ids);
            if(!in_array($service_id, $task_ids)){
                $data = [
                    'status' => 'error',
                    'message' => 'Task not found',
                    'extra' => 'Task not found',
                ];
                return response()->json($data);
            }
        }


        //VALIDATE IF USER can use service
        $claim = false;
        $msg_balance = '';
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
//                    echo 'item not ready';
                    $data = [
                        'status' => 'error',
                        'message' => 'item not ready',
                        'extra' => 'service not ready',
//                        'balance' => $balanceArray
                    ];
                    return response()->json($data);
//                    $msg = 'item not ready';
//                    die;
                } else {
                    $claim = true;
//                    echo 'item ready';
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
//            echo $diff;
//            echo 'claim time: '.$service_use->claimed_at.'<br>';
//            echo 'reload time: '.$service->reload.'<br>';
//            echo 'sum :'.$claimed_at.'<br>';
//            echo 'now: '.now().'<br>';
//            die;
//            echo $diff->format("%R%a days");



//            if($service_use->claimed_at->addSeconds($service->reload) < now()){

            if(strtotime($claimed_at)-strtotime(now()) > 0){
                echo 'claim time: '.$service_use->claimed_at.'<br>';
                echo 'reload time: '.$service->reload.'<br>';
                echo 'sum :'.$service_use->claimed_at->addSeconds($service->reload).'<br>';
                echo 'now: '.now().'<br>';
                echo 'Service not ready';
//                $msg = 'Service not ready';
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


            //if farm is_public = false, check if another user use or reload it
            $farm = \App\Models\Farm::find($farm_id);
            if(!$farm->is_public) {
                $service_use_farm = \App\Models\ServiceUse::where('farm_id', $farm_id)//->where('claimed_at', null)
                ->orderBy('id', 'desc')->first();
                if($service_use_farm){
                    $service = \App\Models\Service::find($service_use_farm->service_id);

                    if($service_use_farm->claimed_at == null) {
                        $diff = strtotime($service_use_farm->created_at->addSeconds($service->time)) - strtotime(now());
                        if ($diff > 0) {
                            $data = [
                                'status' => 'error',
                                'message' => 'Farm is busy',
                                'extra' => 'Farm is busy by user (in use)' . $service_use_farm->user_id,
                                'farm_service_id'=> $service_use_farm->service_id,
                                'farm_status' => 'in_use',
                                'farm_text' => $diff,
                                'farm_ready' => strtotime(now())+$diff,
                                'farm_use_by'=> $service_use_farm->user_id,
                            ];
                            return response()->json($data);
                        }
                    } else {
                        $diff = strtotime($service_use_farm->claimed_at->addSeconds($service->reload)) -strtotime(now());
//                        if($service->reload > 0 && $service_use_farm->created_at->addSeconds($service->reload) > now()){
                        if($diff > 0){
                            $data = [
                                'status' => 'error',
                                'message' => 'Farm is busy',
                                'extra' => 'Farm is busy by user (reload)' . $service_use_farm->user_id,
                                'farm_service_id'=> $service_use_farm->service_id,
                                'farm_status' => 'reload',
                                'farm_text' => $diff,
                                'farm_ready' => strtotime(now())+$diff,
                                'farm_use_by'=> $service_use_farm->user_id,
                            ];
                            return response()->json($data);
                        }
                    }
                }

//                if(!empty($service_use)){
//                    $data = [
//                        'status' => 'error',
//                        'message' => 'Farm is busy',
//                        'extra' => 'Farm is busy',

            }
            foreach ($service->cost as $cost){

                if(empty($balanceArray[$cost['resource']])){
                    $balanceArray[$cost['resource']] = 0;
                    //need add emty balance record here

                }

                if($balanceArray[$cost['resource']] < $cost['value'] * $amount){
                    $data = [
                        'status' => 'error',
                        'message' => 'Not enough '.$currencyArray[$cost['resource']]['name'].'  balance need '.($cost['value'] * $amount),
                        'extra' => 'Not enough '.$currencyArray[$cost['resource']]['name'].' balance',
//                        'balance' => $balanceArray
                    ];
                    return response()->json($data);
                }
            }
//            echo 'Enough balance<br>';

            //subtract balance
            foreach ($service->cost as $cost){
                $balance = \App\Models\Balance::where('user_id', $user_id)->where('currency_id', $cost['resource'])->first();
                $balance->value = $balance->value - ($cost['value'] * $amount);
                $balance->save();
                $msg_balance .= '-'.($cost['value'] * $amount).' '.$currencyArray[$cost['resource']]['name'].' <br>';
            }
        }


        //if time == 0 add balance or claim = true
        if($service->time == 0 || $claim){

            foreach ($service->revenue as $revenue){
                //check level
                if(!empty($revenue['level'])){
//                    $resource = Resource::where('id',$service->resource_id)->first();
                    if(!empty($resource->skill_id)){
                        $skillUser = \App\Models\SkillUser::where('skill_id',$resource->skill_id)->where('user_id', $user_id)->first();
                        if(empty($skillUser)){
//                            echo 'No skill user';
                            continue;
                        }
                        if($skillUser->level < $revenue['level']){
//                            echo 'Level not enough';
                            continue;
                        }
                    }
                }

                //check procents
                if(empty($revenue['percent'])){
//                    print_r($revenue);
//                    echo 'No percent';
                    continue;
                }
                if($revenue['percent'] < 100){
                    $random = rand(1, 100);
                    if($random > $revenue['percent']){
//                        echo 'Random not enough';
                        continue;
                    }
                }
                $balance = \App\Models\Balance::where('user_id', $user_id)->where('currency_id', $revenue['resource'])->first();
                if(empty($balance)){
                    $balance = new \App\Models\Balance();
                    $balance->user_id = $user_id;
                    $balance->currency_id = $revenue['resource'];
                    $balance->value = $revenue['value'] * $amount;
                    $balance->save();
                } else {
                    $balance->value = $balance->value + ($revenue['value'] * $amount);
                    $balance->save();
                }
                $msg_balance .= '+'.($revenue['value'] * $amount).' '.$currencyArray[$revenue['resource']]['name'].' <br>';

                //land owner
                $land_owner_id = 0;
                $farm = \App\Models\Farm::find($farm_id);
                $land = \App\Models\Land::find($farm->land_id);
                if($land->owner_id){
                    $land_owner_id = $land->owner_id;
                }
                if($land_owner_id){
                    $balance = \App\Models\Balance::where('user_id', $land_owner_id)->where('currency_id', $revenue['resource'])->first();
                    if(empty($balance)){
                        $balance = new \App\Models\Balance();
                        $balance->user_id = $land_owner_id;
                        $balance->currency_id = $revenue['resource'];
                        $balance->value = ($revenue['value'] * $amount * $land_owner_percentage);
                        $balance->save();
                    } else {
//                        dd($balance->value + ($revenue['value'] * $land_owner_percentage));
                        $balance->value = $balance->value + ($revenue['value'] * $amount * $land_owner_percentage);
//                        dd($balance->value);
                        $balance->save();
                    }
                }

            }

            //add experience
            if(!empty($service->xp)){
//                echo "xp yes".$service->xp.'<br>';
//                $resource = Resource::where('id',$service->resource_id)->first();
                if(!empty($resource->skill_id)){
//                    echo "resource->skill_id: ".$resource->skill_id.'<br>';
                    $skillUser = \App\Models\SkillUser::where('skill_id',$resource->skill_id)->where('user_id', $user_id)->first();
                    if(empty($skillUser)){
                        $skillUser = new \App\Models\SkillUser();
                        $skillUser->skill_id = $resource->skill_id;
                        $skillUser->user_id = $user_id;
                        $skillUser->level = 0;
                        $skillUser->xp = $service->xp;
                        $skillUser->save();
//                        print_r($skillUser->toArray());
                    } else {
                        $skillUser->xp += $service->xp;
                        $skillUser->save();
                    }

                    //recalculate level ????
                    //check if level up
//                    $skilllevel = \App\Models\SkillLevel::where('skill_id',$resource->skill_id)->where('level', $skillUser->level+1)->first();
//                    if(!empty($skilllevel)) {
                        $skilllevel = \App\Models\SkillLevel::where('skill_id', null)
                            ->where('level', $skillUser->level + 1)->first();
//                    }
                    if(!empty($skilllevel)){
//                        echo $skillUser->xp .' '
                        if($skillUser->xp >= $skilllevel->xp_required){
                            $skillUser->level += 1;
                            $skillUser->xp = $skillUser->xp - $skilllevel->xp_required;
                            $skillUser->save();
                        }
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
//                echo 'Service claimed<br>';
                $msg = 'Service claimed';
            } else {
//                echo 'Service started<br>';
                $msg = 'Service started';
            }
            $service_use->save();
        } else {
            $service_use->claimed_at = now();
            $service_use->save();
//            echo 'Service claimed<br>';
            $msg = 'Service claimed';
//            die;
        }

        //clear user task to get new one
        if($service->resource_id == 5){

            //remove task from task_ids array
            $task_ids = json_decode(Auth::user()->task_ids,1);

            if (($key = array_search($service_id, $task_ids)) !== false) {
                unset($task_ids[$key]);
            }

            $new_task_ids = [];
//            $first = false;
            foreach ($task_ids as $key => $task_id){
//                if($task_id != $service_id){
////                    unset($task_ids[$key]);
////                    break;
                    $new_task_ids[] = $task_id;
//                } else {
//
//                }
            }
//            $task_ids = array_diff($task_ids, [$service_id]);

            Auth::user()->task_ids = $new_task_ids;
            Auth::user()->save();

//            dd($task_ids);
//            $user = Auth::user();
//            $user->task_ids = null;
//            $user->save();
        }

        $balanceArrayHtml = $this->Apibalance($currencyArray);


        $data = [
            'status' => 'success',
            'message' => $msg,
            'extra' => $msg_balance,
            'balance' => $balanceArrayHtml,
            'user_health' => Balance::where('user_id', $user_id)->where('currency_id', 25)->first()->value,
        ];

//        return Redirect::route('dashboard')->with('status', 'balance-updated');
        return response()->json($data);
    }


    /*
    public function select()
    {

        $currencies = \App\Models\Currency::all();
        foreach ($currencies as $currency){
            $currencyArray[$currency->id] = $currency->name;
        }
        $this->balance($currencyArray);

        $farm = \App\Models\Farm::find(\request('farm_id'));


        $resource = \App\Models\Resource::find($farm->resource_id);
        echo '<br><br>';
        echo '<h1>'.$resource->name.'</h1>';
        echo 'id: '.$resource->id.'<br>';
        echo '<br><br>';

        if($resource->id == 7){
            $services = \App\Models\Service::where('resource_id', 6)->get();
        } else {
            $services = \App\Models\Service::where('resource_id', $farm->resource_id)->get();
        }


        foreach ($services as $service){
            echo $service->name.'[#'.$service->id.']<br>';


//            dd($resource->getAttributes());
            if($resource->id == 6 ) {

//                echo ' <a href="/service-use/sell?farm_id=' . $farm->id . '&service_id=' . $service->id . '">Sell</a><br>';
                echo '<form action="/service-use/sell" method="get">';
                echo '<input type="hidden" name="farm_id" value="' . $farm->id . '">';
                echo '<input type="hidden" name="service_id" value="' . $service->id . '">';
                echo 'Price:<input type="number" id="price" name="price" value="10" min="1" max="1000"> ';
                echo 'Amount:<input type="number" id="amount" name="amount" value="2" min="1" max="1000"> ';
//                echo ' <a href="/service-use/sell?farm_id=' . $farm->id . '&service_id=' . $service->id . '">Sell</a><br>';
                echo '<input type="submit" value="Sell">';
                echo '</form>';

            } if($resource->id == 7 ){

                echo ' <a href="/service-use/orders?farm_id=' . $farm->id . '&service_id=' . $service->id . '">Buy</a><br>';
//                echo '<form action="/service-use/buy" method="get">';
//                echo '<input type="hidden" name="farm_id" value="'.$farm->id.'">';
//                echo '<input type="hidden" name="service_id" value="'.$service->id.'">';
//                echo 'Amount:<input type="number" id="amount" name="amount" value="2" min="1" max="1000"> ';
////                echo ' <a href="/service-use/sell?farm_id=' . $farm->id . '&service_id=' . $service->id . '">Sell</a><br>';
//                echo '<input type="submit" value="B">';
//                echo '</form>';


            } else {


                echo 'Revenue:<br>';
                foreach ($service->revenue as $revenue) {
                    echo $revenue['value'] . ' ' . $currencyArray[$revenue['resource']] . '<br>';
                }
                echo 'Cost:<br>';
                foreach ($service->cost as $cost) {
                    echo $cost['value'] . ' ' . $currencyArray[$cost['resource']] . '<br>';
                }
                echo ' <a href="/service-use/claim?farm_id=' . $farm->id . '&service_id=' . $service->id . '">Start</a><br>';

            }

            echo '<br>';
        }

        echo '<a href ="/dashboard">Back</a>';

        die;
//        return view('service-use.select', ['services' => $services]);
    }
    */

    public function ApiSelect()
    {

        $user_id = Auth::id();
        if (empty($user_id)) {
            echo 'User not logged in';
            die;
        }

        $farm = \App\Models\Farm::find(\request('farm_id'));
        if($farm->resource_id == 5) {
            $open_tasks = 6;

            $task_ids = [];
            if (!empty(Auth::user()->task_ids)) {
                $task_ids = json_decode(Auth::user()->task_ids);
            }
            if (sizeof($task_ids) < $open_tasks) {

//                $services = \App\Models\Service::where('resource_id', 5)->get();

                $tasks = [];
                for ($i = sizeof($task_ids); $i < $open_tasks; $i++) {

                    $userSkill = \App\Models\SkillUser::where('user_id', $user_id)->inRandomOrder()->first()->getAttributes();
                    $service = \App\Models\Service::where('resource_id', 5)->where('skill_id', $userSkill['skill_id'])->where('level', '<=' ,$userSkill['level'])->inRandomOrder()->first();
//                    dd($userSkills);
//                    dd($service);


//                    $random = rand(0, sizeof($services) - 1);
//                    $task_ids[] = $services[$random]->id;
                    $task_ids[] = $service->id;
//                    break;
                }
                Auth::user()->task_ids = $task_ids;
                Auth::user()->save();

            }

            foreach ($task_ids as $task_id) {
                $tasks[] = \App\Models\Service::where('id', $task_id)->first();
            }

            $services = $tasks;
        } else if($farm->resource_id == 7){
            $services = \App\Models\Service::where('resource_id', 6)->get();
            foreach ($services as $key => $service){
                $cost = $service->cost;
                $services[$key]->cost = $service->revenue;
                $services[$key]->revenue = $cost;
            }
        } else {
            $services = \App\Models\Service::where('resource_id', $farm->resource_id)->get();
        }

//        $resource = \App\Models\Resource::find($farm->resource_id);

        $level = \App\Models\SkillUser::select('skill_id','level')->where('user_id', Auth::id())->get();
        foreach ($level as $l){
            $levelArray[$l->skill_id] = $l->level;
        }

        $data = [
            'services' => $services,
            'level' => $levelArray
        ];

        return response()->json($data);

    }


    //sell
    /*
    public function sell()
    {

        $user_id = Auth::id();
        if(empty($user_id)){
            echo 'User not logged in';
            die;
        }

        $currencies = \App\Models\Currency::all();
        foreach ($currencies as $currency){
            $currencyArray[$currency->id] = $currency->name;
        }
        $this->balance($currencyArray);

        $farm_id = \request('farm_id');
        $service_id = \request('service_id');
        $price = \request('price');
        $amount = \request('amount');

        $service = \App\Models\Service::find($service_id);


        //validate if user has enough resource

        //decrease balance
        $balance = \App\Models\Balance::where('user_id', $user_id)->where('currency_id', $service->cost[0]['resource'])->first();

        if(empty($balance) || $balance->value < $amount){
            echo 'Not enough resource';
            die;
        } else {
            $balance->value = $balance->value - $amount;
            $balance->save();
        }

        //add order
        $order = new \App\Models\Order();
        $order->service_id = $service_id;
        $order->user_id = $user_id;
        $order->type = 'sell';
        $order->price = $price;
        $order->amount = $amount;
        $order->save();

//        dd($service->cost[0]['resource']);

//        echo 1; die;

        echo "ok";
        //back link
        return Redirect::route('dashboard')->with('status', 'balance-updated');




    }
    */

    public function ApiSell()
    {

        $user_id = Auth::id();
        if(empty($user_id)){
            echo 'User not logged in';
            die;
        }

        $currency = \App\Models\Currency::get();
        foreach ($currency as $c) {
            $currencyArray[$c->id] = ['name'=>$c->name, 'img'=>$c->image];
        }

//        $farm_id = \request('farm_id');
        $service_id = \request('service_id');
        $price = \request('price');
        $amount = \request('amount');

        $service = \App\Models\Service::find($service_id);


        //validate if user has enough resource

        //decrease balance
        $balance = \App\Models\Balance::where('user_id', $user_id)->where('currency_id', $service->cost[0]['resource'])->first();

        if(empty($balance) || $balance->value < $amount){
            $msg = 'Not enough resource';
            $msg_balance = 'Not enough resource';
            $data = [
                'status' => 'success',
                'message' => $msg,
                'extra' => $msg_balance,
//                'balance' => $balanceArray
            ];
            return response()->json($data);

        } else {
            $balance->value = $balance->value - $amount;
            $msg = '-'.($amount).' '.$service->cost[0]['resource'].' <br>';
            $msg_balance = '-'.($amount).' '.$currencyArray[$service->cost[0]['resource']]['name'].' <br>';
            $balance->save();
        }

        //add order
        $order = new \App\Models\Order();
        $order->service_id = $service_id;
        $order->user_id = $user_id;
        $order->type = 'sell';
        $order->price = $price;
        $order->amount = $amount;
        $order->save();

//        dd($service->cost[0]['resource']);

//        echo 1; die;

//        echo "ok";
        //back link
//        return Redirect::route('dashboard')->with('status', 'balance-updated');
        $balanceArray = $this->Apibalance($currencyArray);

        $data = [
            'status' => 'success',
            'message' => $msg,
            'extra' => $msg_balance,
            'balance' => $balanceArray
        ];

//        return Redirect::route('dashboard')->with('status', 'balance-updated');
        return response()->json($data);



    }

    /*
    public function orders()
    {

        $currencies = \App\Models\Currency::all();
        foreach ($currencies as $currency){
            $currencyArray[$currency->id] = $currency->name;
        }
        $this->balance($currencyArray);

        $farm = \App\Models\Farm::find(\request('farm_id'));
        $resource = \App\Models\Resource::find($farm->resource_id);
//        $service = \App\Models\Service::find(\request('service_id'));

        echo '<br><br>';
        echo '<h1>'.$resource->name.'</h1>';



//        dd($service);
//        $orders = \App\Models\Order::where('service_id', $service->id)->where('type', 'sell')->get();
//        $orders = \App\Models\Order::where('service_id', $service->id)->get();
//        $orders = \App\Models\Order::whereIn('service_id', [14,15])->get();
        $orders = \App\Models\Order::orderBy('service_id')->orderBy('price')->get();


        $sname[14] = 'Cornin';
        $sname[15] = 'Cucumba';

        foreach ($orders as $order){

            echo '#'.$order->id.' '.$sname[$order->service_id].' ';

            echo ' Price: '.$order->price.' Amount: '.$order->amount;
            echo ' <a href="/service-use/select-order?order_id='.$order->id.'">Select Order</a><br>';

//            echo '<form action="/service-use/buy" method="get">';
//            echo '<input type="hidden" name="order_id" value="'.$order->id.'">';
//            echo 'Amount:<input type="number" id="amount" name="amount" value="2" min="1" max="1000"> ';
//            echo 'Price: '.$order->price.' Amount: '.$order->amount.' ';
//            echo '<input type="submit" value="Buy">';
//            echo '</form>';



        }

        //back link
//        echo '<a href ="/dashboard">Back</a>';
        echo '<a href ="/play">Back</a>';

        die;
//        return view('service-use.select', ['services' => $services]);
    }
    */

    public function apiOrders()
    {
        $service_id = \request('service_id');
        $orders = \App\Models\Order::where('service_id',$service_id)->orderBy('price')->get(['id','price','amount']);
        $ordersArray = [];
        foreach ($orders as $order){
            $ordersArray[] = $order->getAttributes();
        }
        $data = [
            'orders' => $ordersArray,
        ];
        return response()->json($data);
    }

    /*
    public function selectOrder()
    {

        $currencies = \App\Models\Currency::all();
        foreach ($currencies as $currency){
            $currencyArray[$currency->id] = $currency->name;
        }
        $this->balance($currencyArray);

        $order = \App\Models\Order::find(\request('order_id'));
        echo '<br>Order id: '.$order->id.'<br>';
        echo 'Order Price: '.$order->price.'<br>';
        echo 'Amount in sale: '.$order->amount.'<br>';

        echo '<form action="/service-use/buy" method="get">';
        echo '<input type="hidden" name="order_id" value="'.$order->id.'">';
        echo 'Amount to buy: <input type="number" id="amount" name="amount" value="'.$order->amount.'" min="1" max="1000"> ';
        echo 'Price: '.$order->price.' Coins<br>';
        echo '<input type="submit" value="Buy">';
        echo '</form>';

        //back link
        echo '<a href ="/service-use/orders?farm_id=18">Back</a>';

        die;
//        return view('service-use.select', ['services' => $services]);
    }
    */

    public function ApiSelectOrder()
    {

        $currencies = \App\Models\Currency::all();
        foreach ($currencies as $currency){
            $currencyArray[$currency->id] = $currency->name;
        }
        $this->balance($currencyArray);

        $order = \App\Models\Order::find(\request('order_id'));
        echo '<br>Order id: '.$order->id.'<br>';
        echo 'Order Price: '.$order->price.'<br>';
        echo 'Amount in sale: '.$order->amount.'<br>';

        echo '<form action="/service-use/buy" method="get">';
        echo '<input type="hidden" name="order_id" value="'.$order->id.'">';
        echo 'Amount to buy: <input type="number" id="amount" name="amount" value="'.$order->amount.'" min="1" max="1000"> ';
        echo 'Price: '.$order->price.' Coins<br>';
        echo '<input type="submit" value="Buy">';
        echo '</form>';

        //back link
        echo '<a href ="/service-use/orders?farm_id=18">Back</a>';

        die;
//        return view('service-use.select', ['services' => $services]);
    }

    /*
    public function buy()
    {

        $user_id = Auth::id();
        if(empty($user_id)){
            echo 'User not logged in';
            die;
        }

        $currencies = \App\Models\Currency::all();
        foreach ($currencies as $currency){
            $currencyArray[$currency->id] = $currency->name;
        }
        $this->balance($currencyArray);

//        $farm_id = \request('farm_id');
        $order = \App\Models\Order::find(\request('order_id'));

        if(empty($order)){
            echo 'Order not found';
            die;
        }

        $service  = \App\Models\Service::find($order->service_id);


        //validate if user has enough resource

        //decrese amount of order or close order
        if($order->amount < \request('amount')){
            echo 'Not enough amount on order';
            die;
        } else if($order->amount > \request('amount')){
            $order->amount = $order->amount - \request('amount');
            $order->save();
        } else {
            $order->delete();
        }

        //decrease balance coins
        $amount = \request('amount') * $order->price;
        $balance = \App\Models\Balance::where('user_id', $user_id)->where('currency_id', $service->revenue[0]['resource'])->first();
        if(empty($balance) || $balance->value < $amount * $order->price){
            echo 'Not enough resource';
            die;
        } else {
            $balance->value = $balance->value - $amount * $order->price;
            $balance->save();
        }


        //add balance to seler order maker
        $balance = \App\Models\Balance::where('user_id', $order->user_id)->where('currency_id', $service->revenue[0]['resource'])->first();
        if(empty($balance)){
            $balance = new \App\Models\Balance();
            $balance->user_id = $order->user_id;
            $balance->currency_id = $service->revenue[0]['resource'];
            $balance->value = $amount * $order->price;
            $balance->save();
        } else {
            $balance->value = $balance->value + $amount * $order->price;
            $balance->save();
        }

        //add balance to buyer
        $balance = \App\Models\Balance::where('user_id', $user_id)->where('currency_id', $service->cost[0]['resource'])->first();
        if(empty($balance)){
            $balance = new \App\Models\Balance();
            $balance->user_id = $user_id;
            $balance->currency_id = $service->cost[0]['resource'];
            $balance->value = \request('amount');
            $balance->save();
        } else {
            $balance->value = $balance->value + \request('amount');
            $balance->save();
        }



//        dd($service->cost[0]['resource']);


//        echo "ok";
//        echo '<a href ="/service-use/orders?farm_id=18&service_id='.$service->id.'">Back</a>';
        echo '<a href ="/service-use/orders?farm_id=18&service_id='.$service->id.'">Back</a>';
//        echo 1;
        die;

        //back link
//        return Redirect::route('dashboard')->with('status', 'balance-updated');
//        return Redirect::route('dashboard')->with('status', 'balance-updated');




    }
    */

    public function apiBuy()
    {

        $user_id = Auth::id();
        if(empty($user_id)){
            echo 'User not logged in';
            die;
        }

//        $currencies = \App\Models\Currency::all();
//        foreach ($currencies as $currency){
//            $currencyArray[$currency->id] = $currency->name;
//        }

        $currency = \App\Models\Currency::get();
        foreach ($currency as $c) {
            $currencyArray[$c->id] = ['name'=>$c->name, 'img'=>$c->image];
        }

//        $this->balance($currencyArray);

//        $farm_id = \request('farm_id');
        $order = \App\Models\Order::find(\request('order_id'));

        if(empty($order)){
            echo 'Order not found';
            die;
        }

        $service  = \App\Models\Service::find($order->service_id);


        //validate if user has enough resource

        //decrese amount of order or close order
        if($order->amount < \request('amount')){
            echo 'Not enough amount on order';
            die;
        } else if($order->amount > \request('amount')){
            $order->amount = $order->amount - \request('amount');
            $order->save();
        } else {
            $order->delete();
        }

        //decrease balance coins
        $amount = \request('amount');
        $balance = \App\Models\Balance::where('user_id', $user_id)->where('currency_id', $service->revenue[0]['resource'])->first();
        if(empty($balance) || $balance->value < $amount * $order->price){
            echo 'Not enough resource on balance '.$balance->value.' < '.$amount * $order->price;
            die;
        } else {
            $balance->value = $balance->value - $amount * $order->price;
            $balance->save();
        }
//        $msg = +\request('amount').' '.$service->cost[0]['resource'].' <br>';
//        $msg_balance = '+'.(\request('amount')).' '.$currencyArray[$service->cost[0]['resource']]['name'].' <br>';
        $msg = '-'.($amount * $order->price).' '.$service->revenue[0]['resource'].' <br>';
        $msg_balance = '-'.($amount * $order->price).' '.$currencyArray[$service->revenue[0]['resource']]['name'].' <br>';



        //add balance to seler order maker
        $balance = \App\Models\Balance::where('user_id', $order->user_id)->where('currency_id', $service->revenue[0]['resource'])->first();
        if(empty($balance)){
            $balance = new \App\Models\Balance();
            $balance->user_id = $order->user_id;
            $balance->currency_id = $service->revenue[0]['resource'];
            $balance->value = $amount * $order->price;
            $balance->save();
        } else {
            $balance->value = $balance->value + $amount * $order->price;
            $balance->save();
        }

        //add balance to buyer
        $balance = \App\Models\Balance::where('user_id', $user_id)->where('currency_id', $service->cost[0]['resource'])->first();
        if(empty($balance)){
            $balance = new \App\Models\Balance();
            $balance->user_id = $user_id;
            $balance->currency_id = $service->cost[0]['resource'];
            $balance->value = \request('amount');
            $balance->save();
        } else {
            $balance->value = $balance->value + \request('amount');
            $balance->save();
        }
        $msg .= ' +'.\request('amount').' '.$service->cost[0]['resource'].' <br>';
        $msg_balance .= ' +'.(\request('amount')).' '.$currencyArray[$service->cost[0]['resource']]['name'].' <br>';

        $balanceArray = $this->Apibalance($currencyArray);

        $data = [
            'status' => 'success',
            'message' => $msg,
            'extra' => $msg_balance,
            'balance' => $balanceArray
        ];
        return response()->json($data);
    }

    public function apiFarmAttack()
    {

//        $land_owner_percentage = 0.01;

        $user_id = Auth::id();
        if(empty($user_id)){
            echo 'User not logged in';
            die;
        }

        $farm_id = \request('farm_id');
        $service_id = \request('service_id');
        $amount = \request('amount');

        //test if amout is valid
        if(empty($amount)){
            $amount = 1;
        }

        $currency = \App\Models\Currency::get();
        foreach ($currency as $c) {
            $currencyArray[$c->id] = ['name'=>$c->name, 'img'=>$c->image];
        }
        $service = \App\Models\Service::find($service_id);
        $resource = Resource::where('id',$service->resource_id)->first();
//        dd($resource->getAttributes());

//        if($resource->type == 'bot'){
        $farm = \App\Models\Farm::find($farm_id);
            //if farm health > 0
            //decrease farm health
        if($farm->health <= 0){
            $data = [
                'status' => 'error',
                'message' => 'target health is 0',
                'extra' => 'target health is 0',
            ];
            return response()->json($data);
        }



        //VALIDATE IF USER can use service
        $claim = false;
        $msg_balance = '';
        $service_use = \App\Models\ServiceUse::where('user_id', $user_id)->where('farm_id', $farm_id)->where('service_id', $service_id)
            ->orderBy('id','desc')->first();
        if(!empty($service_use)){

            //check if not claimed
            if($service_use->claimed_at == null){

                //if time to claim is 0, claim it
                if(strtotime($service_use->created_at->addSeconds($service->time)) -strtotime(now()) > 0 ){
                    $data = [
                        'status' => 'error',
                        'message' => 'item not ready',
                        'extra' => 'service not ready',
                    ];
                    return response()->json($data);
                } else {
                    $claim = true;
                }
            }

            if($service->reload == 0){
                $claimed_at = $service_use->claimed_at;
            } else {
                $claimed_at = $service_use->claimed_at->addSeconds($service->reload);
            }
//            $diff=strtotime($claimed_at)-strtotime(now());


            if(strtotime($claimed_at)-strtotime(now()) > 0){
//                echo 'claim time: '.$service_use->claimed_at.'<br>';
//                echo 'reload time: '.$service->reload.'<br>';
//                echo 'sum :'.$service_use->claimed_at->addSeconds($service->reload).'<br>';
//                echo 'now: '.now().'<br>';
//                echo 'Service not ready';
//                $msg = 'Service not ready';
                $data = [
                    'status' => 'error',
                    'message' => 'Service not ready',
                    'extra' => 'Service not ready',
                ];
                return response()->json($data);
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
                    //need add emty balance record here

                }

                if($balanceArray[$cost['resource']] < $cost['value'] * $amount){
                    $data = [
                        'status' => 'error',
                        'message' => 'Not enough '.$currencyArray[$cost['resource']]['name'].'  balance need '.($cost['value'] * $amount),
                        'extra' => 'Not enough '.$currencyArray[$cost['resource']]['name'].' balance',
//                        'balance' => $balanceArray
                    ];
                    return response()->json($data);
                }
            }
//            echo 'Enough balance<br>';

            //subtract balance
            foreach ($service->cost as $cost){
                $balance = \App\Models\Balance::where('user_id', $user_id)->where('currency_id', $cost['resource'])->first();
                $balance->value = $balance->value - ($cost['value'] * $amount);
                $balance->save();
                $msg_balance .= '-'.($cost['value'] * $amount).' '.$currencyArray[$cost['resource']]['name'].' <br>';
            }
        }

        $health = 0;

        //if time == 0 add balance or claim = true
        if($service->time == 0 || $claim){

            foreach ($service->revenue as $revenue){
                //check level
                if(!empty($revenue['level'])){
//                    $resource = Resource::where('id',$service->resource_id)->first();
                    if(!empty($resource->skill_id)){
                        $skillUser = \App\Models\SkillUser::where('skill_id',$resource->skill_id)->where('user_id', $user_id)->first();
                        if(empty($skillUser)){
//                            echo 'No skill user';
                            continue;
                        }
                        if($skillUser->level < $revenue['level']){
//                            echo 'Level not enough';
                            continue;
                        }
                    }
                }

                //check procents
                if(empty($revenue['percent'])){
//                    print_r($revenue);
//                    echo 'No percent';
                    continue;
                }
                if($revenue['percent'] < 100){
                    $random = rand(1, 100);
                    if($random > $revenue['percent']){
//                        echo 'Random not enough';
                        continue;
                    }
                }
                $balance = \App\Models\Balance::where('user_id', $user_id)->where('currency_id', $revenue['resource'])->first();
                if(empty($balance)){
                    $balance = new \App\Models\Balance();
                    $balance->user_id = $user_id;
                    $balance->currency_id = $revenue['resource'];
                    $balance->value = $revenue['value'] * $amount;
                    $balance->save();
                } else {
                    $balance->value = $balance->value + ($revenue['value'] * $amount);
                    $balance->save();
                }
                $msg_balance .= '+'.($revenue['value'] * $amount).' '.$currencyArray[$revenue['resource']]['name'].' <br>';

                //land owner
//                $land_owner_id = 0;
//                $farm = \App\Models\Farm::find($farm_id);
//                $land = \App\Models\Land::find($farm->land_id);
//                if($land->owner_id){
//                    $land_owner_id = $land->owner_id;
//                }
//                if($land_owner_id){
//                    $balance = \App\Models\Balance::where('user_id', $land_owner_id)->where('currency_id', $revenue['resource'])->first();
//                    if(empty($balance)){
//                        $balance = new \App\Models\Balance();
//                        $balance->user_id = $land_owner_id;
//                        $balance->currency_id = $revenue['resource'];
//                        $balance->value = ($revenue['value'] * $amount * $land_owner_percentage);
//                        $balance->save();
//                    } else {
////                        dd($balance->value + ($revenue['value'] * $land_owner_percentage));
//                        $balance->value = $balance->value + ($revenue['value'] * $amount * $land_owner_percentage);
////                        dd($balance->value);
//                        $balance->save();
//                    }
//                }

            }

            //add experience
            if(!empty($service->xp)){
//                echo "xp yes".$service->xp.'<br>';
//                $resource = Resource::where('id',$service->resource_id)->first();
                if(!empty($resource->skill_id)){
//                    echo "resource->skill_id: ".$resource->skill_id.'<br>';
                    $skillUser = \App\Models\SkillUser::where('skill_id',$resource->skill_id)->where('user_id', $user_id)->first();
                    if(empty($skillUser)){
                        $skillUser = new \App\Models\SkillUser();
                        $skillUser->skill_id = $resource->skill_id;
                        $skillUser->user_id = $user_id;
                        $skillUser->level = 0;
                        $skillUser->xp = $service->xp;
                        $skillUser->save();
//                        print_r($skillUser->toArray());
                    } else {
                        $skillUser->xp += $service->xp;
                        $skillUser->save();
                    }

                    //recalculate level ????
                    //check if level up
//                    $skilllevel = \App\Models\SkillLevel::where('skill_id',$resource->skill_id)->where('level', $skillUser->level+1)->first();
//                    if(!empty($skilllevel)) {
                    $skilllevel = \App\Models\SkillLevel::where('skill_id', null)
                        ->where('level', $skillUser->level + 1)->first();
//                    }
                    if(!empty($skilllevel)){
//                        echo $skillUser->xp .' '
                        if($skillUser->xp >= $skilllevel->xp_required){
                            $skillUser->level += 1;
                            $skillUser->xp = $skillUser->xp - $skilllevel->xp_required;
                            $skillUser->save();
                        }
                    }

                }


            }


            //if attack service
            if($resource->type == 'bot'){
                $farm = \App\Models\Farm::find($farm_id);
                //if farm health > 0
                //decrease farm health
                if($farm->health > 0){
                    $farm->health=$farm->health - $service->damage;
                    if($farm->health < 0){
                        $farm->health=0;
                    }

//                    if($farm->health > 0 && !$farm->state){
//                        $farm->state = 'attack';
//                        $farm->target_id = $user_id;
//                    }

                    $farm->save();
                    //get big price
                    if($farm->health <= 0){
                        $msg_balance .= add_revenue($user_id, $resource->revenue, $resource->skill_id, $currencyArray, 1);
                    }

                } else {
                    //target ded
                    $data = [
                        'status' => 'error',
                        'message' => 'Target not live',
                        'extra' => 'Target not live',
                        'health' => 0,
//                        'balance' => $balanceArray
                    ];

//        return Redirect::route('dashboard')->with('status', 'balance-updated');
                    return response()->json($data);


                }
            }
            $health = $farm->health;
//            echo 'Farm size: '.$farm->size.'<br>';




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
//                echo 'Service claimed<br>';
                $msg = 'Service claimed';
            } else {
//                echo 'Service started<br>';
                $msg = 'Service started';
            }
            $service_use->save();
        } else {
            $service_use->claimed_at = now();
            $service_use->save();
//            echo 'Service claimed<br>';
            $msg = 'Service claimed';
//            die;
        }



        $balanceArrayHtml = $this->Apibalance($currencyArray);


        $data = [
            'status' => 'success',
            'message' => $msg,
            'extra' => $msg_balance,
            'health' => $health,
            'balance' => $balanceArrayHtml,
            'user_health' => Balance::where('user_id', $user_id)->where('currency_id', 25)->first()->value,
        ];

//        return Redirect::route('dashboard')->with('status', 'balance-updated');
        return response()->json($data);
    }


}




function add_revenue($user_id, $revenues , $skill_id, $currencyArray, $amount){
    //check level
    $msg_balance = '';

//    dd($revenues);

    foreach ($revenues as $revenue){
        //check level
        if(!empty($revenue['level'])){
            if(!empty($resource->skill_id)){
                $skillUser = \App\Models\SkillUser::where('skill_id',$skill_id)->where('user_id', $user_id)->first();
                if(empty($skillUser)){
//                            echo 'No skill user';
                    continue;
                }
                if($skillUser->level < $revenue['level']){
//                            echo 'Level not enough';
                    continue;
                }
            }
        }

        //check procents
        if(empty($revenue['percent'])){
//                    print_r($revenue);
//                    echo 'No percent';
            continue;
        }
        if($revenue['percent'] < 100){
            $random = rand(1, 100);
            if($random > $revenue['percent']){
//                        echo 'Random not enough';
                continue;
            }
        }
        $balance = \App\Models\Balance::where('user_id', $user_id)->where('currency_id', $revenue['resource'])->first();
        if(empty($balance)){
            $balance = new \App\Models\Balance();
            $balance->user_id = $user_id;
            $balance->currency_id = $revenue['resource'];
            $balance->value = $revenue['value'] * $amount;
            $balance->save();
        } else {
            $balance->value = $balance->value + ($revenue['value'] * $amount);
            $balance->save();
        }
        $msg_balance .= '+'.($revenue['value'] * $amount).' '.$currencyArray[$revenue['resource']]['name'].' <br>';

        //land owner
//                $land_owner_id = 0;
//                $farm = \App\Models\Farm::find($farm_id);
//                $land = \App\Models\Land::find($farm->land_id);
//                if($land->owner_id){
//                    $land_owner_id = $land->owner_id;
//                }
//                if($land_owner_id){
//                    $balance = \App\Models\Balance::where('user_id', $land_owner_id)->where('currency_id', $revenue['resource'])->first();
//                    if(empty($balance)){
//                        $balance = new \App\Models\Balance();
//                        $balance->user_id = $land_owner_id;
//                        $balance->currency_id = $revenue['resource'];
//                        $balance->value = ($revenue['value'] * $amount * $land_owner_percentage);
//                        $balance->save();
//                    } else {
////                        dd($balance->value + ($revenue['value'] * $land_owner_percentage));
//                        $balance->value = $balance->value + ($revenue['value'] * $amount * $land_owner_percentage);
////                        dd($balance->value);
//                        $balance->save();
//                    }
//                }


    }

    return $msg_balance;
}
