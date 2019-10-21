@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="nav-wrapper my-2">
        <a href="/"><h1>Hot Back Then</h1></a>
        <div class="left-nav-wrapper">
            {{-- <div class="prompt">Select a country to see what's trending right now</div> --}}
            <select id="location-selector" class="select-css ml-2">
                @foreach ($locations as $location)
                    <option value="{{ $location->location }}" 
                        {{(trim($location->location) == $current_location ? 'selected' : '')}}>
                        {{ $location->location }} 
                    </option>
                @endforeach
            </select>
            <input placeholder="Within the last hour" class="flatpickr flatpickr-input" id="flatpickr" class="datepicker-text" />
        </div>
    </div>

    @if(count($trends) <= 0)
        <div class="no-trends">
            <h1 class="no-trends-text">No trends for this location. Please choose another</h1>
        </div>
    @else
        <div class="grid">
            <div class="grid-sizer"></div>
            @foreach ($trends as $key => $craze)
                @if ($key < $image_count)
                <div class="grid-item">
                    <img src={{$craze['image_url']}} class="img-fluid" alt="">
                    <div class="text-overlay-wrapper">
                        <a class="craze-link" target="__blank" href="{{$craze['url']}}">
                            {{ trim($craze['name']) }}
                            <div class="craze-volume">{{ $craze['tweet_volume'] }} tweets</div>
                        </a>
                    </div>
                    <div class="credit-overlay-wrapper">
                        <a class="craze-credit-link" href="{{ $craze['image_html_url'] }}">photo by {{ $craze['image_username'] }}</a>
                    </div>
                </div> 
                @endif 
            @endforeach
        </div>
    @endif
</div>
@endsection
