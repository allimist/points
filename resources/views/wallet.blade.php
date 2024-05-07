

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

    .table tr th, .table tr td{
        /*text-align: center;*/
        border: 1px solid #ccc;
        color: #fff;
    }

</style>
<?php

$user_id = Auth::user()->id;

//wallets
$wallets = \App\Models\Wallet::where('user_id', $user_id)->get();
$wallets_connected = $wallets->count();

$points = \App\Models\Balance::where('user_id', $user_id)->where('currency_id', 3)->first()?->value?:0;
$reputation = Auth::user()->reputation?:0;

$withdraws = \App\Models\Withdraw::where('user_id', $user_id)->get();
$deposits = \App\Models\Deposit::where('user_id', $user_id)->get();


?>

<div id="popup" class="popup">
    <div class="popup-content">
        <span class="closeBtn">&times;</span>
        <p id="popup-text"></p>
    </div>
</div>

<x-app-layout>
    <x-slot name="header">
        {{--        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">--}}
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight" style="text-align: center">
            {{ __('Transactions') }}
        </h2>
    </x-slot>


    <div id="to_hide" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <h2>Contract Details</h2><br>
                    <p>Contract chain: <a href="https://dogether.dog/" target="_blank">Dogether (https://dogether.dog)</p>
                    <p>Contract address: <a href="https://explorer.dogether.dog/address/0xf116fa0d0f1407A77393a3d1eb524d99C444170c" target="_blank">0xf116fa0d0f1407A77393a3d1eb524d99C444170c</a></p>
                    <br><hr><br>

                    <button class="btn btn-success" id="connectMetamaskBtn">Connect Metamask</button>

                    @if($wallets_connected > 0)

                        <h2>Withdrawals</h2><br>
{{--                        <p>Withdraw your points to your wallet</p><br>--}}
                        <p>Current Reputation {{$reputation}} (Minimum 10 Reputation)</p>
                        <p>Current Points: {{$points}} (Minimum 10 POINTS)</p>
                        <br>

                        @if($reputation >= 10 && $points >= 10)
                            <p>Withdrawal is possible</p><br>

                            <form method="post" action="/wallet/withdraw">
                            @csrf
                            <label for="wallet_id">Select Wallet</label>
                            <select name="wallet_id" id="wallet_id" required>
                                @foreach($wallets as $wallet)
                                    <option value="{{$wallet->id}}">{{$wallet->address}}</option>
                                @endforeach
                            </select>
                            <label for="amount">Amount ( min 10 POINTS)</label>
                            <input type="number" name="amount" id="amount" min="10" max="{{ $points }}" value="10" required>
                            <button type="submit" class="btn btm-info">Withdraw</button>
                        </form>
                        @else
                            <p>Withdrawal is not possible</p><br>
                        @endif
                        <br><br>
                    @endif


                    @if(!$withdraws->isEmpty())
                        <h2>Withdrawal History</h2><br>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Created At</th>
                                <th>Wallet</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Tx#</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($withdraws as $w)
                                <tr>
                                    <td>{{ $w->id }}</td>
                                    <td>{{ $w->created_at }}</td>
                                    <td>{{ $w->wallet->address }}</td>
                                    <td>{{ $w->amount }}</td>
                                    <td>{{ $w->status }}</td>
                                    <td>
                                        @if(!empty($w->tx_id))
                                            <a href="https://explorer.dogether.dog/tx/{{ $w->tx_id }}" target="_blank">{{ $w->tx_id }}</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif

                    <br><hr><br>
                    <h2>Deposits</h2><br>
{{--                    <p>Deposit your points to your account</p>--}}
                    <p>Deposit address: 0x????</p>
                    <p>Important - Please deposit only from connected wallet</p>
                    <p>Important - Please fill this form after every deposit</p>
                    <form method="post" action="/wallet/deposit">
                        @csrf
                        <label for="wallet_id">Select Wallet</label>
                        <select name="wallet_id" id="wallet_id" required>
                            @foreach($wallets as $wallet)
                                <option value="{{$wallet->id}}">{{$wallet->address}}</option>
                            @endforeach
                        </select>
                        <label for="tx_id">Transaction ID</label>
                        <input type="text" name="tx_id" id="tx_id" required>
                        <button type="submit" class="btn btm-info">Deposit</button>
                    </form>
                    <br><br>


                    @if(!$deposits->isEmpty())
                        <h2>Deposit History</h2><br>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Created At</th>
                                <th>Wallet</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Tx#</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($deposits as $d)
                                <tr>
                                    <td>{{ $d->id }}</td>
                                    <td>{{ $d->created_at }}</td>
                                    <td>{{ $d->wallet->address }}</td>
                                    <td>{{ $d->amount }}</td>
                                    <td>{{ $d->status }}</td>
                                    <td>
{{--                                        @if(!empty($w->tx_id))--}}
                                            <a href="https://explorer.dogether.dog/tx/{{ $d->tx_id }}" target="_blank">{{ $d->tx_id }}</a>
{{--                                        @endif--}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <br><br>

                    @endif


                </div>
            </div>
        </div>
    </div>
</x-app-layout>


<script>

    let wallets_connected  = {!! $wallets_connected !!};

    if(wallets_connected > 0){
        document.getElementById('connectMetamaskBtn').style.display = 'none';
    } else {
        document.getElementById('connectMetamaskBtn').style.display = 'block';
    }

    window.addEventListener('load', async () => {
        const connectMetamaskBtn = document.getElementById('connectMetamaskBtn');

        connectMetamaskBtn.addEventListener('click', async () => {
            if (window.ethereum) {
                try {
                    const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
                    // Metamask is connected
                    alert('Metamask is connected!');

                    // Send the user's Ethereum address to your server using AJAX
                    const address = accounts[0];
                    sendAddressToServer(address);
                } catch (error) {
                    // User denied account access or other error occurred
                    console.error('Error:', error);
                }
            } else {
                // Metamask is not installed
                alert('Please install Metamask to use this feature.');
            }
        });
    });

    // sendAddressToServer('0x000asd');

    function sendAddressToServer(address) {
        const xhr = new XMLHttpRequest();
        const url = '/api/wallet/add'; // Replace with your server endpoint
        const params = `address=${address}`;

        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    console.log('Address sent successfully.');
                    //reload
                    location.reload();
                } else {
                    console.error('Failed to send address:', xhr.statusText);
                }
            }
        }

        xhr.send(params);
    }



</script>



