<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A worldwide archive of trending tweets">


    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Hot Back Then') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Bungee+Shade|Roboto+Mono&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    {{-- <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css"> --}}

</head>
<body>
    <div id="app">
        <main>
            @yield('content')
        </main>
        <footer>
            @include('includes.footer')
        </footer>
    </div>
    <link href="https://fonts.googleapis.com/css?family=Roboto+Mono&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
    <script src="https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js"></script>
    <script>

        var getUrlParameter = function getUrlParameter(sParam) {
            var sPageURL = window.location.search.substring(1),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                }
            }
        };

        function defer(method) {
            if (window.jQuery) {
                method();
            } else {
                setTimeout(function() { defer(method) }, 50);
            }
        }

        defer(function () {
            var $grid = $('.grid').imagesLoaded( function() {
                $grid.masonry({
                    // set itemSelector so .grid-sizer is not used in layout
                    itemSelector: '.grid-item',
                    // use element for option
                    columnWidth: '.grid-sizer',
                    percentPosition: true,
                    gutter: 0
                });
            });
        });

        $( document ).ready(function() {
            var selectedDate = getUrlParameter('date') || new Date().toISOString().slice(0, 10);
            $("#flatpickr-date-input").flatpickr({
                defaultDate: selectedDate,
                enableTime: false,
                altInput: true,
                altFormat: "J F Y",
                dateFormat: "Y-m-d",
                position: "below center",
                enable: [
                    {
                        from: "2019-10-16",
                        to: "today",
                    }
                ],
                onReady: function(selectedDates, dateStr, instance) {
                    selectedDate = dateStr;   
                },
                onChange: function(selectedDates, dateStr, instance) {
                    selectedDate = dateStr;
                },
            });

            $('#search-submit').click(function() {
                var country =  $( "#location-selector option:selected" ).text().trim();
                if(typeof selectedDate != undefined) {
                    var date = selectedDate;
                }
                window.location.href = 'http://' + window.location.hostname + window.location.pathname + 
                "?country=" + country + '&date=' + date;
            });

        });
    
    </script>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-137553389-3"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-137553389-3');
    </script>
</body>
</html>
