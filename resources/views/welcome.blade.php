@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="nav-wrapper my-2">
        <a href="/">
            <h1>Hot Back Then</h1>
            <div class="prompt">A worldwide archive of trending tweets</div>
        </a>
        <div class="left-nav-wrapper">
        {{-- <h1>{{$is_mobile}}</h1> --}}
            <input class="flatpickr flatpickr-input" id="flatpickr-date-input" class="datepicker-text" />
            <select id="location-selector" class="select-css ml-2">
                @foreach ($locations as $location)
                    <option value="{{ $location->location }}" 
                        {{(trim($location->location) == $current_location ? 'selected' : '')}}>
                        {{ $location->location }} 
                    </option>
                @endforeach
            </select>
            <button id="search-submit" class="btn btn-light">Search</button>
        </div>
    </div>

    {{-- <h1>{{$image_count}}</h1> --}}

    @if(count($trends) <= 0)
        <div class="no-trends">
            <h1 class="no-trends-text">No trends for this location at this time.</h1>
            <h2 class="no-trends-text">Please choose another.</h1>
        </div>
    @else
        <div class="grid">
            <div class="grid-sizer"></div>
            @foreach ($trends as $key => $craze)
                <div class="grid-item">
                    <img src={{$craze['image_url']}} class="img-fluid" alt="">
                    <div class="text-overlay-wrapper">
                        <a class="craze-link" target="__blank" href="{{$craze['url']}}">
                            {{ trim($craze['name']) }}
                            <div class="craze-volume">
                                {{ $craze['tweet_volume'] != "Unknown" ? $craze['tweet_volume'] . " tweets" : ""}}</div>
                        </a>
                    </div>
                    <div class="credit-overlay-wrapper">
                        <a class="craze-credit-link" href="{{ $craze['image_html_url'] }}">photo by {{ $craze['image_username'] }}</a>
                    </div>
                </div> 
            @endforeach
        </div>
    @endif
</div>
@endsection
