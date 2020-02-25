@extends('layouts.main')
@section('title', 'Add Listing Details')

@section('content')

<div class="container page-add-listing-details">
    <div class="row">
        <div class="col-12">
            {{ dd($property_details) }}
        </div>
    </div>
</div>
@endsection

