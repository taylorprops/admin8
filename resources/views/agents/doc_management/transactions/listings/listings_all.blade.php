@extends('layouts.main')
@section('title', 'Listings')

@section('content')
<div class="container page-listings-all">
    <div class="row">
        <div class="col-12 mt-5">
            <div class="list-group">
                @foreach($listings as $listing)
                    <div class="list-group-item">
                        <div class="row">
                            <div class="col-2">
                                <a href="/agents/doc_management/transactions/listings/listing_details/{{ $listing -> Listing_ID }}" class="btn btn-primary">View Listing</a>
                            </div>
                            <div class="col-10">
                                {{ $listing -> FullStreetAddress.' '.$listing -> City.' '.$listing -> StateOrProvince.' '.$listing -> PostalCode }}
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
