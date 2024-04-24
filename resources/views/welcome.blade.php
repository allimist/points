<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Points</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

{{--<x-guest-layout>--}}

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Points</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
    <ul class="navbar-nav">
        @if (Route::has('login'))
            @auth
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/dashboard') }}">Dashboard</a>
                    <a class="nav-link" href="{{ url('/play') }}">Play</a>
                    <a class="nav-link" href="{{ url('/profile') }}">Profile</a>
                    <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                </li>
                @if (Route::has('register'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    </li>
                @endif
            @endauth
        @endif
    </ul>
    </div>
</nav>

<div class="container mt-4">
    <div class="jumbotron">
        <h1 class="display-4">Welcome to Points!</h1>
        <p class="lead">
            Embark on an adventurous journey in an old world filled with diverse scenarios like deserts, forests, snow-covered landscapes, mountains, and more. Discover, craft, and trade your way to success!
        </p>
        <img src="/img/logo.png" alt="EternaPoint" class="w-64 h-auto mx-auto">
        <hr class="my-4">

        <p><strong>Game Features:</strong></p>
        <ul>
            <li>Explore vast landscapes including forests, deserts, mountains, and more, each offering unique resources and items.</li>
            <li>Engage in crafting and trading with the game's token, Points, to enhance your experience.</li>
            <li>Utilize different modes of transport like horse carriages and boats to navigate through diverse scenarios.</li>
            <li>Interact in a dynamic market to buy and sell items and resources. Open your market to other players or visit theirs for trade.</li>
            <li>Embark on quests for rewards and climb the leaderboard to showcase your prowess.</li>
            <li>Connect with other players through in-game chat for an enriched social experience.</li>
            <li>Use Points for various in-game activities, including accessing special scenarios.</li>

        </ul>

        <div class="text-center">
            <a href="/register" class="btn btn-primary mt-3">Register Now</a>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
{{--</x-guest-layout>--}}
