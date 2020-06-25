@extends('layouts.main')
@section('title', 'Transactions')

@section('content')
<div class="container page-listings-all">
    <div class="row">
        <div class="col-12 mt-5">
            <div class="list-group">
                <h4>Listings</h4>
                @foreach($listings as $listing)
                    <div class="list-group-item">
                        <div class="row">
                            <div class="col-2">
                                <a href="/agents/doc_management/transactions/transaction_details/{{ $listing -> Listing_ID }}/listing" class="btn btn-primary">View Listing</a>
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
    <div class="row">
        <div class="col-12 mt-5">
            <div class="list-group">
                <h4>Contracts</h4>
                @foreach($contracts as $contract)
                    <div class="list-group-item">
                        <div class="row">
                            <div class="col-2">
                                <a href="/agents/doc_management/transactions/transaction_details/{{ $contract -> Contract_ID }}/contract" class="btn btn-primary">View Contract</a>
                            </div>
                            <div class="col-10">
                                {{ $contract -> FullStreetAddress.' '.$contract -> City.' '.$contract -> StateOrProvince.' '.$contract -> PostalCode }}
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
