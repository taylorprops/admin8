@extends('layouts.main')
@section('title', 'Add Listing')

@section('content')
<div class="page-container page-add-listing">
    <div class="row">
        <div class="col-12">
            <div class="add-transaction-container d-flex justify-content-center align-items-center">
                <div class="add-transaction-div">
                    <h2>Enter The Property Address</h2>
                    <div class="justify-content-center">
                        <input type="text" id="address_search_input" placeholder="Enter Property Address">
                        <input type="text" id="address_unit" class="ml-2" placeholder="Unit">
                    </div>
                    <div class="h4 text-center mt-4"><a href="javascript:void(0)" class="text-orange" id="address_enter_manually">Or Enter Manually</a></div>
                </div>
            </div>
        </div>
    </div><!-- ./ .row -->
</div><!-- ./ .container -->

@endsection

