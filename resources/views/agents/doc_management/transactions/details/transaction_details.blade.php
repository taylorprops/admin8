@extends('layouts.main')
@section('title', 'title here')

@section('content')

<div class="container page-transaction-details mb-5">

    <div id="details_header"></div>

    <span id="scroll_to"></span>

    <div class="row">
        <div class="col-md-12 px-1 px-sm-3 mt-3 details-tabs">
            <ul id="tabs" class="nav nav-tabs details-list-group">
                <li class="nav-item"><a href="javascript: void(0)" data-tab="details" data-target="#details_tab" data-toggle="tab" class="nav-link active"><i class="fad fa-home-lg-alt mr-2 d-none d-md-inline-block"></i> Details</a></li>
                <li class="nav-item"><a href="javascript: void(0)" data-tab="members" id="open_members_tab" data-target="#members_tab" data-toggle="tab" class="nav-link"><i class="fad fa-user-friends mr-2 d-none d-md-inline-block"></i> Members</a></li>
                <li class="nav-item"><a href="javascript: void(0)" data-tab="documents" id="open_documents_tab" data-target="#documents_tab" data-toggle="tab" class="nav-link"><i class="fad fa-folder-open mr-2 d-none d-md-inline-block"></i> Documents</a></li>
                <li class="nav-item"><a href="javascript: void(0)" data-tab="checklist" id="open_checklist_tab" data-target="#checklist_tab" data-toggle="tab" class="nav-link"><i class="fad fa-tasks mr-2 d-none d-md-inline-block"></i> Checklist</a></li>
                @if($transaction_type == 'listing')
                <li class="nav-item"><a href="javascript: void(0)" data-tab="contracts" id="open_contracts_tab" data-target="#contracts_tab" data-toggle="tab" class="nav-link"><i class="fad fa-file-signature mr-2 d-none d-md-inline-block"></i> Contracts</a></li>
                @else
                @php
                // agent and admin have different commission tabs
                $commission = 'commission';
                if(auth() -> user() -> group == 'agent') {
                    $commission = 'agent_commission';
                }
                @endphp
                {{-- show listing link if exists --}}
                <li class="nav-item"><a href="javascript: void(0)" data-tab="{{ $commission }}" id="open_{{ $commission }}_tab" data-target="#{{ $commission }}_tab" data-toggle="tab" class="nav-link"><i class="fad fa-sack-dollar mr-2 d-none d-md-inline-block"></i> Commission</a></li>
                @endif
            </ul>

            <div id="details_tabs" class="tab-content details-main-tabs">
                <div id="details_tab" class="tab-pane fade active show">
                    <div class="w-100 my-5 text-center">
                        {!! config('global.vars.loader') !!}
                    </div>
                </div>
                <div id="members_tab" class="tab-pane fade">
                    <div class="w-100 my-5 text-center">
                        {!! config('global.vars.loader') !!}
                    </div>
                </div>
                <div id="documents_tab" class="tab-pane fade">
                    <div class="w-100 my-5 text-center">
                        {!! config('global.vars.loader') !!}
                    </div>
                </div>
                <div id="checklist_tab" class="tab-pane fade">
                    <div class="w-100 my-5 text-center">
                        {!! config('global.vars.loader') !!}
                    </div>
                </div>
                <div id="contracts_tab" class="tab-pane fade">
                    <div class="w-100 my-5 text-center">
                        {!! config('global.vars.loader') !!}
                    </div>
                </div>
                @if(auth() -> user() -> group == 'admin')
                <div id="commission_tab" class="tab-pane fade">
                    <div class="w-100 my-5 text-center">
                        {!! config('global.vars.loader') !!}
                    </div>
                </div>
                @endif
                @if(auth() -> user() -> group == 'agent')
                <div id="agent_commission_tab" class="tab-pane fade">
                    <div class="w-100 my-5 text-center">
                        {!! config('global.vars.loader') !!}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <input type="hidden" id="Listing_ID" value="{{ $property -> Listing_ID }}">
    <input type="hidden" id="Contract_ID" value="{{ $property -> Contract_ID }}">
    <input type="hidden" id="Agent_ID" value="{{ $property -> Agent_ID }}">
    <input type="hidden" id="transaction_type" value="{{ $transaction_type }}">

    <div class="modal fade draggable disable-scrollbars" id="accept_contract_modal" tabindex="-1" role="dialog" aria-labelledby="accept_contract_modal_title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary draggable-handle">
                    <h4 class="modal-title" id="accept_contract_modal_title">Accept Contract</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="h5 text-primary pb-3 border-bottom">Enter the required details <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Required Details" data-content="This information will be used to autopopulate your forms"><i class="fad fa-question-circle ml-2"></i></a></div>

                    <form id="accept_contract_form" autocomplete="off">

                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="h5 text-orange">Buyer's Agent Details</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <a class="btn btn-primary btn-sm my-3" data-toggle="collapse" href="#agent_search_div" role="button" aria-expanded="false" aria-controls="agent_search_div">
                                        <i class="fad fa-search mr-2"></i> Search Agents in Bright MLS
                                    </a>
                                    <div class="collapse p-2 mb-4 border" id="agent_search_div">
                                        <div class="mb-4">Type the Agent's Name, Email or BrightMLS ID</div>
                                        <input type="text" class="custom-form-element form-input" id="agent_search" data-label="Enter Agent's Name, Email or ID" autocomplete="agentsearch">
                                        <div class="search-results-container">
                                            <div class="list-group search-results bg-white p-2 border z-depth-1 w-100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input required" id="accept_contract_buyer_agent_company" data-label="Agent Company">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input" id="accept_contract_buyer_agent_mls_id" data-label="Agent BrightMLS ID">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input required" id="accept_contract_buyer_agent_first" data-label="Agent First Name">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input required" id="accept_contract_buyer_agent_last" data-label="Agent Last Name">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input phone" id="accept_contract_buyer_agent_phone" data-label="Agent Phone">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="email" class="custom-form-element form-input" id="accept_contract_buyer_agent_email" data-label="Agent Email">
                                </div>
                                <input type="hidden" id="accept_contract_buyer_agent_street">
                                <input type="hidden" id="accept_contract_buyer_agent_city">
                                <input type="hidden" id="accept_contract_buyer_agent_state">
                                <input type="hidden" id="accept_contract_buyer_agent_zip">
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="h5 text-orange">Buyer Details</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input required" id="accept_contract_buyer_one_first" data-label="Buyer One First Name">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input required" id="accept_contract_buyer_one_last" data-label="Buyer One Last Name">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input" id="accept_contract_buyer_two_first" data-label="Buyer Two First Name">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input" id="accept_contract_buyer_two_last" data-label="Buyer Two Last Name">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="h5 text-orange">Property Details</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input datepicker required" id="accept_contract_contract_date" data-label="Contract Date">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input datepicker required" id="accept_contract_close_date" data-label="Settle Date">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input money required" id="accept_contract_contract_price" data-label="Sales Price">
                                </div>
                            </div>
                        </div>

                    </form>

                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                    <a class="btn btn-success" id="save_accept_contract_button"><i class="fad fa-check mr-2"></i> Save</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade draggable" id="confirm_import_modal" tabindex="-1" role="dialog" aria-labelledby="import_title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary draggable-handle">
                    <h4 class="modal-title" id="import_title">Confirm Import</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body"> </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                    <a class="btn btn-success modal-confirm-button" id="confirm_import_button"><i class="fad fa-check mr-2"></i> Confirm</a>
                </div>
            </div>
        </div>
    </div>


</div>


@endsection
