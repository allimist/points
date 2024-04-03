
<style>
    .btn,a{color: lightblue !important;}
    #defaultCanvas0 {margin: 0 auto;}
</style>
<style>
    /* Style.css */
    .popup {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0,0,0,0.4);
    }

    .popup-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 450px;
    }

    .closeBtn {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .closeBtn:hover,
    .closeBtn:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

</style>


<?php
//  dd($status);

?>


<x-app-layout>
    <x-slot name="header">
{{--        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">--}}
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight" style="text-align: center">
{{--            {{ __('Dashboard') }}--}}
            <span id="show" class="btn btn-info" >Show</span> |
            <span id="hide" class="btn btn-info" >Hide</span> |
            <a class="btn btn-info" href="/dashboard">Reload</a><br>
        </h2>

    </x-slot>


    <div id="to_hide" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}

                    hi, {{ Auth::user()->name }} - <a class="btn btn-info" href="/dashboard">Reload</a><br><br>
                    Reputation : {{ Auth::user()->reputation }} |
                    Location : {{ Auth::user()->land_id }} |
                    posx : {{ Auth::user()->posx }} |
                    posy : {{ Auth::user()->posy }} |
                    active_at : {{ Auth::user()->active_at }} <br>

                    <?php
                        $user_id = Auth::user()->id;

                        $resource = \App\Models\Resource::get();
                        foreach ($resource as $r) {
                            $resourceArray[$r->id] = $r->name;
                        }

                        $currency = \App\Models\Currency::get();
                        foreach ($currency as $c) {
                            $currencyArray[$c->id] = $c->name;
                        }

                        $balance = \App\Models\Balance::where('user_id', $user_id)->get();
                        foreach ($balance as $b) {
                            $balanceArray[$b->currency_id] = $b->value;
                        }

                        $lands = \App\Models\Land::get();
                        foreach ($lands as $land) {
                            $landArray[$land->id] = $land->name;
                        }

                        foreach ($currencyArray as $key => $value) {
                            if(!empty($balanceArray[$key])) {
                                echo $value.':'.$balanceArray[$key].' | ';
                            }
                        }
                    ?>
                    <br><br>

                    Lands:<br>

                    <?php
                        foreach ($lands as $land) {
                            if($land->id == Auth::user()->land_id) {
                                echo $land->name.' - ['.$land->id.']<br>';
                            } else {
                                echo '<a href="/land/go?id='.$land->id.'">'.$land->name.' - ['.$land->id.'] </a><br>';
                            }
                        }
                    ?>
                    <br><br>

                    Farms:<br>
                    <?php


                        $farms = \App\Models\Farm::where('land_id',Auth::user()->land_id)->orderBy('resource_id')->get();
                        foreach ($farms as $farm) {
                            $farmsArray[$farm->id] = $farm->getAttributes();
                            echo $resourceArray[$farm->resource_id].'['.$farm->id.']<br>';

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
                                        echo ' - in progress - diff:' . $diff . ' ' . date("H:i:s", $diff).'<br>';
                                        $farmsArray[$farm->id]['service_id'] = $service->id;
                                        $farmsArray[$farm->id]['status'] = 'in_use';
                                        $farmsArray[$farm->id]['text'] = $diff;
                                    } else {
                                        echo $service->name.' - ['.$service->id.']';
                                        echo ' <a href="/service-use/claim?farm_id=' . $farm->id . '&service_id=' . $service->id . '">Claim</a><br>';
                                        $farmsArray[$farm->id]['service_id'] = $service->id;
                                        $farmsArray[$farm->id]['status'] = 'claim';
                                        $farmsArray[$farm->id]['text'] = 'Claim';
                                    }
                                } else {
                                    $diff = strtotime($service_use->claimed_at->addSeconds($service->reload)) -strtotime(now());
                                    if($diff > 0 ){
                                        echo $service->name.' - ['.$service->id.']';
                                        echo ' - in reload - diff:'.$diff.' '.date("H:i:s", $diff);
                                        $service_free = false;
                                        $farmsArray[$farm->id]['service_id'] = $service->id;
                                        $farmsArray[$farm->id]['status'] = 'reload';
                                        $farmsArray[$farm->id]['text'] = $diff;
                                    } else {

                                    }
                                    //echo '<br>';
                                }
                            }

                            if($service_free){
                                $services = \App\Models\Service::where('resource_id', $farm->resource_id)->get();
                                $serviceArray = [];
                                foreach ($services as $service) {
                                    $farmsServiceArray[$farm->id] = $service->getAttributes();
                                    $serviceArray[$service->id] = $service->name;
                                    echo $service->name.' - ['.$service->id.']';
                                    echo ' <a href="/service-use/claim?farm_id='.$farm->id.'&service_id='.$service->id.'">';
//                                    $farmsArray[$farm->id]['status'] = $service->name.' - ['.$service->id.']';
//                                    $farmsArray[$farm->id]['status'] .=  ' <a href="/service-use/claim?farm_id='.$farm->id.'&service_id='.$service->id.'">';
//                                    $farmsArray[$farm->id]['link_url'] .=  ' <a href="/service-use/claim?farm_id='.$farm->id.'&service_id='.$service->id.'">';
//                                    $farmsArray[$farm->id]['link_text'] =  ' <a href="/service-use/claim?farm_id='.$farm->id.'&service_id='.$service->id.'">';

                                    $farmsArray[$farm->id]['service_id'] = $service->id;


                                    if($service->time > 0) {
                                        echo '(crafting time:'.$service->time.' sec)';
                                    }
                                    if($service->reload > 0) {
                                        echo '[reload time: '.$service->reload.' sec]';
                                    }
                                    if($service->time == 0) {
                                        echo 'Claim</a>';
                                        $farmsArray[$farm->id]['status'] = 'claim';
                                        $farmsArray[$farm->id]['text'] = 'Claim';

                                    } else {
                                        echo 'Start</a>';
                                        $farmsArray[$farm->id]['status'] = 'start';
                                        $farmsArray[$farm->id]['text'] = 'Start';
                                    }
                                    echo '<br>';
                                }
                            }


                        }
                    ?>



                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/p5.js/1.4.0/p5.js"></script>

<script>
    let land_id = {{ Auth::user()->land_id }};
    let posx = {{ Auth::user()->posx }};
    let posy = {{ Auth::user()->posy }};
    let farmsArray = <?php echo json_encode($farmsArray); ?>;
    let farmsServiceArray = <?php echo json_encode($farmsServiceArray); ?>;
</script>
<script src="test6.js"></script>

<button id="showPopupBtn">Get Wood</button>
<div id="popup" class="popup">
    <div class="popup-content">
        <span class="closeBtn">&times;</span>
        <p id="popup-text">You got 20 wood</p>
    </div>
</div>


<script>
    // setTimeout(function(){ window. location. reload(); }, 7000);
    // script.js
    document.getElementById('showPopupBtn').onclick = function() {
        document.getElementById('popup').style.display = 'block';
    }

    document.getElementsByClassName('closeBtn')[0].onclick = function() {
        document.getElementById('popup').style.display = 'none';
    }

    // Close the popup if the user clicks anywhere outside of it
    window.onclick = function(event) {
        if (event.target == document.getElementById('popup')) {
            document.getElementById('popup').style.display = 'none';
        }
    }

    $('#hide').click(function(){
        $('#to_hide').hide();
        //save state to local storage
        localStorage.setItem('to_hide', 'hide');

    });
    $('#show').click(function(){
        $('#to_hide').show();
        //save state to local storage
        localStorage.setItem('to_hide', 'show');
    });

    //get state from local storage
    var state = localStorage.getItem('to_hide');
    if(state == 'hide'){
        $('#to_hide').hide();
    }else{
        $('#to_hide').show();
    }

</script>



