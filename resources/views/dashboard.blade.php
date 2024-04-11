

<?php
//  dd($status);

$user_id = Auth::user()->id;

$currency = \App\Models\Currency::get();
foreach ($currency as $c) {
    $currencyArray[$c->id] = $c->name;
}

$balance = \App\Models\Balance::where('user_id', $user_id)->get();
foreach ($balance as $b) {
    $balanceArray[$b->currency_id] = $b->value;
}

//foreach ($currencyArray as $key => $value) {
//    if(!empty($balanceArray[$key])) {
//        echo $value.':'.$balanceArray[$key].' | ';
//    }
//}

$heroland = \App\Models\Land::where('id', Auth::user()->land_id)->first();
//dd($land->image);

?>





<x-app-layout>
    <x-slot name="header">
{{--        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">--}}
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight" style="text-align: center">
            {{ __('Dashboard') }}
{{--            <span id="show" class="btn btn-info" >Show</span> |--}}
{{--            <span id="hide" class="btn btn-info" >Hide</span> |--}}
{{--            <a class="btn btn-info" href="/dashboard">Reload</a><br>--}}
{{--            <a class="btn btn-info" href="/play">Play</a><br>--}}

        </h2>

    </x-slot>


    <div id="to_hide" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                    date time now : {{ now() }}<br>

                    hi, {{ Auth::user()->name }} - <a class="btn btn-info" href="/dashboard">Reload</a><br><br>
                    Reputation : {{ Auth::user()->reputation }} |
                    Location : {{ Auth::user()->land_id }} |
                    posx : {{ Auth::user()->posx }} |
                    posy : {{ Auth::user()->posy }} |
                    active_at : {{ Auth::user()->active_at }} <br>

                    <br><br>

                    <a class="btn btn-info" href="/play">Play</a><br>


                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/p5.js/1.4.0/p5.js"></script>--}}

<script>
    let land_id = {{ Auth::user()->land_id }};
    let map = <?php echo json_encode($heroland->image); ?>;
    let posx = {{ Auth::user()->posx }};
    let posy = {{ Auth::user()->posy }};
    let avatar_id = {{ Auth::user()->avatar_id }};
    {{--let farmsArray = <?php echo json_encode($farmsArray); ?>;--}}
    {{--let farmsServiceArray = <?php echo json_encode($farmsServiceArray); ?>;--}}
    {{--let resourceArray = <?php echo json_encode($resourceArray); ?>;--}}
    {{--let avatarsArray = <?php echo json_encode($avatarsArray); ?>;--}}
    {{--let usersArray = <?php echo json_encode($usersArray); ?>;--}}

</script>

{{--<div>--}}
{{--    <h2>Server Time: <span id="serverTime"></span></h2>--}}
{{--    <h2>Message: <span id="serverMessage"></span></h2>--}}
{{--</div>--}}

<script>
        if (!!window.EventSource) {
        // console.log('ok');

        var source = new EventSource("/stream");
        //
        source.onmessage = function(event) {
            // console.log('m');
            // Parse the JSON data
            var data = JSON.parse(event.data);

            // Log the data or use it to update the DOM
            // console.log(data); // Example: { time: "2023-04-01T12:34:56", message: "Hello from Laravel SSE!" }

            // Update the DOM or perform other actions with the data
            // document.getElementById("serverTime").innerText = data.time;
            // document.getElementById("serverMessage").innerText = data.message;

            usersArray = data.usersArray;
        };
    } else {
        console.log("Your browser does not support server-sent events.");
    }
</script>


{{--<script src="v.js"></script>--}}




<script>

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




