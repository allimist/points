<?php

$user_id = Auth::user()->id;

$currency = \App\Models\Currency::get();
$currencyArray = [];
$resourceCurrencyArray = [];
foreach ($currency as $c) {
    $currencyArray[$c->id] = ['name'=>$c->name, 'img'=>$c->image , 'resource_id'=>$c->resource_id, 'service_id'=>$c->service_id];
    if($c->resource_id){
        $resourceCurrencyArray[$c->resource_id] = $c->id;
    }
}

//$balance = \App\Models\Balance::where('user_id', $user_id)->get();
$balance = \App\Models\Balance::where('user_id', $user_id)->where('value', '>' , 0)->orderBy('currency_id')->get();
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
    $resourceArray[$r->id] = ['name'=>$r->name, 'size'=>$r->size, 'type'=>$r->type, 'img'=>$r->image,'img_hover'=>$r->image_hover,'health'=>$r->health,'skill_id'=>$r->skill_id];
    if($r->id == 3 || $r->id == 6 || $r->id == 7){ //market , exchange buy , exchange sell
        $resourceArray[$r->id]['amountable'] = true;
    } else {
        $resourceArray[$r->id]['amountable'] = false;
    }
}

$service = \App\Models\Service::get();
$serviceArray = [];
foreach ($service as $s) {
    //$serviceArray[$s->id] = ['name'=>$s->name, 'time'=>$s->time, 'reload'=>$s->reload , 'cost'=>$s->cost , 'revenue'=>$s->revenue];
    $serviceArray[$s->id] = $s->getAttributes();
}


$heroland = \App\Models\Land::where('id', Auth::user()->land_id)->first();
$landType = \App\Models\LandType::where('id', $heroland->type_id)->first();
if(empty($landType->grid)) {
////    dd('no gssdrid');
    $gridSize = 100;
    $grid = [];
    for ($i = 0; $i < $gridSize; $i++) {
        $matrix[$i] = [];
        for ($j = 0; $j < $gridSize; $j++) {
            $matrix[$i][$j] = 0;
        }
    }
    $landType->grid = json_encode($matrix);
}
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

            $farmsArray[$farm->id]['service_id'] = $service->id;

            if($service->time == 0) {
                $farmsArray[$farm->id]['status'] = 'take';
                $farmsArray[$farm->id]['text'] = 'ClaimGETTTTT';
            } else {
                $farmsArray[$farm->id]['status'] = 'start';
                $farmsArray[$farm->id]['text'] = 'Start';
            }
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
        //        foreach ($currencyArray as $key => $value) {
        //            if(!empty($balanceArray[$key]) && $balanceArray[$key] > 0) {
        //                echo '<img class="resources" data-id="'.$key.'" src= "/storage/'.$value['img'].'"> '.floor($balanceArray[$key]).' | ';
        //
        //            }
        //        }
        //        $balance_string = substr($balance_string, 0, -2);
        //        echo $balance_string;
        $serviceUseController = new \App\Http\Controllers\ServiceUseController();
        echo $serviceUseController->Apibalance($currencyArray);

        ?>
    </div>

    <div id="menu" class="unselectable">
        <?php
        if($user_id == 1){
            echo '<a class="btn" href="/admin">A</a> | ';
        }
        ?>
        <a class="btn" href="/dashboard">Q</a> |
        <a class="btn" href="/play">R</a> |
        <button class="btn" onclick="land_go_select()">T</button><br>

        <button id="editor_mode_on" class="btn" onclick="editor_mode(true)">Edit on</button>
        <button id="editor_mode_off" class="btn" onclick="editor_mode(false)">Edit off</button><br>
        <div id="addResource">
            <span id="pickResource">(-Pick-)</span><br>
            <?php
                if($user_id == 1){
                    foreach ($resourceArray as $key => $value) {

                        if(empty($resourceCurrencyArray[$key])) {
//                    echo '<button class="btn addResource" onclick="addResource('.$key.')">'.$value['name'].'</button> | ';
                            echo '<a href=/farm/add?resource_id=' . $key . ' class="btn addResource" >' . $value['name'] . '</a> | ';
                        }
                    }

                    ?>
                    <?php

                }
            ?>
        </div>

        <?php if($user_id == 1){ ?>
        <button id="grid_mode_on" class="btn" onclick="egrid_mode(true)">Grid on</button>
        <button id="grid_mode_off" class="btn grid_mode" onclick="egrid_mode(false)">Grid off</button>
        <button id="grid_mode_allno" class="btn grid_mode" onclick="egrid_all(0)">All no</button>
        <button id="grid_mode_allyes" class="btn grid_mode" onclick="egrid_all(1)">All yes</button>
        <button id="grid_mode_save" class="btn grid_mode" onclick="egrid_save()">Save</button>
        <?php } ?>

    </div>

    <div id="popup" class="popup unselectable">
        <div class="popup-content">
            <span class="closeBtn">&times;</span>
            <p id="popup-text">Loading ...</p>
        </div>
    </div>
</x-app-layout>


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
    let user_id = <?php echo $user_id; ?>;
    let grid = <?php echo $landType->grid; ?>;
    // let edit_mode = false;
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
