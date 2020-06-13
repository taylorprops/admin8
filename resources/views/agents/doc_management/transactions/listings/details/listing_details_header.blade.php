@php
if($listing -> SaleRent == 'sale') {
    $sale_rent = 'For Sale';
} else if($listing -> SaleRent == 'rental') {
    $sale_rent = 'Rental';
} else if($listing -> SaleRent == 'both') {
    $sale_rent = 'For Sale And Rent';
}
@endphp<div class="row mt-1 mt-sm-4">

    <div class="col-12 col-lg-9">

        <div class="d-flex justify-content-start flex-wrap">

            @if($listing -> ListPictureURL)
            <div class="d-none d-sm-block ml-2 mr-3">
                <div class="property-image-div">
                    <img src="{{ $listing -> ListPictureURL }}" class="img-fluid z-depth-2">
                </div>
            </div>
            @endif

            <div class="ml-2 ml-md-3">
                <div class="h3 mb-2 text-gray">{!! $listing -> FullStreetAddress.' '.$listing -> Street.' '.$listing -> City.', '.$listing -> StateOrProvince.' '.$listing -> PostalCode !!}</div>
                <div class="mb-1 mb-md-3">
                    <span class="badge bg-orange p-1 p-sm-2"><span class="transaction-type text-white">Listing</span></span>
                    <span class="badge bg-primary ml-1 ml-lg-2 p-1 p-sm-2"><span class="transaction-sub-type text-white">{{ $sale_rent }}</span></span>
                    <span class="badge bg-primary ml-1 ml-lg-2 p-1 p-sm-2"><span class="transaction-sub-type text-white">{{ $resource_items -> GetResourceName($listing -> PropertyType) }}</span></span>
                    @if($sale_rent != 'Rental' && $listing -> PropertySubType > '0')
                    <span class="badge bg-primary ml-1 ml-lg-2 p-1 p-sm-2"><span class="transaction-sub-type text-white">{{ $resource_items -> GetResourceName($listing -> PropertySubType) }}</span></span>
                    @endif
                </div>
            </div>

        </div>

    </div>
    <div class="col-12 col-lg-3 mt-3 mt-lg-0">
        <div class="row">
            <div class="col-6 col-lg-12 text-center text-sm-right text-lg-right">
                <a href="javascript: void(0);" class="btn btn-sm btn-success mt-2" id="add_contract_button"><i class="fa fa-plus mr-2"></i> Accept Contract</a>
            </div>
            <div class="col-6 col-lg-12 text-center text-sm-left text-lg-right">
                <a href="javascript: void(0);" class="btn btn-sm btn-danger mt-2" id="withdraw_listing_button"><i class="fa fa-minus mr-2"></i> Withdraw Listing</a>
            </div>
        </div>
    </div>

</div>

<div class="row my-2 my-md-4 py-2 border-top border-bottom listing-header-details">

    <div class="col-12">
        <div class="d-flex justify-content-start flex-wrap">

            <div class="bg-primary d-flex justify-content-start flex-wrap text-white m-1 p-2 z-depth-1">
                <div class="text-white d-none d-sm-inline-block mr-3">
                    <i class="fad fa-users fa-2x"></i>
                </div>
                <div class="ml-2">
                    <span class="font-weight-bold text-yellow">List Agent</span>
                    <br>
                    {{ $listing -> ListAgentFirstName . ' ' . $listing -> ListAgentLastName }}<br>

                    @php
                    $contact_details = '<a href=\'tel:'.format_phone($listing -> ListAgentPreferredPhone).'\'>'.format_phone($listing -> ListAgentPreferredPhone).'</a><br>
                    <a href=\'mailto:'.$listing -> ListAgentEmail.'\'>'.$listing -> ListAgentEmail.'</a>';
                    @endphp
                    <a href="javascript: void(0)" class="btn btn-sm btn-primary ml-0" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Contact Details" data-content="{!! $contact_details !!}"><i class="fad fa-address-card mr-1"></i> Contact</a>

                </div>
                <div class="ml-2">
                    <span class="font-weight-bold text-yellow">Sellers</span>
                    <br>
                    <div class="d-flex justify-content-start">
                        @foreach($sellers as $seller)
                        <div class="mx-1">
                            {{ $seller -> first_name . ' ' . $seller -> last_name }}<br>
                            @php
                            $contact_details = '<a href=\'tel:'.format_phone($seller -> cell_phone).'\'>'.format_phone($seller -> cell_phone).'</a><br>
                            <a href=\'mailto:'.$seller -> email.'\'>'.$seller -> email.'</a>';
                            @endphp
                            <a href="javascript: void(0)" class="btn btn-sm btn-primary ml-0" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Contact Details" data-content="{!! $contact_details !!}"><i class="fad fa-address-card mr-1"></i> Contact</a>

                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="bg-primary d-flex justify-content-start text-white p-2 m-1 z-depth-1">
                <div class="text-white d-none d-sm-inline-block mr-3">
                    <i class="fad fa-calendar-alt fa-2x"></i>
                </div>
                <div class="container p-0 m-0">
                    <div class="row">
                        <div class="col-6 text-right pr-0">
                            <span class="font-weight-bold text-yellow text-nowrap">List Date</span>
                        </div>
                        <div class="col-6 text-left">
                            {{ date('n/j/Y', strtotime($listing -> MLSListDate)) }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 text-right pr-0">
                            <span class="font-weight-bold text-yellow text-nowrap">Expires Date</span>
                        </div>
                        <div class="col-6 text-left">
                            {{ date('n/j/Y', strtotime($listing -> ExpirationDate)) }}
                        </div>
                    </div>
                </div>

            </div>

            <div class="bg-primary d-flex justify-content-start text-white p-2 m-1 pr-4 z-depth-1">

                <div class="text-white d-none d-sm-inline-block mr-3">
                    <i class="fad fa-home-alt fa-2x"></i>
                </div>
                <div class="container p-0 m-0">
                    <div class="row">
                        <div class="col-6 text-right pr-0">
                            <span class="font-weight-bold text-yellow mr-2">Status</span>
                        </div>
                        <div class="col-6 text-left text-nowrap">
                            {{ $resource_items -> GetResourceName($listing -> Status) }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 text-right pr-0">
                            <span class="font-weight-bold text-yellow mr-2">List Price</span>
                        </div>
                        <div class="col-6 text-left">
                            ${{ number_format($listing -> ListPrice) }}
                        </div>
                    </div>
                </div>

            </div>

        </div>




    </div>



    {{-- <div class="col">
        <div class="bg-primary d-flex justify-content-start text-white rounded p-2 z-depth-1">
            <div class="text-white ml-2 mr-4">
                <i class="fad fa-users fa-2x"></i>
            </div>
            <div>
                <strong>List Agent</strong>
                <br>
                {{ $listing -> ListAgentFirstName . ' ' . $listing -> ListAgentLastName }}<br>
                {{ format_phone($listing -> ListAgentPreferredPhone) }}<br>
                <a href="mailto:{{ $listing -> ListAgentEmail }}">{{ $listing -> ListAgentEmail }}</a>
            </div>
            <div>
                <strong>Sellers</strong>
                <br>
                @foreach($sellers as $seller)
                <div id="seller_one_display">{{ $seller -> first_name . ' ' . $seller -> last_name }}</div>
                @endforeach
            </div>
        </div>

    </div>

    <div class="col">
        <div class="card bg-primary text-white rounded p-2">
            <strong>List Date</strong>
            <br>
            {{ date('n/j/Y', strtotime($listing -> MLSListDate)) }}

            <strong>Expiration Date</strong>
            {{ date('n/j/Y', strtotime($listing -> ExpirationDate)) }}
        </div>
    </div> --}}


    {{-- <div class="col-6 col-md-3 col-lg-2">

        <div class="h5 mb-1 text-gray border-bottom d-inline-block">Status</div>
        <div class="mb-2">{{ $resource_items -> GetResourceName($listing -> Status) }}</div>

        <div class="h5 pb-0 text-gray border-bottom d-inline-block">List Price</div>
        <div id="list_price_display">${{ number_format($listing -> ListPrice) }}</div>

    </div> --}}
</div>
