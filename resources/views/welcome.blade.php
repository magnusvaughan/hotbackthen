@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="nav-wrapper my-2">
        <h1>Hot Back Then</h1>
        <div class="left-nav-wrapper">
            <div class="prompt">Select a country to see its trending hashtags</div>
            <select id="location-selector" class="select-css ml-2">
                @foreach ($locations as $location)
                    <option value="{{ $location->location }}">{{ $location->location }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid">
        <div class="grid-sizer"></div>
        @foreach ($trends as $craze)
            <div class="grid-item">
                <img src={{$craze['image_url']}} class="img-fluid" alt="">
                <div class="text-overlay-wrapper">
                    <a class="craze-link" target="__blank" href="{{$craze['url']}}">
                        {{ trim($craze['name']) }}
                        <div class="craze-volume">{{ $craze['tweet_volume'] }} tweets</div>
                    </a>
                </div>
            </div>  
        @endforeach
    </div>
</div>
@endsection
