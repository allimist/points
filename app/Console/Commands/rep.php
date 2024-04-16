<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class rep extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zapp:rep';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'add repa , decrease vip days and clear taskbar orders (daily)';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        //add 100 coins

        //add 0.1 repa
        $twoDaysAgo = Carbon::now()->subHours(48);
        \DB::table('users')
            ->where('reputation', '<', 100)
            ->where('active_at', '>=', $twoDaysAgo)
            ->increment('reputation');

        //-1day vip
        \DB::table('balances')
            ->where('currency_id', 13)
            ->where('value', '>', 0)
            ->increment('value', -1);

        //clear taskbar orders
        \DB::table('users')
            ->whereNotNull('task_ids')
            ->update(['task_ids'=>null]);
    }
}
