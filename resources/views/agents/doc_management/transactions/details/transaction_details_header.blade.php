@php

$sale_rent = 'For Sale';
if($property -> SaleRent == 'rental') {
    $sale_rent = 'Rental';
} else if($property -> SaleRent == 'both' && $transaction_type == 'listing') {
    $sale_rent = 'For Sale And Rent';
}
@endphp
<div class="row mt-1 mt-sm-4">

    <div class="col-12 col-lg-9">

        <div class="d-flex justify-content-start flex-wrap">

            @if($property -> ListPictureURL)
            <div class="d-none d-sm-block ml-2 mr-3">
                <div class="property-image-div">
                    <img src="{{ $property -> ListPictureURL }}" class="img-fluid z-depth-2">
                </div>
            </div>
            @endif

            <div class="ml-2 ml-md-3">
                <div class="h3 mb-2 text-gray text-uppercase">{!! $property -> FullStreetAddress.' '.$property -> Street.' '.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode !!}</div>
                <div class="mb-1 mb-md-3">
                    <span class="badge bg-orange p-1 p-sm-2"><span class="transaction-type text-white">{{ ucwords($transaction_type) }}</span></span>
                    <span class="badge bg-primary ml-1 ml-lg-2 p-1 p-sm-2"><span class="transaction-sub-type text-white">{{ $sale_rent }}</span></span>
                    <span class="badge bg-primary ml-1 ml-lg-2 p-1 p-sm-2"><span class="transaction-sub-type text-white">{{ $resource_items -> GetResourceName($property -> PropertyType) }}</span></span>
                    @if($sale_rent != 'Rental' && $property -> PropertySubType > '0')
                    <span class="badge bg-primary ml-1 ml-lg-2 p-1 p-sm-2"><span class="transaction-sub-type text-white">{{ $resource_items -> GetResourceName($property -> PropertySubType) }}</span></span>
                    @endif
                </div>
            </div>

        </div>

    </div>
    <div class="col-12 col-lg-3 mt-3 mt-lg-0">
        @if($transaction_type == 'listing')
        <div class="row">
            <div class="col-6 col-lg-12 text-center text-sm-right">
                <a href="javascript: void(0);" class="btn btn-sm btn-success mt-2" id="accept_contract_button"><i class="fa fa-plus mr-2"></i> Accept Contract</a>
            </div>
            <div class="col-6 col-lg-12 text-center text-sm-left text-lg-right">
                <a href="javascript: void(0);" class="btn btn-sm btn-danger mt-2" id="withdraw_listing_button"><i class="fa fa-minus mr-2"></i> Withdraw Listing</a>
            </div>
        </div>
        @else
        <div class="row">
            <div class="col-6 col-lg-12 text-sm-left text-lg-right">
                <a href="javascript: void(0);" class="btn btn-sm btn-danger mt-2" id="release_contract_button"><i class="fa fa-minus mr-2"></i> Release Contract</a>
            </div>
        </div>
        @endif
    </div>

</div>

<div class="row my-2 my-md-4 py-2 border-top border-bottom listing-header-details">

    <div class="col-12">
        <div class="d-flex justify-content-start flex-wrap">

            <div class="bg-primary d-flex justify-content-start flex-wrap text-white m-1 p-2 z-depth-1">
                <div class="text-white d-none d-sm-inline-block mr-2">
                    <i class="fad fa-users fa-2x"></i>
                </div>
                <div class="ml-2 pr-2 agent-section border-right">
                    <span class="font-weight-bold text-yellow">List Agent</span>
                    <br>
                    <div class="d-flex justify-content-between">
                        <div>
                            {{ $property -> ListAgentFirstName . ' ' . $property -> ListAgentLastName }}
                            <br>
                            {{ $property -> ListOfficeName }}
                        </div>
                        <div class="ml-2">
                            @php
                            $contact_details = '<i class=\'fad fa-phone-alt mr-2 text-primary\'></i> <a href=\'tel:'.format_phone($property -> ListAgentPreferredPhone).'\'>'.format_phone($property -> ListAgentPreferredPhone).'</a><br>
                            <i class=\'fad fa-at mr-2 text-primary\'></i> <a href=\'mailto:'.$property -> ListAgentEmail.'\'>'.$property -> ListAgentEmail.'</a>';
                            @endphp
                            <a href="javascript: void(0)" class="btn btn-sm btn-primary" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Contact Details" data-content="{!! $contact_details !!}"><i class="fad fa-address-book mr-1"></i> Contact</a>
                        </div>
                    </div>
                </div>
                @if($sellers)
                <div class="ml-2">
                    <span class="font-weight-bold text-yellow">Sellers</span>
                    <br>
                    <div>
                        @foreach($sellers as $seller)
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                {{ ($seller -> entity_name ? $seller -> entity_name : $seller -> first_name . ' ' . $seller -> last_name) }}
                            </div>
                            <div class="ml-2">
                                @php
                                $contact_details = '';
                                if($seller -> cell_phone != '') {
                                    $contact_details .= '<i class=\'fad fa-phone-alt mr-2 text-primary\'></i> <a href=\'tel:'.format_phone($seller -> cell_phone).'\'>'.format_phone($seller -> cell_phone).'</a><br>';
                                }
                                if($seller -> email != '') {
                                    $contact_details .= '<i class=\'fad fa-at mr-2 text-primary\'></i> <a href=\'mailto:'.$seller -> email.'\'>'.$seller -> email.'</a>';
                                }
                                @endphp
                                @if($seller -> cell_phone != '' || $seller -> email != '')
                                <a href="javascript: void(0)" class="btn btn-sm btn-primary" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Contact Details" data-content="{!! $contact_details !!}"><i class="fad fa-address-book mr-1"></i> Contact</a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            @if($transaction_type == 'contract')
            <div class="bg-primary d-flex justify-content-start flex-wrap text-white m-1 p-2 z-depth-1">
                <div class="text-white d-none d-sm-inline-block mr-2">
                    <i class="fad fa-users fa-2x"></i>
                </div>
                <div class="ml-2 pr-2 agent-section border-right">
                    <span class="font-weight-bold text-yellow">Buyer's Agent</span>
                    <br>
                    <div class="d-flex justify-content-between">
                        <div>
                            {{ $property -> BuyerAgentFirstName . ' ' . $property -> BuyerAgentLastName }}
                            <br>
                            {{ $property -> BuyerOfficeName }}
                        </div>
                        <div class="ml-2">
                            @php
                            $contact_details = '<i class=\'fad fa-phone-alt mr-2 text-primary\'></i> <a href=\'tel:'.format_phone($property -> BuyerAgentPreferredPhone).'\'>'.format_phone($property -> BuyerAgentPreferredPhone).'</a><br>
                            <i class=\'fad fa-at mr-2 text-primary\'></i> <a href=\'mailto:'.$property -> BuyerAgentEmail.'\'>'.$property -> BuyerAgentEmail.'</a>';
                            @endphp
                            <a href="javascript: void(0)" class="btn btn-sm btn-primary ml-2" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Contact Details" data-content="{!! $contact_details !!}"><i class="fad fa-address-book mr-1"></i> Contact</a>
                        </div>
                    </div>
                </div>
                @if(count($buyers) > 0)
                <div class="ml-2">
                    <span class="font-weight-bold text-yellow">Buyers</span>
                    <br>
                    <div>
                        @foreach($buyers as $buyer)
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                {{ $buyer -> first_name . ' ' . $buyer -> last_name }}
                            </div>
                            <div class="ml-2">
                                @php
                                $contact_details = '';
                                if($buyer -> cell_phone != '') {
                                    $contact_details .= '<i class=\'fad fa-phone-alt mr-2 text-primary\'></i> <a href=\'tel:'.format_phone($buyer -> cell_phone).'\'>'.format_phone($buyer -> cell_phone).'</a><br>';
                                }
                                if($buyer -> email != '') {
                                    $contact_details .= '<i class=\'fad fa-at mr-2 text-primary\'></i> <a href=\'mailto:'.$buyer -> email.'\'>'.$buyer -> email.'</a>';
                                }
                                @endphp
                                @if($buyer -> cell_phone != '' || $buyer -> email != '')
                                <a href="javascript: void(0)" class="btn btn-sm btn-primary ml-2" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Contact Details" data-content="{!! $contact_details !!}"><i class="fad fa-address-book mr-1"></i> Contact</a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endif

            <div class="bg-primary d-flex justify-content-start text-white p-2 m-1 z-depth-1">
                <div class="text-white d-none d-sm-inline-block mr-2">
                    <i class="fad fa-home-alt fa-2x"></i>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-6 text-right pr-0">
                            <span class="font-weight-bold text-yellow text-nowrap">Status</span>
                        </div>
                        <div class="col-6 text-left text-nowrap">
                            {{ $resource_items -> GetResourceName($property -> Status) }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 text-right pr-0">
                            <span class="font-weight-bold text-yellow text-nowrap">@if($transaction_type == 'listing') List Date @else Offer Date @endif</span>
                        </div>
                        <div class="col-6 text-left">
                            @if($transaction_type == 'listing') {{ date('n/j/Y', strtotime($property -> MLSListDate)) }} @else {{ date('n/j/Y', strtotime($property -> ContractDate)) }} @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 text-right pr-0">
                            <span class="font-weight-bold text-yellow text-nowrap">@if($transaction_type == 'listing') Expires Date @else Settle Date @endif</span>
                        </div>
                        <div class="col-6 text-left">
                            @if($transaction_type == 'listing') {{ date('n/j/Y', strtotime($property -> ExpirationDate)) }} @else {{ date('n/j/Y', strtotime($property -> CloseDate)) }} @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 text-right pr-0 text-nowrap text-nowrap">
                            <span class="font-weight-bold text-yellow">@if($transaction_type == 'listing') List Price @else Sale Price @endif</span>
                        </div>
                        <div class="col-6 text-left text-nowrap">
                            @if($transaction_type == 'listing') ${{ number_format($property -> ListPrice) }} @else ${{ number_format($property -> ContractPrice) }} @endif
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>