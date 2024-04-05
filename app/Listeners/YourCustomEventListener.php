<?php

namespace App\Listeners;

use App\Events\YourCustomEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class YourCustomEventListener
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
    public function handle(YourCustomEvent $event): void
    {
        //
    }
}
