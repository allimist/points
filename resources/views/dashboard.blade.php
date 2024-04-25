

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

$skill = \App\Models\Skill::orderBy('name')->get();
$skillLevel = \App\Models\SkillLevel::get();
$skillLevelArray = [];
foreach ($skillLevel as $sl) {
    $skillLevelArray[$sl->level] = $sl->xp_required;
}
//dd($skillLevelArray);
$skillUser = \App\Models\SkillUser::where('user_id', $user_id)->get();
$skillUserArray = [];
foreach ($skillUser as $su) {
    $skillUserArray[$su->skill_id] = $su->getAttributes();
}
//dd($skill);
?>

<style>
    .skills th, .skills td {
        padding: 5px;
        text-align: center;
        border: 1px solid black;
        /*border-radius: 5px;*/
        color: #e5e7eb;
        background-color: #0d251e;
    }
    .skills td img{
        background-color: whitesmoke;
        margin: 0 auto;
    }
    .btn-info{
        background-color: #17a2b8;
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        text-decoration: none;

    }
</style>



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
{{--                    date time now : {{ now() }}<br>--}}

                    hi, {{ Auth::user()->name }}<br><br>
{{--                    - <a class="btn btn-info" href="/dashboard">Reload</a><br><br>--}}
                    Reputation : {{ Auth::user()->reputation }} |
                    Location : {{ Auth::user()->land_id }} |
                    posx : {{ Auth::user()->posx }} |
                    posy : {{ Auth::user()->posy }} |
                    active_at : {{ Auth::user()->active_at }} <br>

                    <br>

                    <table class="skills">
                        <tr>
                            <th>Skill</th>
                            <th>Level</th>
                            <th>XP</th>
                            <th>Next Level</th>
                            <th>Missing</th>
                        </tr>
                        <?php
                        foreach ($skill as $s) {
//                            dd($s);
                            if(!empty($skillUserArray[$s->id])) {
                                echo '<tr>';
                                echo '<td>';
                                echo '<img src="/storage/'.$s->image.'" style="width: 50px; height: 50px;">';
                                echo $s->name.'</td>';
                                echo '<td>'.$skillUserArray[$s->id]['level'].'</td>';
                                echo '<td>'.$skillUserArray[$s->id]['xp'].'</td>';
                                echo '<td>'.$skillLevelArray[$skillUserArray[$s->id]['level']+1].'</td>';
                                echo '<td>'.($skillLevelArray[$skillUserArray[$s->id]['level']+1] - $skillUserArray[$s->id]['xp']).'</td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                    </table>
                    <br>

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




