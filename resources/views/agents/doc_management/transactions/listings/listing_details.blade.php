@extends('layouts.main')
@section('title', 'title here')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2>{{ $listing -> FullStreetAddress.' '.$listing -> Street.' '.$listing -> City.' '.$listing -> StateOrProvince.' '.$listing -> PostalCode }}</h2>
        </div>
    </div>
</div>
@endsection
