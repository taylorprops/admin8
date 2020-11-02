<div class="container commission-container mx-auto p-1 pb-5 mb-5">


    <form id="commission_form">

        <div class="row">
            <div class="col-5">
                <div class="row">
                    <div class="col-5"></div>
                    <div class="col-7">
                        <div class="h5-responsive text-orange mb-4 w-100 border-bottom">Income</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Checks In --}}
        <div class="row popout-row">

            <div class="col-12 col-lg-5 pr-3 pr-lg-0">

                <div class="row">

                    <div class="col-5 text-gray">
                        <div class="py-3 text-right">
                            Checks In
                        </div>
                    </div>

                    <div class="col-7">
                        <div class="popout-action pr-1 pr-lg-4 py-2 bg-blue-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="javascript: void(0)" class="btn btn-sm btn-primary show-view-add-button">View/Add</a>
                                </div>
                                <div class="badge badge-pill badge-primary py-1" id="checks_in_count"></div>
                                <div class="mr-2 font-10 text-success">
                                    <span id="checks_in_total"></span>
                                    <input type="hidden" id="checks_in_total_value" name="checks_in_total_value" class="total">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-7 p-lg-0">

                <div class="popout-div mr-3">

                    <div class="popout top animated fast flipInX w-100 bg-blue-light active">

                        <div class="px-3 pb-3 pt-1">

                            <div class="d-flex justify-content-start align-items-center mb-3">
                                <div class="h4 mt-2 text-primary">Checks In</div>
                                <div class="ml-4">
                                    <a href="javascript: void(0)" class="btn btn-sm btn-success add-check-in-button"><i class="fa fa-plus mr-2"></i> Add</a>
                                </div>
                            </div>

                            <div class="view-add-div checks-in-div p-1 p-sm-2 p-md-4">

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Money In Escrow --}}
        <div class="row">

            <div class="col-12 col-lg-5 pr-2 pr-lg-0">
                <div class="row">
                    <div class="col-5">
                        <div class="h-100 text-gray d-flex justify-content-end align-items-center">
                            Money In Escrow
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="pr-4">
                            <input type="text" class="custom-form-element form-input money-decimal numbers-only text-success text-right pr-2 total" name="money_in_escrow" id="money_in_escrow">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Admin Fee - From Title --}}
        <div class="row">

            <div class="col-12 col-lg-5 pr-2 pr-lg-0">

                <div class="row">
                    <div class="col-5">
                        <div class="h-100 text-gray d-flex justify-content-end align-items-center">
                            Admin Fee - From Title
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="pr-4">
                            <input type="text" class="custom-form-element form-input money-decimal numbers-only text-success text-right pr-2 total" name="admin_fee_from_title" id="admin_fee_from_title">
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <div class="row">

            <div class="col-12 col-lg-5 pr-2 pr-lg-0">

                <div class="row">
                    <div class="col-5"></div>
                    <div class="col-7 border-top mt-3">
                        <h6 class="text-danger mt-2">Income Deductions</h6>
                    </div>
                </div>

            </div>

        </div>

        {{-- Check Deductions --}}
        <div class="row popout-row">

            <div class="col-12 col-lg-5 pr-3 pr-lg-0">

                <div class="row">

                    <div class="col-5 text-gray">
                        <div class="py-3 text-right">
                            Check Deductions
                        </div>
                    </div>

                    <div class="col-7">
                        <div class="popout-action pr-1 pr-lg-4 py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="javascript: void(0)" class="btn btn-sm btn-primary show-view-add-button">View/Add</a>
                                </div>
                                <div class="badge badge-pill badge-primary py-1" id="deductions_count"></div>
                                <div class="mr-2 font-10 text-danger">
                                    <span id="deductions_total"></span>
                                    <input type="hidden" id="income_deductions_total_value" class="total">
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

            <div class="col-12 col-lg-7 p-lg-0">

                <div class="popout-div mr-3">

                    <div class="popout top animated fast flipInX w-100">

                        <div class="px-1 px-sm-3 pb-3 pt-1">

                            <div class="d-flex justify-content-start align-items-center">
                                <div class="h4 mt-2 text-primary">Check Deductions</div>
                                <div class="ml-4">
                                    <a class="btn btn-sm btn-success" data-toggle="collapse" href="#add_check_deduction_div" role="button" aria-expanded="false" aria-controls="add_check_deduction_div"><i class="fa fa-plus mr-2"></i> Add</a>
                                </div>
                            </div>

                            <div class="view-add-div">

                                <div class="collapse" id="add_check_deduction_div">

                                    <div class="d-flex flex-wrap justify-content-start align-items-center">
                                        <div class="mr-2">
                                            <input type="text" class="custom-form-element form-input required" name="check_deduction_description" id="check_deduction_description" data-label="Enter Description">
                                        </div>
                                        <div class="mr-2">
                                            <input type="text" class="custom-form-element form-input money-decimal numbers-only required" name="check_deduction_amount" id="check_deduction_amount" data-label="Enter Amount">
                                        </div>
                                        <div>
                                            <div class="d-flex justify-content-start align-items-center h-100">
                                                <a href="javascript: void(0);" class="btn btn-sm btn-success" id="save_add_check_deduction_button"><i class="fad fa-save mr-2"></i> Save</a>
                                                <a class="btn btn-sm btn-danger" data-toggle="collapse" href="#add_check_deduction_div" role="button" aria-expanded="false" aria-controls="add_check_deduction_div"><i class="fad fa-ban"></i></a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="list-group check-deductions-div mt-3"></div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Admin Fee - Paid By Client --}}
        <div class="row">

            <div class="col-12 col-lg-5 pr-2 pr-lg-0">
                <div class="row">
                    <div class="col-5">
                        <div class="h-100 text-gray d-flex justify-content-end align-items-center">
                            Admin Fee - Paid By Client
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="pr-4">
                            <input type="text" class="custom-form-element form-input money-decimal numbers-only text-danger text-right pr-2 total" name="admin_fee_from_client" id="admin_fee_from_client">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Total Income --}}
        <div class="row no-gutters">

            <div class="col-12 col-lg-5 border-top border-bottom py-2">

                <div class="row">
                    <div class="col-5">
                        <div class="h-100 font-10 d-flex text-success justify-content-end align-items-center">
                            Total Income
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="bg-green-light text-white p-2 mr-4">
                            <div class="d-flex justify-content-end">
                                <div class="mr-1 font-10 text-success">
                                    <span id="total_income"></span>
                                    <input type="hidden" id="total_income_value" name="total_income_value">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>



            </div>

        </div>

        {{-- Commission Deductions --}}
        <div class="row">
            <div class="col-5">
                <div class="row">
                    <div class="col-5"></div>
                    <div class="col-7">
                        <div class="h5-responsive text-orange mb-2 mt-3 w-100 border-bottom">Commission Deductions</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Agent Commission % --}}
        <div class="row">

            <div class="col-12 col-lg-5 pr-2 pr-lg-0">
                <div class="row">
                    <div class="col-5">
                        <div class="h-100 text-gray d-flex justify-content-end align-items-center">
                            Agent Commission
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="pr-4">
                            <div class="d-flex justify-content-start align-items-center">
                                <div class="agent-commission percent d-flex justify-content-start align-items-center">
                                    <select class="custom-form-element form-select form-select-no-search form-select-no-cancel text-center" name="agent_commission_percent" id="agent_commission_percent" >
                                        <option value=""></option>
                                        @foreach($commission_percentages as $percent)
                                        <option value="{{ $percent }}" @if($percent == $agent -> commission_percent) selected @endif>{{ $percent }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fal fa-percentage text-primary ml-1"></i>
                                </div>
                                <div class="mx-5"></div>
                                <div class="w-100">
                                    <input type="text" class="custom-form-element form-input text-danger text-right pr-2 total" readonly name="agent_commission_amount" id="agent_commission_amount">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </form>



    <div class="modal fade draggable" id="edit_check_in_modal" tabindex="-1" role="dialog" aria-labelledby="edit_check_in_modal_title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary draggable-handle">
                    <h4 class="modal-title" id="edit_check_in_modal_title">Edit Check</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="edit_check_in_form">

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="edit-check-preview-div"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="h5 text-orange">Check Details</div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input datepicker required" name="edit_check_date" id="edit_check_date" data-label="Date On Check">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input numbers-only required" name="edit_check_number" id="edit_check_number" data-label="Check Number">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input money-decimal numbers-only required" name="edit_check_amount" id="edit_check_amount" data-label="Check Amount">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="h5 text-orange">Dates</div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input datepicker required" name="edit_date_received" id="edit_date_received" value="{{ date('Y-m-d') }}" data-label="Date Received">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input datepicker" name="edit_date_deposited" id="edit_date_deposited" data-label="Date Deposited">
                            </div>
                        </div>
                        <input type="hidden" name="edit_check_id" id="edit_check_id">
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                    <a class="btn btn-success" id="save_edit_check_in_button" data-dismiss="modal"><i class="fad fa-check mr-2"></i> Save</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade draggable" id="add_check_in_modal" tabindex="-1" role="dialog" aria-labelledby="add_check_in_modal_title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary draggable-handle">
                    <h4 class="modal-title" id="add_check_in_modal_title">Add Check In</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="add_check_in_form" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="h5 text-orange">Upload</div>
                            </div>
                            <div class="col-12">
                                <div><input type="file" accept="application/pdf" class="custom-form-element form-input-file required" name="check_upload" id="check_upload" data-label="Click to search or Drag and Drop files here"></div>
                            </div>
                            <div class="col-12">
                                <div class="check-preview-div"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="h5 text-orange">Check Details</div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input datepicker required" name="check_date" id="check_date" data-label="Date On Check">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input numbers-only required" name="check_number" id="check_number" data-label="Check Number">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input money-decimal numbers-only required" name="check_amount" id="check_amount" data-label="Check Amount">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="h5 text-orange">Dates</div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input datepicker required" name="date_received" id="date_received" value="{{ date('Y-m-d') }}" data-label="Date Received">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input datepicker" name="date_deposited" id="date_deposited" data-label="Date Deposited">
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                    <button type="button" class="btn btn-success" id="save_add_check_in_button"><i class="fad fa-check mr-2"></i> Save</button>
                </div>
            </div>
        </div>
    </div>

</div>


