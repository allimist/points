<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class botattack extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zapp:botsattack';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'bots attack (every 1 minutes)';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        //restore bots
//        $bots = \DB::table('resources')
//            ->where('State', 'bot')->get();

        $bots = \DB::table('farms')
            ->where('state', 'attack')
            ->where('health', '>', 0)
            ->get();

        foreach ($bots as $bot) {

//            dd($bot);

            //get user
//            $user = \DB::table('users')
//                ->where('id', $bot->target_id)
//                ->first();

            $balance = \DB::table('balances')
                ->where('user_id', $bot->target_id)
                ->where('currency_id', 25)
                ->value('value');

            echo 'user health: ' . $balance . '\n';

//            if($balance <= 0){
//                \DB::table('farms')->where('id', $bot->id)
//                    ->update(['state' => 'relax', 'target_id' => null]);
//                echo "Bot " . $bot->id . " target user " . $bot->target_id . " is dead\n";
//                //continue;
//            } else {
//                //attack
//                $damage = rand(1, 10);
//                $user->health -= $damage;
//                if($user->health <= 0){
//                    $user->health = 0;
//                    \DB::table('farms')->where('id', $bot->id)
//                        ->update(['state' => 'relax', 'target_id' => null]);
//                    echo "Bot " . $bot->id . " Kill user " . $user->id . "\n";
//                }
//                \DB::table('users')->where('id', $user->id)
//                    ->update(['health' => $user->health]);
//                echo "Bot " . $bot->id . " attack user " . $user->id . " with damage " . $damage . "\n";
//            }


        }


    }
}
