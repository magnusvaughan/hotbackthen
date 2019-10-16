<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Hot Back Then</title>

    </head>
    <body>
        @foreach ($trends as $key => $trend)
            <h2>{{ $key }}</h2>
            @foreach ($trend as $craze)
                <p>Hashtag: <strong>{{ $craze['name'] }}</strong>Tweet Volume: <strong>{{ $craze['tweet_volume'] }}</strong></p>
            @endforeach
        @endforeach
    </body>
</html>
