<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class go extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zapp:go';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'add energy (every 5 minutes)';

    /**
     * Execute the console command.
     */
    public function handle()
    {

//        //add energy
//        $balances = \App\Models\Balance::where('currency_id', 1)
//            ->where('value', '<', 1000)
//            ->get();
//        foreach ($balances as $balance) {
//            echo $balance->value . '\r\n';
//            $balance->value++;
//            $balance->save();
//        }
//
//        //fix energy
//        $balances = \App\Models\Balance::where('currency_id', 1)
//            ->where('value', '>', 1000)
//            ->get();
//        foreach ($balances as $balance) {
//            echo $balance->value . "\r\n";
//            $balance->value = 1000;
//            $balance->save();
//        }

        //+5 energy every 5 minutes
        \DB::table('balances')
            ->where('currency_id', 1)
            ->where('value', '<', 1000)
            ->increment('value', 5);

        \DB::table('balances')
            ->where('currency_id', 1)
            ->where('value', '>', 1000)
            ->update(['value'=>1000]);

    }
}
