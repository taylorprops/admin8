@extends('layouts.main')
@section('title', 'Commission Breakdowns')

@section('content')
<div class="container page-commission-breakdowns">
    <div class="row">
        <div class="col-12">
            <h2>Commission Breakdowns</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="text-orange">Commission Checks Queue</h4>

                <a class="btn btn-primary" data-toggle="collapse" href="#add_check_div" role="button" aria-expanded="false" aria-controls="add_check_div">
                    Add Check <i class="fal fa-plus ml-2"></i>
                </a>
            </div>


            <div class="collapse" id="add_check_div">

                <div class="p-3 mt-3 border rounded shadow">

                    <form id="add_check_in_form" enctype="multipart/form-data">

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="h5 text-orange">Agent</div>
                            </div>
                            <div class="col-6">
                                <select class="custom-form-element form-select form-select-no-cancel" id="check_in_agent_id" name="check_in_agent_id" data-label="Agent">
                                    <option value=""></option>
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent -> id }}">{{ $agent -> first_name.' '.$agent -> last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="h5 text-orange">Property Address</div>
                            </div>
                            <div class="col-5">
                                <input type="text" class="custom-form-element form-input required" id="check_in_street" name="check_in_street" data-label="Street Address">
                            </div>
                            <div class="col-3">
                                <input type="text" class="custom-form-element form-input required" id="check_in_city" name="check_in_city" data-label="City">
                            </div>
                            <div class="col-2">
                                <select class="custom-form-element form-select form-select-no-cancel required" id="check_in_state" name="check_in_state" data-label="State">
                                    <option value=""></option>
                                    @foreach($states as $state)
                                        <option value="{{ $state -> state }}">{{ $state -> state }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">
                                <input type="text" class="custom-form-element form-input required" id="check_in_zip" name="check_in_zip" data-label="Zip Code">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="h5 text-orange">Upload</div>
                            </div>
                            <div class="col-12">
                                <div><input type="file" accept="application/pdf" class="custom-form-element form-input-file required" name="check_in_upload" id="check_in_upload" data-label="Click to search or Drag and Drop files here"></div>
                            </div>
                            <div class="col-12">
                                <div class="check-in-preview-div"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="h5 text-orange">Check Details</div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input datepicker required" name="check_in_date" id="check_in_date" data-label="Date On Check">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input numbers-only required" name="check_in_number" id="check_in_number" data-label="Check Number">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input money-decimal numbers-only required" name="check_in_amount" id="check_in_amount" data-label="Check Amount">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="h5 text-orange">Dates</div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input datepicker required" name="check_in_date_received" id="check_in_date_received" value="{{ date('Y-m-d') }}" data-label="Date Received">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input datepicker" name="check_in_date_deposited" id="check_in_date_deposited" data-label="Date Deposited">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-around align-items-center">
                                    <a class="btn btn-danger"data-toggle="collapse" href="#add_check_div" role="button" aria-expanded="false" aria-controls="add_check_div"><i class="fa fa-times mr-2"></i> Cancel</a>
                                    <button type="button" class="btn btn-success" id="save_add_check_in_button"><i class="fad fa-check mr-2"></i> Save</button>
                                </div>
                            </div>
                        </div>

                    </form>

                </div>

            </div>

            <div class="row">
                <div class="col-12">
                    <div class="commission-checks-queue"></div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
