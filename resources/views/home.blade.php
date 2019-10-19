@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="grid">
        <div class="grid-sizer"></div>
        @foreach ($images->results as $key => $image)
            <div class="grid-item">
            <img src={{$image->urls->small . ""}} class="img-fluid" alt="">
            </div>  
        @endforeach
    </div>
</div>
@endsection
