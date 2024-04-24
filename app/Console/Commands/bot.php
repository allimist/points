<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class bot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zapp:bots';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'restore bots (every 1 minutes)';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        //restore bots
        $bots = \DB::table('resources')
            ->where('type', 'bot')->get();

        foreach ($bots as $bot) {

            \DB::table('farms')->where('resource_id', $bot->id)
                ->whereBetween('health', [1, $bot->health-1])
                ->increment('health', 1);

            //chack reload time and restore health

            //date + reload time
            $date = \Carbon\Carbon::now();
            $date->addSeconds(-$bot->reload);


            $updated = \DB::table('farms')->where('resource_id', $bot->id)
                ->where('health', 0)
                ->where('updated_at', '<', $date)
                ->update(['health' => $bot->health]);

            echo 'updated: '.$updated."\n";

        }


    }
}
