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
        $balance = \App\Models\Balance::where('user_id', $user_id)->get();
        $balance_string = '';
        foreach ($balance as $b) {
//            $balanceArray[$b->currency_id] = $b->value;
            $balance_string .= $b->value . ' ' . $currencyArray[$b->currency_id] . ' | ';
        }
//        return $balanceArray;
        return $balance_string;

    }

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
        if(empty($amount)){
            $amount = 1;
        }


//        echo 'user id: '. $user_id.'<br>';
//        echo 'farm id: '.$farm_id.'<br>';
//        echo 'service id: '. $service_id.'<br>';

        $currencies = \App\Models\Currency::all();
        foreach ($currencies as $currency){
            $currencyArray[$currency->id] = $currency->name;
        }
        $service = \App\Models\Service::find($service_id);


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
            foreach ($service->cost as $cost){

                if(empty($balanceArray[$cost['resource']])){
                    $balanceArray[$cost['resource']] = 0;
                    //need add emty balance record here

                }

//                echo $currencyArray[$cost['resource']].' Cost: '.$cost['value'].' have: '.$balanceArray[$cost['resource']].'<br>';
                if($balanceArray[$cost['resource']] < $cost['value'] * $amount){
//                    echo 'Not enough '.$currencyArray[$cost['resource']].' balance';
//                    $msg = 'Not enough '.$currencyArray[$cost['resource']].' balance';
//                    $validBalance = false;
//                    die;
                    $data = [
                        'status' => 'error',
                        'message' => 'Not enough '.$currencyArray[$cost['resource']].'  balance nned '.($cost['value'] * $amount),
                        'extra' => 'Not enough '.$currencyArray[$cost['resource']].' balance',
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
                $msg_balance .= '-'.($cost['value'] * $amount).' '.$currencyArray[$cost['resource']].' <br>';
            }
        }


        //if time == 0 add balance or claim = true
        if($service->time == 0 || $claim){



            foreach ($service->revenue as $revenue){
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
                $msg_balance .= '+'.($revenue['value'] * $amount).' '.$currencyArray[$revenue['resource']].' <br>';


                //land owner
                $land_owner_id = 0;
                $farm = \App\Models\Farm::find($farm_id);
//            dd($farm->land_id);
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

    public function ApiSelect()
    {

//        $currencies = \App\Models\Currency::all();
//        foreach ($currencies as $currency) {
//            $currencyArray[$currency->id] = $currency->name;
//        }
//        $this->balance($currencyArray);

        $farm = \App\Models\Farm::find(\request('farm_id'));
        $services = \App\Models\Service::where('resource_id', $farm->resource_id)->get();

        $data = [
            'services' => $services
        ];

//        return Redirect::route('dashboard')->with('status', 'balance-updated');
        return response()->json($data);

    }


    //sell
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

    public function orders()
    {

        $currencies = \App\Models\Currency::all();
        foreach ($currencies as $currency){
            $currencyArray[$currency->id] = $currency->name;
        }
        $this->balance($currencyArray);

        $farm = \App\Models\Farm::find(\request('farm_id'));
        $resource = \App\Models\Resource::find($farm->resource_id);
        $service = \App\Models\Service::find(\request('service_id'));

        echo '<br><br>';
        echo '<h1>'.$resource->name.'</h1>';
        echo '<br><br>';


//        dd($service);
//        $orders = \App\Models\Order::where('service_id', $service->id)->where('type', 'sell')->get();
//        $orders = \App\Models\Order::where('service_id', $service->id)->get();
        $orders = \App\Models\Order::where('service_id', 14)->get();
        foreach ($orders as $order){

            echo $order->id.' Price: '.$order->price.' Amount: '.$order->amount;
            echo ' <a href="/service-use/select-order?order_id='.$order->id.'">Select Order</a><br>';

//            echo '<form action="/service-use/buy" method="get">';
//            echo '<input type="hidden" name="order_id" value="'.$order->id.'">';
//            echo 'Amount:<input type="number" id="amount" name="amount" value="2" min="1" max="1000"> ';
//            echo 'Price: '.$order->price.' Amount: '.$order->amount.' ';
//            echo '<input type="submit" value="Buy">';
//            echo '</form>';



        }

        //back link
        echo '<a href ="/dashboard">Back</a>';

        die;
//        return view('service-use.select', ['services' => $services]);
    }

    public function selectOrder()
    {

        $currencies = \App\Models\Currency::all();
        foreach ($currencies as $currency){
            $currencyArray[$currency->id] = $currency->name;
        }
        $this->balance($currencyArray);

        $order = \App\Models\Order::find(\request('order_id'));
        echo '<br>Order id: '.$order->id.'<br>';
        echo 'Price: '.$order->price.'<br>';
        echo 'Amount: '.$order->amount.'<br>';

        echo '<form action="/service-use/buy" method="get">';
        echo '<input type="hidden" name="order_id" value="'.$order->id.'">';
        echo 'Amount:<input type="number" id="amount" name="amount" value="'.$order->amount.'" min="1" max="1000"> ';
        echo '<input type="submit" value="Buy">';
        echo '</form>';

        die;
//        return view('service-use.select', ['services' => $services]);
    }

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


        echo "ok";
        echo '<a href ="/service-use/orders?farm_id=18&service_id='.$service->id.'">Back</a>';
        echo 1; die;

        //back link
//        return Redirect::route('dashboard')->with('status', 'balance-updated');
//        return Redirect::route('dashboard')->with('status', 'balance-updated');




    }


}
