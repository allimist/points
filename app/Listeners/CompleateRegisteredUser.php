<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
//use App\Events\Registered;
use App\Models\Balance;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;




class CompleateRegisteredUser
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        //
        $user = $event->user;



        $balance = new \App\Models\Balance();
        $balance->user_id = $user->id;
        $balance->currency_id = 1;
        $balance->value = 1000;
        $balance->save();
//        dd($balance);

    }
}
