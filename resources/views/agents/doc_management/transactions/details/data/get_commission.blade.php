<div class="container commission-container mx-auto p-1 pb-5 mb-5">


    <form id="commission_form">

        <div class="row">

            <div class="col-5">

                <div class="row">
                    <div class="col-5"></div>
                    <div class="col-7">
                        <div class="h5-responsive text-orange mb-2 w-100 border-bottom">Property Details</div>
                    </div>
                </div>

                {{-- Sales Price --}}
                <div class="row">

                    <div class="col-5">
                        <div class="h-100 text-gray d-flex justify-content-end align-items-center">
                            Sales Price
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="pr-4">
                            <input type="text" class="custom-form-element form-input money-decimal numbers-only pr-2 required" name="sales_price" id="sales_price" value="${{-- {{ number_format($property -> ContractPrice, 0) }} --}}">
                        </div>
                    </div>

                </div>

                {{-- Settle Date --}}
                <div class="row">

                    <div class="col-5">
                        <div class="h-100 text-gray d-flex justify-content-end align-items-center">
                            Settle Date
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="pr-4">
                            <input type="text" class="custom-form-element form-input datepicker pr-2 required" name="settle_date" id="settle_date" value="{{-- {{ $property -> CloseDate }} --}}">
                        </div>
                    </div>

                </div>

                {{-- Represent Both Sides --}}
                <div class="row">

                    <div class="col-5">
                        <div class="h-100 text-gray d-flex justify-content-end align-items-center">
                            Represent Both Sides
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="pr-4">
                            <select class="custom-form-element form-select form-select-no-search required" id="both_sides" name="both_sides">
                                <option value=""></option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                    </div>

                </div>

                {{-- Using Heritage Title --}}
                <div class="row">

                    <div class="col-5">
                        <div class="h-100 text-gray d-flex justify-content-end align-items-center">
                            Using Heritage Title
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="pr-4">
                            <select class="custom-form-element form-select form-select-no-search required" id="using_heritage" name="using_heritage">
                                <option value=""></option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                    </div>

                </div>

            </div>

            <div class="col-7">

                <ul class="nav nav-tabs" id="options_tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link options-tab active" id="agent_details_tab" data-toggle="tab" href="#agent_details_div" role="tab" aria-controls="agent_details_div" aria-selected="true">Agent Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link options-tab" id="notes_tab" data-toggle="tab" href="#notes_div" role="tab" aria-controls="notes_div" aria-selected="false">Notes</a>
                    </li>
                </ul>

                <div class="tab-content border-left border-bottom border-right pt-2 pb-1" id="options_tab_content">

                    <div class="tab-pane fade show active" id="agent_details_div" role="tabpanel" aria-labelledby="agent_details_tab">

                        <div class="agent-details-div">
                            <div class="row">
                                <div class="col-4">

                                    <div class="font-weight-bold font-11 text-primary">{{ $agent -> full_name }}</div>

                                    @if($agent -> team_id > 0)
                                    <div class="font-italic font-8">{{ $teams -> GetTeamName($agent -> team_id) }}</div>
                                    @endif

                                    <div class="font-weight-bold font-8 mt-2">
                                        @if($agent -> llc_name != '')
                                            LLC - {{ $agent -> llc_name }}<br>
                                            EIN - {{ $agent -> ein }}<br>
                                        @endif
                                        SS - {{ $agent -> social_security }}
                                    </div>

                                    <div class="text-gray mt-2">
                                        {{ $agent -> address_street }}<br>
                                        {{ $agent -> address_city.', '.$agent -> address_state.' '.$agent -> address_zip }}<br>
                                        {{ $agent -> cell_phone }}<br>
                                        <a href="mailto:{{ $agent -> email }}">{{ $agent -> email }}</a>
                                    </div>

                                </div>

                                <div class="col-4">

                                    <div class="p-2 text-gray">

                                        @if(!stristr($agent -> company, 'referral'))

                                            <div class="d-flex justify-content-between">
                                                <div>Admin Fee Amount</div>
                                                <div>{{ $for_sale == true ? $agent -> admin_fee : $agent -> admin_fee_rentals }}</div>
                                            </div>

                                            <div class="d-flex justify-content-between @if($agent -> balance > 0) text-danger @endif">
                                                <div>Balance Dues</div>
                                                <div>${{ number_format($agent -> balance, 2) ?? '0.00' }}</div>
                                            </div>

                                            <div class="d-flex justify-content-between @if($agent -> balance_eno > 0) text-danger @endif">
                                                <div>Balance E&O</div>
                                                <div>${{ number_format($agent -> balance_eno, 2) ?? '0.00' }}</div>
                                            </div>

                                            @if($agent -> office_rent_amount > 0 || $agent -> balance_rent != 0)
                                                <div class="d-flex justify-content-between @if($agent -> balance_rent > 0) text-danger @endif">
                                                    <div>Balance Rent</div>
                                                    <div>${{ number_format($agent -> balance_rent, 2) ?? '0.00' }}</div>
                                                </div>
                                            @endif

                                            <hr class="my-1">

                                            <div class="d-flex justify-content-between">
                                                <div>Auto Billed</div>
                                                <div>{{ $agent -> auto_bill == 'on' ? 'Yes' : 'No' }}</div>
                                            </div>

                                            <div class="d-flex justify-content-between">
                                                <div>Commission</div>
                                                <div>{{ ucwords($agent -> commission_percent) }}% - Plan {{ ucwords($agent -> commission_plan) }}</div>
                                            </div>

                                        @endif

                                        @if($agent -> owe_other == 'yes')
                                            <div class="wage-garnishments p-1 mt-1 bg-orange-light text-danger rounded">{!! nl2br($agent -> owe_other_notes) !!}</div>
                                        @endif

                                    </div>

                                </div>

                                <div class="col-4">

                                    <div class="font-weight-bold text-primary">Agent Account Notes</div>

                                    <div class="notes-container">
                                        @foreach($agent_notes as $agent_note)
                                            <div class="note-div border-top">
                                                <div class="font-7 text-gray">{{ date('Y-m-d', strtotime($agent_note -> created_at)) }} - <span class="font-italic">{{ $agent_note -> created_by }}</span></div>
                                                {!! nl2br($agent_note -> notes) !!}
                                            </div>
                                        @endforeach
                                    </div>

                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="tab-pane fade" id="notes_div" role="tabpanel" aria-labelledby="notes_tab">



                    </div>

                </div>


            </div>

        </div>




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
                    <div class="col-7 mt-2">
                        <div class="h5-responsive text-orange mb-4 w-100 border-bottom">Income Deductions</div>
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
                                <div class="badge badge-pill badge-primary py-1" id="income_deductions_count"></div>
                                <div class="mr-2 font-10 text-danger">
                                    <span id="income_deductions_total"></span>
                                    <input type="hidden" id="income_deductions_total_value" name="income_deductions_total_value" class="total">
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
                                    <a class="btn btn-sm btn-success" data-toggle="collapse" href="#add_income_deduction_div" role="button" aria-expanded="false" aria-controls="add_income_deduction_div"><i class="fa fa-plus mr-2"></i> Add</a>
                                </div>
                            </div>

                            <div class="view-add-div">

                                <div class="collapse" id="add_income_deduction_div">

                                    <div class="d-flex flex-wrap justify-content-start align-items-center">
                                        <div class="mr-2">
                                            <input type="text" class="custom-form-element form-input required" name="income_deduction_description" id="income_deduction_description" data-label="Enter Description">
                                        </div>
                                        <div class="mr-2">
                                            <input type="text" class="custom-form-element form-input money-decimal numbers-only required" name="income_deduction_amount" id="income_deduction_amount" data-label="Enter Amount">
                                        </div>
                                        <div>
                                            <div class="d-flex justify-content-start align-items-center h-100">
                                                <a href="javascript: void(0);" class="btn btn-sm btn-success" id="save_add_income_deduction_button"><i class="fad fa-save mr-2"></i> Save</a>
                                                <a class="btn btn-sm btn-danger" data-toggle="collapse" href="#add_income_deduction_div" role="button" aria-expanded="false" aria-controls="add_income_deduction_div"><i class="fad fa-ban"></i></a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="p-1 p-sm-2 p-md-4">
                                    <div class="list-group check-deductions-div"></div>
                                </div>

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

            <div class="col-12 col-lg-5 border-top py-2">

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

        {{-- Agent Commission % --}}
        <div class="row">

            <div class="col-12 col-lg-5 pr-2 pr-lg-0 border-bottom">
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
                                    <select class="custom-form-element form-select form-select-no-search form-select-no-cancel text-center total" name="agent_commission_percent" id="agent_commission_percent" >
                                        <option value=""></option>
                                        @foreach($commission_percentages as $percent)
                                        <option value="{{ $percent }}" @if($percent == $agent -> commission_percent) selected @endif>{{ $percent }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fal fa-percentage text-primary ml-1"></i>
                                </div>
                                <div class="mx-5"></div>
                                <div class="w-100">
                                    <input type="text" class="custom-form-element form-input text-success text-right pr-2 total" readonly name="agent_commission_amount" id="agent_commission_amount">
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



        {{-- Admin Fee - Paid By Agent --}}
        <div class="row">

            <div class="col-12 col-lg-5 pr-2 pr-lg-0">
                <div class="row">
                    <div class="col-5">
                        <div class="h-100 text-gray d-flex justify-content-end align-items-center">
                            Admin Fee - Paid By Agent
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="pr-4">
                            <input type="text" class="custom-form-element form-input money-decimal numbers-only text-danger text-right pr-2 total" name="admin_fee_from_agent" id="admin_fee_from_agent">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Commission Deductions --}}
        <div class="row popout-row">

            <div class="col-12 col-lg-5 pr-3 pr-lg-0">

                <div class="row">

                    <div class="col-5 text-gray">
                        <div class="py-3 text-right">
                            Commission Deductions
                        </div>
                    </div>

                    <div class="col-7">
                        <div class="popout-action pr-1 pr-lg-4 py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="javascript: void(0)" class="btn btn-sm btn-primary show-view-add-button">View/Add</a>
                                </div>
                                <div class="badge badge-pill badge-primary py-1" id="commission_deductions_count"></div>
                                <div class="mr-2 font-10 text-danger">
                                    <span id="commission_deductions_total"></span>
                                    <input type="hidden" id="commission_deductions_total_value" name="commission_deductions_total_value" class="total">
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

            <div class="col-12 col-lg-7 p-lg-0">

                <div class="popout-div mr-3 h-100">

                    <div class="popout bottom animated fast flipInX w-100">

                        <div class="px-1 px-sm-3 pb-3 pt-1">

                            <div class="d-flex justify-content-start align-items-center">
                                <div class="h4 mt-2 text-primary">Commission Deductions</div>
                                <div class="ml-4">
                                    <a class="btn btn-sm btn-success" data-toggle="collapse" href="#add_commission_deduction_div" role="button" aria-expanded="false" aria-controls="add_commission_deduction_div"><i class="fa fa-plus mr-2"></i> Add</a>
                                </div>
                            </div>

                            <div class="view-add-div">

                                <div class="collapse" id="add_commission_deduction_div">

                                    <div class="d-flex flex-wrap justify-content-start align-items-center">
                                        <div class="mr-2">
                                            <input type="text" class="custom-form-element form-input required" name="commission_deduction_description" id="commission_deduction_description" data-label="Enter Description">
                                        </div>
                                        <div class="mr-2">
                                            <input type="text" class="custom-form-element form-input money-decimal numbers-only required" name="commission_deduction_amount" id="commission_deduction_amount" data-label="Enter Amount">
                                        </div>
                                        <div>
                                            <div class="d-flex justify-content-start align-items-center h-100">
                                                <a href="javascript: void(0);" class="btn btn-sm btn-success" id="save_add_commission_deduction_button"><i class="fad fa-save mr-2"></i> Save</a>
                                                <a class="btn btn-sm btn-danger" data-toggle="collapse" href="#add_commission_deduction_div" role="button" aria-expanded="false" aria-controls="add_commission_deduction_div"><i class="fad fa-ban"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-1 p-sm-2 p-md-4">
                                    <div class="list-group commission-deductions-div"></div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Commission Income --}}
        <div class="row no-gutters">

            <div class="col-12 col-lg-5 border-top border-bottom py-2">

                <div class="row">
                    <div class="col-5">
                        <div class="h-100 font-10 d-flex text-success justify-content-end align-items-center">
                            Total Commission To Agent
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="bg-green-light text-white p-2 mr-4">
                            <div class="d-flex justify-content-end">
                                <div class="mr-1 font-12 text-success">
                                    <span id="total_commission"></span>
                                    <input type="hidden" id="total_commission_value" name="total_commission_value">
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


