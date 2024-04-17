<?php

$user_id = Auth::user()->id;

$currency = \App\Models\Currency::get();
$currencyArray = [];
$resourceCurrencyArray = [];
foreach ($currency as $c) {
    $currencyArray[$c->id] = ['name'=>$c->name, 'img'=>$c->image , 'resource_id'=>$c->resource_id];
    if($c->resource_id){
        $resourceCurrencyArray[$c->resource_id] = $c->id;
    }
}

$balance = \App\Models\Balance::where('user_id', $user_id)->get();
foreach ($balance as $b) {
    $balanceArray[$b->currency_id] = $b->value;
}



//avatar
$avatarsArray = [];
$avatars = \App\Models\Avatar::get();
foreach ($avatars as $avatar) {
    $avatarsArray[$avatar->id] = ['name'=>$avatar->name,'img'=>$avatar->image];
}

$resource = \App\Models\Resource::get();
foreach ($resource as $r) {
    $resourceArray[$r->id] = ['name'=>$r->name, 'size'=>$r->size, 'type'=>$r->type, 'img'=>$r->image,'img_hover'=>$r->image_hover];
    if($r->id == 3 || $r->id == 6){
        $resourceArray[$r->id]['amountable'] = true;
    } else {
        $resourceArray[$r->id]['amountable'] = false;
    }
}

$service = \App\Models\Service::get();
$serviceArray = [];
foreach ($service as $s) {
    $serviceArray[$s->id] = ['name'=>$s->name, 'time'=>$s->time, 'reload'=>$s->reload , 'cost'=>$s->cost , 'revenue'=>$s->revenue];
}


$heroland = \App\Models\Land::where('id', Auth::user()->land_id)->first();
$landType = \App\Models\LandType::where('id', $heroland->type_id)->first();
//dd($landType);
//$heroland->image = $landType->image;

if($heroland->owner_id == $user_id || $user_id == 1){
    $land_owner = true;
} else {
    $land_owner = false;
}


$farms = \App\Models\Farm::where('land_id',Auth::user()->land_id)->orderBy('resource_id')->get();
$farmsArray = [];
//$farmsServiceArray = [];
foreach ($farms as $farm) {
    $farmsArray[$farm->id] = $farm->getAttributes();

    $services = \App\Models\Service::where('resource_id', $farm->resource_id)->get();
    if(sizeof($services) == 1){
        $farmsArray[$farm->id]['single_service'] = true;
    } else {
        $farmsArray[$farm->id]['single_service'] = false;
    }

    $service_use = \App\Models\ServiceUse::where('user_id', $user_id)
        ->where('farm_id', $farm->id)
        ->orderBy('id', 'desc')
        ->first();

    $service_free = true;


    if(!empty($service_use)) {
        $service = \App\Models\Service::where('id', $service_use->service_id)->first();
        if($service_use->claimed_at == null) {
            $service_free = false;
            $diff = strtotime($service_use->created_at->addSeconds($service->time)) - strtotime(now());
            if ($diff > 0) {
                $farmsArray[$farm->id]['service_id'] = $service->id;
                $farmsArray[$farm->id]['status'] = 'in_use';
                $farmsArray[$farm->id]['text'] = $diff;
                $farmsArray[$farm->id]['ready'] = strtotime(now())+$diff;
            } else {
                $farmsArray[$farm->id]['service_id'] = $service->id;
                $farmsArray[$farm->id]['status'] = 'claim';
                $farmsArray[$farm->id]['text'] = 'Claim';
            }
        } else {
            $diff = strtotime($service_use->claimed_at->addSeconds($service->reload)) -strtotime(now());
            if($diff > 0 ){
                $service_free = false;
                $farmsArray[$farm->id]['service_id'] = $service->id;
                $farmsArray[$farm->id]['status'] = 'reload';
                $farmsArray[$farm->id]['text'] = $diff;
                $farmsArray[$farm->id]['ready'] = strtotime(now())+$diff;

            } else {

            }
            //echo '<br>';
        }
    }


    if($service_free){

        foreach ($services as $service) {
//            $farmsServiceArray[$farm->id] = $service->getAttributes();
//            $farmsServiceArray[$farm->id][$service->id] = $service->name;
//                    echo $service->name.' - ['.$service->id.']';
//                    echo ' <a href="/service-use/claim?farm_id='.$farm->id.'&service_id='.$service->id.'">';
//                                    $farmsArray[$farm->id]['status'] = $service->name.' - ['.$service->id.']';
//                                    $farmsArray[$farm->id]['status'] .=  ' <a href="/service-use/claim?farm_id='.$farm->id.'&service_id='.$service->id.'">';
//                                    $farmsArray[$farm->id]['link_url'] .=  ' <a href="/service-use/claim?farm_id='.$farm->id.'&service_id='.$service->id.'">';
//                                    $farmsArray[$farm->id]['link_text'] =  ' <a href="/service-use/claim?farm_id='.$farm->id.'&service_id='.$service->id.'">';

            $farmsArray[$farm->id]['service_id'] = $service->id;


//                    if($service->time > 0) {
//                        echo '(crafting time:'.$service->time.' sec)';
//                    }
//                    if($service->reload > 0) {
//                        echo '[reload time: '.$service->reload.' sec]';
//                    }
            if($service->time == 0) {
//                        echo 'Claim</a>';
                $farmsArray[$farm->id]['status'] = 'take';
                $farmsArray[$farm->id]['text'] = 'ClaimGETTTTT';

            } else {
//                        echo 'Start</a>';
                $farmsArray[$farm->id]['status'] = 'start';
                $farmsArray[$farm->id]['text'] = 'Start';
            }
//                    echo '<br>';
        }
    }


}



//recent activity
$users = \App\Models\User::where('land_id', Auth::user()->land_id)
    ->select('id','name','avatar_id','posx','posy')
//                            ->where('id', '!=', Auth::user()->id)
//                            ->whereDate('active_at', '>=', now()->subMinutes(10))
    ->where('active_at', '>=', now()->subMinutes(15))
    ->get();
$usersArray = [];
foreach ($users as $u) {
    $usersArray[$u->id] = $u->getAttributes();
//            echo $u->name.' - '.$u->reputation.' - '.$u->land_id.' - '.$u->posx.' - '.$u->posy. ' - '.$u->active_at.'<br>';
}
?>


<link href="play.css" rel="stylesheet">

<x-app-layout class="unselectable">

    <div id="balance" class="unselectable">
        <?php
        foreach ($currencyArray as $key => $value) {
            if(!empty($balanceArray[$key]) && $balanceArray[$key] > 0) {
//            echo '<img class="resources" src= "/storage/'.$value['img'].'"> '.$value['name'].':'.$balanceArray[$key].' | ';
                echo '<img class="resources" data-id="'.$key.'" src= "/storage/'.$value['img'].'"> '.$balanceArray[$key].' | ';
            }
        }
        ?>
    </div>


    <div id="menu" class="unselectable">

        <a class="btn" href="/dashboard">Dashboard</a><br>
        <button id="editor_mode_on" class="btn" onclick="editor_mode(true)">Edit On</button><br>
        <button id="editor_mode_off" class="btn" onclick="editor_mode(false)">Edit Off</button><br>
        <div id="addResource">
            <span id="pickResource">(Pick resource)</span><br>
            <?php
            foreach ($resourceArray as $key => $value) {

                if(empty($resourceCurrencyArray[$key])) {
//                    echo '<button class="btn addResource" onclick="addResource('.$key.')">'.$value['name'].'</button> | ';
                    echo '<a href=/farm/add?resource_id=' . $key . ' class="btn addResource" >' . $value['name'] . '</a> | ';
                }
            }
            ?>
        </div>
    </div>


    <div id="popup" class="popup unselectable">
        <div class="popup-content">
            <span class="closeBtn">&times;</span>
            <p id="popup-text"></p>
        </div>
    </div>


{{--    <div class="p-head">--}}
{{--        <div>--}}
{{--            Reputation : {{ Auth::user()->reputation }} |--}}
{{--            Location : {{ '#'.Auth::user()->land_id.' '.$heroland->name }} |--}}
{{--            posx : {{ Auth::user()->posx }} |--}}
{{--            posy : {{ Auth::user()->posy }} |--}}
{{--            active_at : {{ Auth::user()->active_at }} <br>--}}
{{--        </div>--}}

{{--        <div class="actions">--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    <div id="canvasContainer">--}}
{{--        <div id="textOverlay">Your Text Here</div>--}}
{{--    </div>--}}
</x-app-layout>

{{--<div id="canvasContainer"></div>--}}



{{--<div id="popup_land_go" class="popup">--}}
{{--    <div class="popup-content">--}}
{{--        <span class="closeBtn">&times;</span>--}}
{{--        <p id="popup-text">You got 20 wood</p>--}}
{{--    </div>--}}
{{--</div>--}}


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/p5.js/1.4.0/p5.js"></script>

<script>
    let serverTime = {{ strtotime(now()) }};
    let land_id = {{ Auth::user()->land_id }};
    let map = <?php echo json_encode($landType->image); ?>;
    let posx = {{ Auth::user()->posx }};
    let posy = {{ Auth::user()->posy }};
    let avatar_id = {{ Auth::user()->avatar_id }};
    let balanceArray = <?php echo json_encode($balanceArray); ?>;
    let avatarsArray = <?php echo json_encode($avatarsArray); ?>;
    let currencyArray = <?php echo json_encode($currencyArray); ?>;
    let resourceArray = <?php echo json_encode($resourceArray); ?>;
    let serviceArray = <?php echo json_encode($serviceArray); ?>;
    let farmsArray = <?php echo json_encode($farmsArray); ?>;
    {{--let farmsServiceArray = <?php echo json_encode($farmsServiceArray); ?>;--}}
    {{--let farmsServiceArray = <?php echo json_encode($farmsServiceArray); ?>;--}}
    let usersArray = <?php echo json_encode($usersArray); ?>;
    let isPopupVisible = false;
    let land_owner = '<?php echo $land_owner; ?>';
    let edit_mode = false;
    // let edit_mode = localStorage.getItem('edit_mode');
    // // console.log('edit_mode',edit_mode);
    // if(edit_mode==true){
    //     document.getElementById('addResource').style.display = 'block';
    //     document.getElementById('editor_mode_off').style.display = 'block';
    //     document.getElementById('editor_mode_on').style.display = 'none';
    // }
</script>

{{--@push('after_scripts')--}}
<script src="main.js"></script>
<script src="play.js"></script>

{{--@endpush--}}

{{--@push('after_styles')--}}
{{--    <link href="{{ asset('play.css') }}" rel="stylesheet">--}}
{{--@endpush--}}
