@extends('layouts.main')
@section('title', 'Add Listing')

@section('content')
<div class="container-fluid page-add-listing">
    <div class="row">
        <div class="col-12">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h2>Add Listing</h2>
                    </div>
                </div><!-- ./ .row -->
            </div><!-- ./ .container -->
            <div class="add-transaction-container">
                <div id="address_search_div" class="address-container collapse show mt-5">
                    <div class="h3 text-center text-orange mb-4">To Begin, Search For The Street Address</div>
                    <div class="d-flex justify-content-center mt-5">
                        <div>
                            <div class="h5 text-primary">Enter Property Address</div>
                            <input type="text" id="address_search_input" placeholder="">
                        </div>
                        <div class="ml-2">
                            <div class="h5 text-primary">Unit</div>
                            <input type="text" id="address_unit" placeholder="">
                        </div>
                    </div>
                    <div class="h5 text-center mt-4">
                        <a href=".address-container" class="btn btn-sm btn-secondary" data-toggle="collapse" role="button" aria-expanded="false" aria-controls=".address-container">Or Enter Manually</a>
                    </div>
                </div>
                <div id="address_enter_div" class="address-container collapse mt-5">
                    <div class="h3 text-center text-orange mb-4">To Begin, Enter The Street Address</div>
                    <form id="enter_address_form">
                        <div class="container">
                            <div class="row">
                                <div class="col-2">
                                    <input type="text" class="custom-form-element form-input" data-label="Street Number">
                                </div>
                                <div class="col-2">
                                    <select class="custom-form-element form-select form-select-no-search" data-label="Dir">
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
                                    <input type="text" class="custom-form-element form-input" data-label="Street Address">
                                </div>
                                <div class="col-2">
                                    <input type="text" class="custom-form-element form-input" data-label="Unit">
                                </div>
                            </div><!-- ./ .row -->
                        </div><!-- ./ .container -->
                    </form>
                    <div class="h5 text-center mt-4">
                        <a href=".address-container" class="btn btn-sm btn-secondary" data-toggle="collapse" role="button" aria-expanded="false" aria-controls=".address-container">Go Back To Address Search</a>
                    </div>

                </div>
            </div>
        </div>
    </div><!-- ./ .row -->
</div><!-- ./ .container -->

@endsection

