<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Hot Back Then</title>

    </head>
    <body>
        @foreach ($trends as $trend)
            <p>{{ $trend->name }}</p>
        @endforeach
    </body>
</html>
