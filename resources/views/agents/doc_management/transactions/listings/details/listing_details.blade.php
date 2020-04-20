@extends('layouts.main')
@section('title', 'title here')

@section('content')

<div class="container page-listing-details mb-5">

    <div id="details_header"></div>

    <span id="scroll_to"></span>

    <div class="row">
        <div class="col-md-12 mt-3 details-tabs">
            <ul id="tabs" class="nav nav-tabs details-list-group">
                <li class="nav-item"><a href="javascript: void(0)" data-tab="details" data-target="#details_tab" data-toggle="tab" class="nav-link active"><i class="fad fa-home-lg-alt mr-2 d-none d-md-inline-block"></i> Details</a></li>
                <li class="nav-item"><a href="javascript: void(0)" data-tab="members" id="open_members_tab" data-target="#members_tab" data-toggle="tab" class="nav-link"><i class="fad fa-user-friends mr-2 d-none d-md-inline-block"></i> Members</a></li>
                <li class="nav-item"><a href="javascript: void(0)" data-tab="documents" id="open_documents_tab" data-target="#documents_tab" data-toggle="tab" class="nav-link"><i class="fad fa-folder-open mr-2 d-none d-md-inline-block"></i> Documents</a></li>
                <li class="nav-item"><a href="javascript: void(0)" data-tab="checklist" id="open_checklist_tab" data-target="#checklist_tab" data-toggle="tab" class="nav-link"><i class="fad fa-tasks mr-2 d-none d-md-inline-block"></i> Checklist</a></li>
                <li class="nav-item"><a href="javascript: void(0)" data-tab="contracts" id="open_contracts_tab" data-target="#contracts_tab" data-toggle="tab" class="nav-link"><i class="fad fa-file-signature mr-2 d-none d-md-inline-block"></i> Contracts</a></li>
                @if(auth() -> user() -> group == 'admin')
                <li class="nav-item"><a href="javascript: void(0)" data-tab="commission" id="open_commission_tab" data-target="#commission_tab" data-toggle="tab" class="nav-link"><i class="fad fa-sack-dollar mr-2 d-none d-md-inline-block"></i> Commission</a></li>
                @endif
            </ul>

            <div id="tabsContent" class="tab-content details-main-tabs">
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
<input type="hidden" id="Listing_ID" value="{{ $listing -> Listing_ID }}">
@endsection
