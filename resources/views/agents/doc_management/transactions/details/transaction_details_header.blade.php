@php

$sale_rent = 'For Sale';
$for_sale = true;
if($property -> SaleRent == 'rental') {
    $sale_rent = 'Rental';
    $for_sale = false;
} else if($property -> SaleRent == 'both' && $transaction_type == 'listing') {
    $sale_rent = 'For Sale And Rent';
}

if($transaction_type == 'listing') {
    $header_transaction_type = '<i class="fad fa-sign mr-2"></i> Listing Agreement';
    $transaction_type_bg = 'bg-orange';
} else if($transaction_type == 'contract') {
    $header_transaction_type = '<i class="fad fa-file-signature mr-2"></i> Lease Agreement';
    if($for_sale) {
        $header_transaction_type = '<i class="fad fa-file-signature mr-2"></i> Sales Contract';
    }
    $transaction_type_bg = 'bg-success';
} else if($transaction_type == 'referral') {
    $header_transaction_type = '<i class="fad fa-handshake mr-2"></i> Referral Agreement';
    $transaction_type_bg = 'bg-orange';
}
@endphp
<div class="row mt-1 mt-sm-4">

    <div class="col-12 col-lg-7">

        <div class="d-flex justify-content-start">

            @if($property -> ListPictureURL)
                <div class="d-none d-sm-block ml-2 mr-3">
                    <div class="property-image-div">
                        <img loading="lazy" src="{{ $property -> ListPictureURL }}" class="img-fluid z-depth-2">
                    </div>
                </div>
            @endif

            <div class="ml-2 ml-md-3">
                <div class="h3-responsive mb-2 text-gray text-uppercase">{!! $property -> FullStreetAddress.' '.$property -> Street.' '.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode !!}</div>
                <div class="mb-1 mb-md-3">
                    <span class="badge {{ $transaction_type_bg }} my-1"><span class="transaction-type text-white">{!! $header_transaction_type !!}</span></span>
                    @if($transaction_type != 'referral')
                        <span class="badge bg-primary ml-0 ml-sm-2 my-1"><span class="transaction-sub-type text-white">{{ $sale_rent }}</span></span>
                        <span class="badge bg-primary ml-0 ml-sm-2 my-1"><span class="transaction-sub-type text-white">{{ $resource_items -> GetResourceName($property -> PropertyType) }}</span></span>
                        @if($sale_rent != 'Rental' && $property -> PropertySubType > '0')
                            <span class="badge bg-primary ml-0 ml-sm-2 my-1"><span class="transaction-sub-type text-white">{{ $resource_items -> GetResourceName($property -> PropertySubType) }}</span></span>
                        @endif
                    @endif
                </div>
            </div>

        </div>

    </div>
    <div class="col-12 col-lg-5 mt-3 mt-lg-0">

        @if($transaction_type == 'listing')

        <div class="d-flex flex-wrap justify-content-end align-items-center">
            @if(in_array($property -> Status, $resource_items -> GetActiveListingStatuses('no', 'yes', 'yes') -> toArray()))
                <div>
                    <a href="javascript: void(0);" class="btn btn-success mt-2 d-block d-sm-inline-block" id="accept_contract_button"><i class="fa fa-plus mr-2"></i> Accept {{ $for_sale ? 'Contract' : 'Lease' }}</a>
                </div>
                <div>
                    <a href="javascript: void(0);" class="btn btn-danger mt-2 d-block d-sm-inline-block" id="withdraw_listing_button"><i class="fa fa-minus mr-2"></i> Withdraw Listing</a>
                </div>
            @else
                <div>
                    <span class="badge bg-orange text-white mr-2 font-12"><i class="fad fa-check-circle mr-2"></i> {{ $for_sale ? 'Under Contract' : 'Lease Accepted' }}!</span>
                </div>
            @endif
        </div>

        @elseif($transaction_type == 'contract')

            @php
            $status = $resource_items -> GetResourceName($property -> Status);
            @endphp

            <div class="row">

                <div class="col-12">

                    <div class="d-flex flex-wrap justify-content-end align-items-center">

                        @if($property -> Status == $resource_items -> GetResourceID('Active', 'contract_status'))

                            <div>
                                <a href="javascript: void(0);" class="btn btn-danger mt-2" id="release_contract_button" data-for-sale="{{ $for_sale ? 'yes' : 'no' }}" data-listing-expiration-date="{{ $listing_expiration_date }}"><i class="fa fa-minus mr-2"></i> {{ $for_sale ? 'Release Contract' : 'Cancel Lease' }}</a>
                            </div>

                        @elseif($property -> Status == $resource_items -> GetResourceID('Cancel Pending', 'contract_status'))

                            <span class="badge bg-orange text-white mr-2 font-12"><i class="fad fa-hourglass-start mr-2 text-white"></i> {{ $status }}</span>
                            <div class="mx-3 mt-1">
                                <a href="javascript: void(0)"class="undo-cancel-button" data-contract-id="{{ $property -> Contract_ID }}"><i class="fad fa-undo mr-1"></i> Undo</a>
                            </div>
                            @if(auth() -> user() -> group == 'admin')
                                <div>
                                    <button class="btn btn-danger process-cancellation-button" data-contract-id="{{ $property -> Contract_ID }}"><i class="fad fa-cogs mr-2 text-white"></i> Process</button>
                                </div>
                            @endif

                        @elseif($property -> Status == $resource_items -> GetResourceID('Released', 'contract_status') || $property -> Status == $resource_items -> GetResourceID('Canceled', 'contract_status'))

                            <span class="badge bg-danger text-white mr-2 font-13"><i class="fad fa-ban mr-2 text-white"></i> {{ $status }}</span>
                            <div class="mx-3 mt-1">
                                <a href="javascript: void(0)"class="undo-cancel-button" data-contract-id="{{ $property -> Contract_ID }}"><i class="fad fa-undo mr-1"></i> Undo</a>
                            </div>

                        @else
                            <span class="badge bg-primary text-white mr-2 font-13"><i class="fad fa-exclamation-circle mr-2 text-white"></i> {{ $status }}</span>
                        @endif

                        @if($property -> Listing_ID > 0 && $property -> Contract_ID > 0)

                            <div>
                                <a href="/agents/doc_management/transactions/transaction_details/{{ $property -> Listing_ID }}/listing" class="btn btn-primary mt-2"><i class="fad fa-sign mr-2"></i> View Listing</a>
                            </div>

                        @endif

                    </div>

                </div>

            </div>

        @endif
    </div>

</div>

@if($transaction_type != 'referral')
<div class="row my-2 my-md-4 py-2 border-top border-bottom listing-header-details">

    <div class="col-12">
        <div class="d-block d-sm-flex justify-content-start flex-wrap">

            <div class="bg-primary d-flex justify-content-start flex-wrap text-white m-1 p-2">
                <div class="text-white d-none d-sm-inline-block mr-2">
                    <i class="fad fa-users fa-2x"></i>
                </div>

                @if($resource_items -> GetResourceId('For Sale By Owner', 'checklist_property_sub_types') != $property -> PropertySubType)
                <div class="ml-2 pr-2 agent-section border-right header-section">
                    <span class="font-weight-bold text-yellow">List Agent</span>
                    <br>
                    <div>
                        {{ $property -> ListAgentFirstName . ' ' . $property -> ListAgentLastName }}
                        <br>
                        {{ $property -> ListOfficeName }}
                        <br>
                        @php
                        $contact_details = '<i class=\'fad fa-phone-alt mr-2 text-primary\'></i> <a href=\'tel:'.format_phone($property -> ListAgentPreferredPhone).'\'>'.format_phone($property -> ListAgentPreferredPhone).'</a><br>
                        <i class=\'fad fa-at mr-2 text-primary\'></i> <a href=\'mailto:'.$property -> ListAgentEmail.'\'>'.$property -> ListAgentEmail.'</a>';
                        @endphp
                        <a href="javascript: void(0)" class="btn btn-sm btn-primary ml-0" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Contact Details" data-content="{!! $contact_details !!}"><i class="fad fa-address-book mr-1"></i> Contact</a>
                    </div>
                </div>
                @endif

                @if($sellers)
                <div class="ml-2 header-section">
                    <span class="font-weight-bold text-yellow">{{ $for_sale ? 'Sellers' : 'Owners' }}</span>
                    <br>
                    <div>
                        @foreach($sellers as $seller)
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                {{ $seller }}
                            </div>
                            {{-- <div class="ml-2">
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
                            </div> --}}
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            @if($transaction_type == 'contract' && $property -> BuyerRepresentedBy != 'none')
            <div class="bg-primary d-flex justify-content-start flex-wrap text-white m-1 p-2">
                <div class="text-white d-none d-sm-inline-block mr-2">
                    <i class="fad fa-users fa-2x"></i>
                </div>
                <div class="ml-2 pr-2 agent-section border-right header-section">
                    <span class="font-weight-bold text-yellow">{{ $for_sale ? 'Buyer' : 'Renter' }}'s Agent</span>
                    <br>
                    <div>
                        {{ $property -> BuyerAgentFirstName . ' ' . $property -> BuyerAgentLastName }}
                        <br>
                        {{ $property -> BuyerOfficeName }}
                        <br>
                        @php
                        $contact_details = '<i class=\'fad fa-phone-alt mr-2 text-primary\'></i> <a href=\'tel:'.format_phone($property -> BuyerAgentPreferredPhone).'\'>'.format_phone($property -> BuyerAgentPreferredPhone).'</a><br>
                        <i class=\'fad fa-at mr-2 text-primary\'></i> <a href=\'mailto:'.$property -> BuyerAgentEmail.'\'>'.$property -> BuyerAgentEmail.'</a>';
                        @endphp
                        <a href="javascript: void(0)" class="btn btn-sm btn-primary ml-0" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Contact Details" data-content="{!! $contact_details !!}"><i class="fad fa-address-book mr-1"></i> Contact</a>
                    </div>
                </div>
                @if(count($buyers) > 0)
                <div class="ml-2 header-section">
                    <span class="font-weight-bold text-yellow">{{ $for_sale ? 'Buyers' : 'Renters' }}</span>
                    <br>
                    <div>
                        @foreach($buyers as $buyer)
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                {{ $buyer }}
                            </div>
                            {{-- <div class="ml-2">
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
                            </div> --}}
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endif

            <div class="bg-primary d-flex justify-content-start text-white p-2 m-1">
                <div class="text-white d-none d-sm-inline-block mr-2">
                    <i class="fad fa-home-alt fa-2x"></i>
                </div>

                @if($for_sale || $transaction_type == 'listing')

                    <div class="container pr-5">
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
                                <span class="font-weight-bold text-yellow">@if($transaction_type == 'listing') {{ $for_sale ? 'List Price' : 'Lease Amount' }} @else Sale Price @endif</span>
                            </div>
                            <div class="col-6 text-left text-nowrap">
                                @if($transaction_type == 'listing') ${{ number_format($property -> ListPrice) }} @else ${{ number_format($property -> ContractPrice) }} @endif
                            </div>
                        </div>
                    </div>

                @else

                    <div class="container pr-5">
                        <div class="row">
                            <div class="col-6 text-right pr-0">
                                <span class="font-weight-bold text-yellow text-nowrap">Lease Date</span>
                            </div>
                            <div class="col-6 text-left">
                                {{ date('n/j/Y', strtotime($property -> CloseDate)) }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 text-right pr-0 text-nowrap text-nowrap">
                                <span class="font-weight-bold text-yellow">Lease Price</span>
                            </div>
                            <div class="col-6 text-left text-nowrap">
                                @if($transaction_type == 'listing') ${{ number_format($property -> ListPrice) }} @else ${{ number_format($property -> LeaseAmount) }}  @endif
                            </div>
                        </div>
                    </div>

                @endif

            </div>

        </div>

    </div>

</div>
@endif
