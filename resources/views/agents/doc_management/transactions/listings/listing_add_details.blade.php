@extends('layouts.main')
@section('title', 'Add Listing Details')

@section('content')

<div class="container page-add-listing-details">
    <div class="row">
        <div class="col-12">

            @php
            // set values
            $property_type_val = $property_details -> PropertyType ?? null;
            $forsale = '';
            if($property_type_val) {
                if(stristr($property_type_val, 'lease')) {
                    $forsale = 'no';
                    $property_type_val = str_replace(' Lease', '', $property_type_val);
                } else {
                    $forsale = 'yes';
                }
            }

            $sale_type = $property_details -> SaleType ?? null;
            if($sale_type) {
                $end = strpos($sale_type, ',');
                if(!$end) {
                    $end = strlen($sale_type);
                }
                $sale_type = trim(substr($sale_type, 0, $end));

                if(preg_match('/(hud|reo)/i', $sale_type)) {
                    $sale_type = 'REO/Bank/HUD Owned';
                } else if(preg_match('/foreclosure/i', $sale_type)) {
                    $sale_type = 'Foreclosure';
                } else if(preg_match('/auction/i', $sale_type)) {
                    $sale_type = 'Auction';
                } else if(preg_match('/(short|third)/i', $sale_type)) {
                    $sale_type = 'Short Sale';
                } else if(preg_match('/standard/i', $sale_type)) {
                    $sale_type = 'Standard';
                } else {
                    $sale_type = '';
                }
                // if no results check new construction
                if($sale_type == '') {
                    if($property_details -> NewConstructionYN == 'Y') {
                        $sale_type = 'New Construction';
                    }
                }
            }

            $hoa_condo = 'none';
            $condo = $property_details -> CondoYN ?? null;
            if($condo && $condo == 'Y') {
                $hoa_condo = 'condo';
            }
            $hoa = $property_details -> AssociationYN ?? null;
            if($hoa && $hoa == 'Y') {
                if($property_details -> AssociationFee > 0) {
                    $hoa_condo = 'hoa';
                }
            }

            @endphp

            <div class="h3 text-orange mt-3 mt-sm-4 text-center w-100">{{ $property_details -> FullStreetAddress }} {{ $property_details -> City.', '.$property_details -> StateOrProvince.', '.$property_details -> PostalCode }}</div>

            <div class="steps-container mx-auto">
                <form id="steps_form">
                    <div class="h4 text-primary mt-3 text-center">Please Enter and Verify the following details</div>
                    <ul class="stepper linear mt-2 pt-1">
                        <li class="step active">
                            <div class="h4 step-title waves-effect waves-light text-gray">Listing Type <span class="step-value float-right text-orange"></span></div>
                            <div class="step-new-content mt-3">
                                <div class="row">
                                    <div class="col-12">
                                        <input type="radio" class="custom-form-element form-radio required" name="listing_type" value="sale" data-label="Sale" @if($forsale == 'yes') checked @endif>
                                        <input type="radio" class="custom-form-element form-radio required" name="listing_type" value="rental" data-label="Rental" @if($forsale == 'no') checked @endif>
                                        <input type="radio" class="custom-form-element form-radio required" name="listing_type" value="both" data-label="Both <span class='font-8 text-orange ml-2'>(Add both listing agreements to same transaction)</span>">
                                    </div>
                                </div>
                                <div class="step-actions">
                                    <button class="waves-effect waves-light btn btn-success next-step">CONTINUE <i class="fad fa-chevron-double-right ml-2"></i></button>
                                </div>
                            </div>
                        </li>
                        <li class="step">
                            <div class="h4 step-title waves-effect waves-light text-gray">Property Type <span class="step-value float-right text-orange"></span></div>
                            <div class="step-new-content mt-3">
                                <div class="row">
                                    <div class="col-12">
                                        @foreach($property_types as $property_type)
                                        <input type="radio" class="custom-form-element form-radio required" name="property_type" value="{{ $property_type -> resource_name }}" data-label="{{ $property_type -> resource_name }}" @if($property_type -> resource_name == $property_type_val) checked @endif>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="step-actions">
                                    <button class="waves-effect waves-light btn btn-secondary previous-step"><i class="fad fa-chevron-double-left mr-2"></i> BACK</button>
                                    <button class="waves-effect waves-light btn btn-success next-step">CONTINUE <i class="fad fa-chevron-double-right ml-2"></i></button>
                                </div>
                            </div>
                        </li>
                        <li class="step property-sub-type">
                            <div class="h4 step-title waves-effect waves-light text-gray">Sale Type <span class="step-value float-right text-orange"></span></div>
                            <div class="step-new-content mt-3">
                                <div class="row">
                                    <div class="col-12">
                                        @foreach($property_sub_types as $property_sub_type)
                                            @if($property_sub_type -> resource_name != 'For Sale By Owner')
                                                <input type="radio" class="custom-form-element form-radio required" name="property_sub_type" value="{{ $property_sub_type -> resource_name }}" data-label="{{ $property_sub_type -> resource_name }}" @if($property_sub_type -> resource_name == $sale_type) checked @endif>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                <div class="step-actions">
                                    <button class="waves-effect waves-light btn btn-secondary previous-step"><i class="fad fa-chevron-double-left mr-2"></i> BACK</button>
                                    <button class="waves-effect waves-light btn btn-success next-step">CONTINUE <i class="fad fa-chevron-double-right ml-2"></i></button>
                                </div>
                            </div>
                        </li>
                        <li class="step year-built disclosures">
                            <div class="h4 step-title waves-effect waves-light text-gray">Year Built <span class="step-value float-right text-orange"></span></div>
                            <div class="step-new-content mt-3">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="custom-form-element form-input numbers-only required" name="year_built" id="year_built" value="{{ $property_details -> YearBuilt }}" data-label="Year Built">
                                    </div>
                                </div>
                                <div class="step-actions">
                                    <button class="waves-effect waves-light btn btn-secondary previous-step"><i class="fad fa-chevron-double-left mr-2"></i> BACK</button>
                                    <button class="waves-effect waves-light btn btn-success next-step">CONTINUE <i class="fad fa-chevron-double-right ml-2"></i></button>
                                </div>
                            </div>
                        </li>
                        <li class="step">
                            <div class="h4 step-title waves-effect waves-light text-gray">List Price <span class="step-value float-right text-orange"></span></div>
                            <div class="step-new-content mt-3">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="custom-form-element form-input numbers-only required" name="list_price" id="list_price" value="{{ $property_details -> ListPrice ?? null }}" data-label="List Price">
                                    </div>
                                </div>
                                <div class="step-actions">
                                    <button class="waves-effect waves-light btn btn-secondary previous-step"><i class="fad fa-chevron-double-left mr-2"></i> BACK</button>
                                    <button class="waves-effect waves-light btn btn-success next-step">CONTINUE <i class="fad fa-chevron-double-right ml-2"></i></button>
                                </div>
                            </div>
                        </li>
                        <li class="step hoa disclosures">
                            <div class="h4 step-title waves-effect waves-light text-gray">HOA/Condo Fees <span class="step-value float-right text-orange"></span></div>
                            <div class="step-new-content mt-3">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <input type="radio" class="custom-form-element form-radio required" name="hoa_condo" value="hoa" data-label="HOA Fees" @if($hoa_condo == 'hoa') checked @endif>
                                        <input type="radio" class="custom-form-element form-radio required" name="hoa_condo" value="condo" data-label="Condo Fees" @if($hoa_condo == 'condo') checked @endif>
                                        <input type="radio" class="custom-form-element form-radio required" name="hoa_condo" value="none" data-label="None" @if($hoa_condo == 'none') checked @endif>
                                    </div>
                                </div>
                                <div class="step-actions">
                                    <button class="waves-effect waves-light btn btn-secondary previous-step"><i class="fad fa-chevron-double-left mr-2"></i> BACK</button>
                                    <button class="waves-effect waves-light btn btn-success next-step">CONTINUE <i class="fad fa-chevron-double-right ml-2"></i></button>
                                </div>
                            </div>
                        </li>
                        <li class="step">
                            <div class="h4 step-title waves-effect waves-light text-gray">Verify</div>
                            <div class="step-new-content mt-3">
                                <div class="step-actions">
                                    <div class="d-flex justify-content-center w-100 h-100">
                                        <button class="waves-effect waves-light btn btn-secondary previous-step"><i class="fad fa-chevron-double-left mr-2"></i> BACK</button>
                                        <button id="steps_submit" class="waves-effect waves-light btn btn-lg btn-success">CONTINUE <i class="fad fa-chevron-double-right ml-2"></i></button>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
