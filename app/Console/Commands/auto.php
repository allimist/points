<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class auto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zapp:autoplayer';

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

        for($i = 0; $i < 10; $i++) {
            echo 'loop i: ' . $i . "\n";


            //restore bots
            $autoplayers = \DB::table('auto_players')
                ->where('is_active', 1)->get();

            foreach ($autoplayers as $autoplayer) {

                $user_id = $autoplayer->user_id;
                $tasks = json_decode($autoplayer->tasks);
                $step = $autoplayer->step;


                echo 'step: ' . $step . "\n";



                //check time
                if ($autoplayer->next_state_on > \Carbon\Carbon::now()) {
                    echo '+next_state_on: ' . $autoplayer->next_state_on . ' now: ' . \Carbon\Carbon::now() . "\n";
                    continue;
                }
                echo '-next_state_on: ' . $autoplayer->next_state_on . ' now: ' . \Carbon\Carbon::now() . "\n";


                switch ($tasks[$step]->type) {
                    case 0:
                        echo 'move user: ' . $user_id . ' to x: ' . $tasks[$step]->posX . ' y: ' . $tasks[$step]->posY . "\n";

//                        dd($tasks[$step]);
                        \DB::table('users')->where('id', $user_id)
                            ->update([
                                'posx' => $tasks[$step]->posX,
                                'posy' => $tasks[$step]->posY,
                                'active_at' => \Carbon\Carbon::now()
                            ]);
                        break;


                }

                $step++;
                if ($step >= count($tasks)) {
                    $step = 0;
                }

//                dd($tasks[$step]->seconds);

                //$seconds = parse time ($tasks[$step]->seconds)
                $seconds = $this->getSecondsFromString($tasks[$step]->seconds);
//                dd($seconds);


                \DB::table('auto_players')->where('id', $autoplayer->id)
                    ->update([
                        'step' => $step,
                        'next_state_on' => \Carbon\Carbon::now()->addSeconds($seconds)
                    ]);

            }

            sleep(3);
        }


    }


    public function getSecondsFromString($timeString)
    {
        $parts = explode(':', $timeString);

        if (count($parts) != 2) {
            throw new Exception("Invalid time format. Use 'MM:SS'.");
        }

        $minutes = (int) $parts[0];
        $seconds = (int) $parts[1];

        return ($minutes * 60) + $seconds;
    }
}
