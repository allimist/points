<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
{{--        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>--}}


        <title>Welcome to Points</title>

        <!-- Fonts -->
{{--        <link rel="preconnect" href="https://fonts.bunny.net">--}}
{{--        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />--}}

        <!-- Styles -->
        <style>
            body {
                font-family: 'figtree', sans-serif;

            }
        </style>
    </head>
    <body class="bg-dark text-white">


{{--        <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">--}}
            @if (Route::has('login'))
                <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="row">
                <div class="col-md-12 text-center">
                    <h1 class="text-center mb-4">Welcome to the World of EternaPoint</h1>
                </div>
                <div class="col-md-5">
                </div>
                <div class="col-md-2">

                <img src="/img/logo.png" alt="EternaPoint" class="w-64 h-auto mx-auto">
                </div>
            </div>
            <br>
            <br>
            <br>
            <div class="row">
                <div class="col-md-2">

                </div>
                <div class="col-md-8">
                    <p class="lead">In the vibrant world of <strong>EternaPoint</strong>, adventure and strategy fuse seamlessly, offering a unique "play to earn" experience that captivates and rewards.</p>

                    <h2>The Legend of EternaPoint</h2>
                    <p>A realm thrived under the watchful eyes of the Guardians, mythical beings with immense power. Yet, darkness loomed on the horizon, seeking to disrupt the balance of EternaPoint.</p>

                    <h2>The Quest Begins</h2>
                    <p>Players enter as Wanderers, collecting points of power to unlock the Guardians' powers and battle The Void's minions. These points serve as a currency, enabling players to trade, save, or spend on magical upgrades.</p>

                    <h2>Play, Earn, and Become a Legend</h2>
                    <p>"EternaPoint" is more than a game; it's a journey of growth, discovery, and real-world rewards. Players enjoy the thrill of exploration and combat, earning tangible benefits in the process.</p>

                    <h2>Join the Adventure</h2>
                    <p>Pre-register now to be among the first to explore "EternaPoint." Join a community of adventurers, strategists, and dreamers. Your quest for glory begins here.</p>

                    <div class="text-center">
                        <a href="/register" class="btn btn-primary mt-3">Register Now</a>
                    </div>
                </div>
            </div>

        <footer class="text-center mt-5">
            2024
        </footer>

    </body>
</html>
