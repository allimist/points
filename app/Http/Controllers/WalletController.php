<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class WalletController extends Controller
{

    public function add()
    {
        $user_id = Auth::id();
        if(empty($user_id)){
            echo 'User not logged in';
            die;
        }
        $wallet = new \App\Models\Wallet();
        $wallet->user_id = $user_id;
        $wallet->address = \request()->address;
        $wallet->save();
        die;
    }

    public function withdraw()
    {
        $user_id = Auth::id();
        if(empty($user_id)){
            echo 'User not logged in';
            die;
        }

        //get wallet
        $wallet = \App\Models\Wallet::where('id', \request()->wallet_id)->where('user_id', $user_id)->first();
        if(empty($wallet)){
            echo 'Wallet not found';
            die;
        }
        //check balance
        $points = \App\Models\Balance::where('user_id', $user_id)->where('currency_id', 3)->first()?->value?:0;
        if($points < \request()->amount){
            echo 'Insufficient balance';
            die;
        }

        //check amount
        $amount = intval(\request()->amount);
        if($amount < 10){
            echo 'Minimum withdraw amount is 10';
            die;
        }

        //decrease points
        $balance = \App\Models\Balance::where('user_id', $user_id)->where('currency_id', 3)->first();
        $balance->value = $balance->value - $amount;
        $balance->save();

        //add withdraw request
        $withdraw = new \App\Models\Withdraw();
        $withdraw->user_id = $user_id;
        $withdraw->wallet_id = $wallet->id;
        $withdraw->amount = $amount;
        $withdraw->status = 'pending';
        $withdraw->save();

        return Redirect::back()->with('success', 'Withdraw request submitted successfully');
    }

    public function deposit()
    {
        $user_id = Auth::id();
        if(empty($user_id)){
            echo 'User not logged in';
            die;
        }

        //get wallet
        $wallet = \App\Models\Wallet::where('id', \request()->wallet_id)->where('user_id', $user_id)->first();
        if(empty($wallet)){
            echo 'Wallet not found';
            die;
        }

        //add deposit request
        $deposit = new \App\Models\Deposit();
        $deposit->user_id = $user_id;
        $deposit->wallet_id = \request()->wallet_id;
        $deposit->status = 'pending';
        $deposit->tx_id = \request()->tx_id;
        $deposit->save();

        return Redirect::back()->with('success', 'Deposit request submitted successfully');
    }


}
