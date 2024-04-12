

<style>
    .avatars li{
        width: 25%;
        float: left;
        text-align: center;
    }
    .avatar_img{
        width: 150px;
        height: 150px;
        margin: 0 auto;
    }

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

$user_id = Auth::user()->id;

//avatar
$avatarsArray = [];
$avatars = \App\Models\Avatar::get();
foreach ($avatars as $avatar) {
    $avatarsArray[$avatar->id] = ['name'=>$avatar->name,'img'=>$avatar->image];
}
//dd($avatarsArray);
$status = '';
if(!empty(\request()->avatar_id)){
    $avatar_id = \request()->avatar_id;
    Auth::user()->avatar_id = $avatar_id;
    Auth::user()->save();
    $status = 'Avatar updated';

//    echo '<div class="alert alert-info">'.$status.'</div>';
    echo '<style>.popup {display: block;}</style>';
}


?>


<div id="popup" class="popup">
    <div class="popup-content">
        <span class="closeBtn">&times;</span>
        <p id="popup-text">{{$status}}</p>
    </div>
</div>

<x-app-layout>
    <x-slot name="header">
{{--        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">--}}
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight" style="text-align: center">
            {{ __('Avatar') }}
        </h2>
    </x-slot>


    <div id="to_hide" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="get" action="/avatar">
                        <ul class="avatars">
                          <?php
                            $avatar_id = Auth::user()->avatar_id;
                            foreach ($avatarsArray as $key => $value) {
                                if($key == 1 && Auth::user()->id != 1){
                                    continue;
                                }
                                $selected = '';
                                if($key == $avatar_id){
                                    $selected = 'checked';
                                }
                                echo '<li>';
                                echo '<img src="'.$value['img'].'" class="avatar_img alt="'.$value['name'].'" title="'.$value['name'].'">';
                                echo '<input type="radio" name="avatar_id" value="'.$key.'" '.$selected.'>';
                                echo '</li>';
                            }
                            ?>
                        </ul>
                        <input type="submit" value="Select" class="btn btn-info">
                    </form>

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



