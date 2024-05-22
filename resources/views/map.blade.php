

<style>
    table td{
        border: 1px solid;
    }
    .floatleft{
        float: left;
        /*margin-right: 10px;*/

    }


</style>
<?php
//  dd($status);

$user_id = Auth::user()->id;

//landTypes
$landTypes = DB::table('land_types')->get();
$landTypesArray = [];
foreach($landTypes as $landType){
    $landTypesArray[$landType->id] = $landType;
}


//lands
$lands = DB::table('lands')->orderBy('posx')->orderBy('posy')->get();
$landsArray = [];
foreach($lands as $land){
    if($land->posx && $land->posy){
        $landsArray[$land->posx][$land->posy] = $land;
    }
}


?>


{{--<div id="popup" class="popup">--}}
{{--    <div class="popup-content">--}}
{{--        <span class="closeBtn">&times;</span>--}}
{{--        <p id="popup-text">{{$status}}</p>--}}
{{--    </div>--}}
{{--</div>--}}

<x-app-layout>
    <x-slot name="header">
{{--        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">--}}
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight" style="text-align: center">
            {{ __('Avatar') }}
        </h2>
    </x-slot>


    <div id="to_hide" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <td class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <tr class="p-6 text-gray-900 dark:text-gray-100">

                    <table>
                        <tr>
                            <td></td>
                            @for($y=0; $y<10; $y++)
                                <td>{{$y}}</td>
                            @endfor
                        </tr>
                        @for($y=0; $y<10; $y++)
                        <tr>
                            <td>{{$y}}</td>
                            @for($x=0; $x<10; $x++)
                                <td>
                                    @if(isset($landsArray[$x][$y]))
                                        #{{$landsArray[$x][$y]->id}}<br>
                                        {{$landsArray[$x][$y]->name}} ({{$landsArray[$x][$y]->posx}},{{$landsArray[$x][$y]->posy}})
                                        <img src="/storage/{{$landTypesArray[$landsArray[$x][$y]->type_id]->image}}" width="100">

                                    @else
                                        -
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @endfor
                    </table>
                </div>

                <hr>

                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @foreach($lands as $land)
                        @if(empty($land->posx) || empty($land->posy))
                        <div class="card">
                            <div>
                                <div class="floatleft">
                                    #{{$land->id}} {{$land->name}} ({{$land->posx}},{{$land->posy}})<br>
                                    <a href="/map/{{$land->id}}">View</a>
                                    <img src="/storage/{{$landTypesArray[$land->type_id]->image}}" width="100">
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>--}}


<script>
    document.getElementsByClassName('closeBtn')[0].onclick = function() {
        document.getElementById('popup').style.display = 'none';
        isPopupVisible = false;
    }
</script>



