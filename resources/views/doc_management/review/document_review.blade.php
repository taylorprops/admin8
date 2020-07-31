@extends('layouts.main')
@section('title', 'Document Review')

@section('content')
<div class="container-fluid page-document-review">

    <div class="row">

        <div class="col-3 pl-1 pr-0 mr-0">

            <div class="transactions-container ml-2">

                <div class="properties-container w-100 pr-3">

                    <div class="list-group" id="properties_list_group">

                        @if(count($listings_with_notes) > 0 || count($contracts_with_notes) > 0 || count($referrals_with_notes) > 0)

                            <a href="javascript: void(0)" class="list-group-item property-list-header border-left-0">
                                <div class="h4-responsive text-orange my-0 ml-2"><i class="fad fa-comments mr-2"></i> New Comments</div>
                            </a>

                            @foreach($listings_with_notes as $listing_with_notes)

                                @php
                                $address = ucwords(strtolower($listing_with_notes -> FullStreetAddress.' '.$listing_with_notes -> City)).', '.$listing_with_notes -> StateOrProvince.', '.$listing_with_notes -> PostalCode;
                                $comments_to_review_count = $checklist_item_notes -> where('Listing_ID', $listing_with_notes -> Listing_ID) -> count();
                                @endphp

                                <a href="javascript: void(0)" class="list-group-item list-group-item-action property-item" data-id="{{ $listing_with_notes -> Listing_ID }}" data-type="listing">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            {{ $address }}
                                        </div>
                                        <div>
                                            <span class="badge badge-pill bg-orange text-white">{{ $comments_to_review_count }}</span>
                                        </div>
                                    </div>
                                </a>

                            @endforeach

                            @foreach($contracts_with_notes as $contract_with_notes)

                                @php
                                $address = ucwords(strtolower($contract_with_notes -> FullStreetAddress.' '.$contract_with_notes -> City)).', '.$contract_with_notes -> StateOrProvince.', '.$contract_with_notes -> PostalCode;
                                $comments_to_review_count = $checklist_item_notes -> where('Contract_ID', $contract_with_notes -> Contract_ID) -> count();
                                @endphp

                                <a href="javascript: void(0)" class="list-group-item list-group-item-action property-item" data-id="{{ $contract_with_notes -> Contract_ID }}" data-type="contract">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            {{ $address }}
                                        </div>
                                        <div>
                                            <span class="badge badge-pill bg-orange text-white">{{ $comments_to_review_count }}</span>
                                        </div>
                                    </div>
                                </a>

                            @endforeach

                            @foreach($referrals_with_notes as $referral_with_notes)

                                @php
                                $address = ucwords(strtolower($referral_with_notes -> FullStreetAddress.' '.$referral_with_notes -> City)).', '.$referral_with_notes -> StateOrProvince.', '.$referral_with_notes -> PostalCode;
                                $comments_to_review_count = $checklist_item_notes -> where('Referral_ID', $referral_with_notes -> Referral_ID) -> count();
                                @endphp

                                <a href="javascript: void(0)" class="list-group-item list-group-item-action property-item" data-id="{{ $referral_with_notes -> Referral_ID }}" data-type="referral">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            {{ $address }}
                                        </div>
                                        <div>
                                            <span class="badge badge-pill bg-orange text-white">{{ $comments_to_review_count }}</span>
                                        </div>
                                    </div>
                                </a>

                            @endforeach

                        @endif

                        @if(count($listings) > 0)

                            <a href="javascript: void(0)" class="list-group-item property-list-header border-left-0">
                                <div class="h4-responsive text-orange my-0 ml-2"><i class="fad fa-sign mr-2"></i> Listings</div>
                            </a>

                            @foreach($listings as $listing)

                                @php
                                $address = ucwords(strtolower($listing -> FullStreetAddress.' '.$listing -> City)).', '.$listing -> StateOrProvince.', '.$listing -> PostalCode;
                                $docs_to_review = $checklist_item_docs -> GetDocsToReviewCount($listing -> Listing_ID, 'listing');
                                $docs_to_review_count = count($docs_to_review);
                                @endphp

                                <a href="javascript: void(0)" class="list-group-item list-group-item-action property-item" data-id="{{ $listing -> Listing_ID }}" data-type="listing">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            {{ $address }}
                                        </div>
                                        <div>
                                            <span class="badge badge-pill bg-orange text-white">{{ $docs_to_review_count }}</span>
                                        </div>
                                    </div>
                                </a>

                            @endforeach

                        @endif

                        @if(count($contracts) > 0)

                            <a href="javascript: void(0)" class="list-group-item property-list-header border-left-0">
                                <div class="h4-responsive text-orange my-0 ml-2"><i class="fad fa-file-contract mr-2"></i> Contracts</div>
                            </a>

                            @foreach($contracts as $contract)

                                @php
                                $address = ucwords(strtolower($contract -> FullStreetAddress.' '.$contract -> City)).', '.$contract -> StateOrProvince.', '.$contract -> PostalCode;
                                $docs_to_review = $checklist_item_docs -> GetDocsToReviewCount($contract -> Contract_ID, 'contract');
                                $docs_to_review_count = count($docs_to_review);
                                @endphp

                                <a href="javascript: void(0)" class="list-group-item list-group-item-action property-item" data-id="{{ $contract -> Contract_ID }}" data-type="contract">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            {{ $address }}
                                        </div>
                                        <div>
                                            <span class="badge badge-pill bg-orange text-white">{{ $docs_to_review_count }}</span>
                                        </div>
                                    </div>
                                </a>

                            @endforeach

                        @endif

                        @if(count($referrals) > 0)

                            <a href="javascript: void(0)" class="list-group-item property-list-header border-left-0">
                                <div class="h4-responsive text-orange my-0 ml-2"><i class="fad fa-handshake mr-2"></i> Referrals</div>
                            </a>

                            @foreach($referrals as $referral)

                                @php
                                $address = ucwords(strtolower($referral -> FullStreetAddress.' '.$referral -> City)).', '.$referral -> StateOrProvince.', '.$referral -> PostalCode;
                                $docs_to_review = $checklist_item_docs -> GetDocsToReviewCount($referral -> Referral_ID, 'referral');
                                $docs_to_review_count = count($docs_to_review);
                                @endphp

                                <a href="javascript: void(0)" class="list-group-item list-group-item-action property-item" data-id="{{ $referral -> Referral_ID }}" data-type="referral">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            {{ $address }}
                                        </div>
                                        <div>
                                            <span class="badge badge-pill bg-orange text-white">{{ $docs_to_review_count }}</span>
                                        </div>
                                    </div>
                                </a>

                            @endforeach

                        @endif


                    </div>

                </div>

                <div class="checklist-items-container w-100 pr-3">

                    <div class="sticky bg-white py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="javascript: void(0);" id="close_checklist_button" class="btn btn-sm btn-primary"><i class="fal fa-chevron-double-left mr-2"></i> Back</a>
                            <a href="javascript: void(0);" id="next_button" class="btn btn-sm btn-primary"><i class="fal fa-chevron-double-right mr-2"></i> Next</a>
                        </div>
                    </div>

                    <div class="checklist-items">

                        <div class="checklist-items-div pb-5 mb-5"> </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-6 px-0">

            <div class="documents-container border-left border-right">

                <div class="documents-div">
                    <div class="h1-responsive text-primary w-100 text-center mt-5 pt-5"><i class="fa fa-arrow-left mr-2"></i> To Begin Select A Property</div>
                </div>

            </div>

        </div>

        <div class="col-3">

            <div class="row">
                <div class="col-12">

                    <div class="details-div pl-2"> </div>

                </div>
            </div>

        </div>

    </div>

</div>




@endsection
