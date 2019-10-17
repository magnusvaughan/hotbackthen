<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Hot Back Then</title>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    </head>

    <body>
        <main role="main" class="flex-shrink-0">
                <div class="container">
                    @if (Route::has('login'))
                        {{-- <div class="top-right links">
                            @auth
                                <a href="{{ url('/home') }}">Home</a>
                            @else
                                <a href="{{ route('login') }}">Login</a>
        
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}">Register</a>
                                @endif
                            @endauth
                        </div> --}}
                    @endif
        
                    @foreach ($trends as $key => $trend)
                        <h2>{{ $key }}</h2>
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Hashtag</th>
                                        <th scope="col">Tweet Volume</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($trend as $craze)
                                    <tr>
                                        <td>{{ $craze['name'] }}</td>
                                        <td>{{ $craze['tweet_volume'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
        </main>
    </body>
</html>
