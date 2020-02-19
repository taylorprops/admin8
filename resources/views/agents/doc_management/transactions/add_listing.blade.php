@extends('layouts.main')
@section('title', 'Add Listing')

@section('content')
<div class="container-fluid page-add-listing mx-auto">
    <div class="row">
        <div class="col-12">
            <div class="container mb-5">
                <div class="row">
                    <div class="col-12">
                        <div class="h1 text-primary mt-4 mb-2">Add Listing</div>
                    </div>
                </div>
            </div>
            <div id="address_container" class="property-container collapse show">
                <div id="address_search_container" class="address-container collapse show">
                    <div class="h3 text-center text-orange mb-4">To Begin, Search By Property Address</div>
                    <div class="d-flex justify-content-center mt-5">
                        <div>
                            <div class="h5 text-primary">Enter Property Street Address</div>
                            <input type="text" id="address_search_street" placeholder="">
                        </div>
                        <div class="ml-2">
                            <div class="h5 text-primary">Unit</div>
                            <input type="text" id="address_search_unit" placeholder="">
                        </div>
                    </div>
                    <div class="address-search-error hidden">
                        <div class="alert alert-danger text-danger w-50 my-3 mx-auto text-center" role="alert">
                            <i class="fad fa-exclamation-circle fa-lg mr-3"></i> Street Number not valid. Please enter the address manually
                        </div>
                    </div>
                    <div class="address-search-continue-div text-center my-4 hidden">
                        <a href=".property-container" class="btn btn-primary btn-lg" id="address_search_continue" data-toggle="collapse" role="button" aria-expanded="false" aria-controls=".property-container">Continue <i class="fad fa-chevron-double-right ml-3"></i></a>
                    </div>
                    <div class="h5 text-center mt-4">
                        <a href=".address-container" id="enter_manually_button" class="btn btn-sm btn-secondary" data-toggle="collapse" role="button" aria-expanded="false" aria-controls=".address-container">Or Enter Manually</a>
                    </div>
                </div>
                <div id="address_enter_container" class="address-container collapse">
                    <div class="h3 text-center text-orange mb-4">To Begin, Enter The Street Address</div>
                    <form id="enter_address_form">
                        <div class="container">
                            <div class="row">
                                <div class="col-2">
                                    <input type="text" id="enter_street_number" class="custom-form-element form-input required" data-label="Street Number">
                                </div>
                                <div class="col-2">
                                    <select id="enter_street_dir" class="custom-form-element form-select form-select-no-search" data-label="Dir">
                                        <option value=""></option>
                                        <option value="N">N</option>
                                        <option value="S">S</option>
                                        <option value="E">E</option>
                                        <option value="W">W</option>
                                        <option value="NE">NE</option>
                                        <option value="SE">SE</option>
                                        <option value="NW">NW</option>
                                        <option value="SW">SW</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <input type="text" id="enter_street_name" class="custom-form-element form-input required" data-label="Street Name">
                                </div>
                                <div class="col-2">
                                    <input type="text" id="enter_unit" class="custom-form-element form-input" data-label="Unit">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-2">
                                    <input type="text" id="enter_zip" class="custom-form-element form-input numbers-only required" maxlength="5" data-label="Zip">
                                </div>
                                <div class="col-4">
                                    <input type="text" id="enter_city" class="custom-form-element form-input required" data-label="City">
                                </div>
                                <div class="col-2">
                                    <select id="enter_state" class="custom-form-element form-select form-select-no-search form-select-no-cancel required" data-label="Select State">
                                        <option value=""></option>
                                        @foreach($states as $state)
                                            @if($state != 'All')
                                            <option value="{{ $state }}">{{ $state }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4">
                                    <select id="enter_county" class="custom-form-element form-select form-select-no-cancel required" data-label="Select County" disabled>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="address-enter-continue-div text-center my-4">
                            <button id="address_enter_continue" class="btn btn-primary btn-lg" type="button" data-toggle="collapse" data-target=".property-container" aria-expanded="false" aria-controls="address_search_container address_enter_container" disabled>
                                Continue <i class="fad fa-chevron-double-right ml-3"></i>
                            </button>
                        </div>
                    </form>
                    <div class="h5 text-center mt-4">
                        <a href=".address-container" class="btn btn-sm btn-secondary" data-toggle="collapse" role="button" aria-expanded="false" aria-controls=".address-container">Go Back To Address Search</a>
                    </div>
                </div>
            </div>

            <div id="mls_match_container" class="property-container collapse">
                <a class="btn-floating btn-primary" data-toggle="collapse" href=".property-container" role="button" aria-expanded="false" aria-controls="#mls_match_container #address_container"><i class="fad fa-chevron-double-left"></i></a>
            </div>
        </div>
    </div>
</div>

@endsection

