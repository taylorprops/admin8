<div class="container checklist-container p-1 p-md-4">
    <div class="row">
        <div class="col-12">
            <div class="mb-5">

                <div class="row">
                    <div class="col-12 col-sm-6">
                        <div class="h4-responsive text-primary ml-3"><i class="fad fa-tasks mr-3"></i> {{ ucwords($transaction_type) }} Checklist</div>
                    </div>
                    @if($transaction_type != 'referral')
                    <div class="col-12 col-sm-6">
                        <div class="small text-danger text-right">Wrong Checklist? <a href="javascript: void(0)" class="btn btn-sm btn-primary" id="change_checklist_button" data-checklist-id="{{ $transaction_checklist_id }}"><i class="fad fa-repeat-alt mr-0 mr-sm-2"></i><span class="d-none d-sm-inline-block"> Change Checklist</span></a></div>
                    </div>
                    @endif
                </div>

                <div class="row">
                    @if(auth() -> user() -> group == 'admin')
                    <div class="col-12">
                        <div class="d-flex justify-content-start">
                            <button type="button" class="btn btn-sm btn-primary" id="email_checklist_to_agent_button"><i class="fal fa-envelope mr-2"></i> Email Agent</button>
                            <button type="button" class="btn btn-sm btn-primary" id="add_checklist_item_button"><i class="fal fa-plus mr-2"></i> Add Checklist Item</button>
                        </div>
                    </div>
                    @endif
                </div>

                <hr class="mx-2 mt-0">

                @foreach($checklist_groups as $checklist_group)


                    <div class="h5-responsive text-orange checklist-group-header pb-2 @if(!$loop -> first) mt-4 @else mt-3 @endif">{{ $checklist_group -> resource_name }}</div>

                    @if(count($transaction_checklist_items -> where('checklist_item_group_id', $checklist_group -> resource_id)) > 0)

                        @foreach($transaction_checklist_items -> where('checklist_item_group_id', $checklist_group -> resource_id) as $checklist_item)

                            @php

                            $form_help_html = null;

                            if($checklist_item -> checklist_form_id > 0) {

                                $checklist_item_name = $checklist_items_model -> GetFormName($checklist_item -> checklist_form_id);

                                // details for helper popup
                                $form_help_details = $checklist_items_model -> GetFormHelpDetails($checklist_item -> checklist_form_id);
                                $form_help_details = $form_help_details['details'];

                                $form_help_html = $form_help_details -> helper_text;
                                if($form_help_details -> file_location != '') {
                                    $form_help_html .= '
                                    <hr>View Sample File<br><a href="'.$form_help_details -> file_location.'" class="btn btn-primary" target="_blank">Open File</a>';
                                }

                            } else {

                                $checklist_item_name = $checklist_item -> checklist_item_added_name;

                            }

                            // get docs and notes for checklist item
                            $transaction_checklist_item_id = $checklist_item -> id;
                            $transaction_documents = $transaction_checklist_item_docs_model -> GetDocs($transaction_checklist_item_id);
                            $transaction_documents_count = count($transaction_documents);

                            $transaction_notes = $transaction_checklist_item_notes_model -> GetNotes($transaction_checklist_item_id, '');
                            $transaction_notes_count = count($transaction_notes);
                            $notes_count_unread = $transaction_notes -> where('note_status', 'unread') -> where('note_user_id', '!=', auth() -> user() -> user_id) -> count();

                            // get status
                            $status_details = $transaction_checklist_items_model -> GetStatus($transaction_checklist_item_id);
                            $status = $status_details -> status;
                            $classes = $status_details -> classes;
                            $fa = $status_details -> fa;
                            $show_mark_required = $status_details -> show_mark_required;
                            $show_mark_not_required = $status_details -> show_mark_not_required;
                            $helper_text = $status_details -> helper_text;

                            // review status
                            $item_review_status = $checklist_item -> checklist_item_status;

                            $text_color = 'text-primary';
                            if($status != 'Required' && $status != 'Rejected') {
                                $text_color = 'text-gray';
                            }
                            @endphp

                            <div class="checklist-item-div p-1 border-bottom">

                                <div class="row">

                                    <div class="col-12 col-xl-5">
                                        <div class="checklist-item-details d-flex justify-content-start flex-wrap flex-sm-nowrap align-items-center h-100 mb-lg-0">

                                            <div class="my-1 d-flex justify-content-start align-items-center">
                                                <div class="status-badge badge {{ $classes }} mr-2" title="{{ $helper_text }}">
                                                    {!! $fa . ' ' . $status !!}
                                                </div>

                                                <div>
                                                    <div class="dropdown">

                                                        <button class="btn btn-primary dropdown-toggle checklist-item-dropdown" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>

                                                        <div class="dropdown-menu dropdown-primary">

                                                            <a class="dropdown-item mark-required no @if(!$show_mark_not_required) d-none @else d-block @endif" href="javascript: void(0)" data-checklist-item-id="{{ $transaction_checklist_item_id }}" data-required="no">Make If Applicable</a>

                                                            <a class="dropdown-item mark-required yes @if(!$show_mark_required) d-none @else d-block @endif" href="javascript: void(0)" data-checklist-item-id="{{ $transaction_checklist_item_id }}" data-required="yes">Make Required</a>

                                                            <a class="dropdown-item remove-checklist-item" href="javascript: void(0)" data-checklist-item-id="{{ $transaction_checklist_item_id }}">Remove</a>

                                                        </div>

                                                    </div>
                                                </div>

                                            </div>

                                            <div class="mx-2 my-2 helper-wrapper">
                                                <a href="javascript: void(0)" @if($checklist_item -> checklist_form_id > 0) role="button" class="checklist-item-helper" data-toggle="popover" data-html="true" data-trigger="focus" title="Document Details" data-content="{{ $form_help_html }}" @endif>
                                                    <i class="fad fa-question-circle @if($checklist_item -> checklist_form_id == 0) text-white @endif"></i>
                                                </a>
                                            </div>

                                            <div class="mx-md-2 my-2 checklist-item-name {{ $text_color }}">{{ $checklist_item_name }}</div>

                                        </div>

                                    </div>
                                    <div class="col-12 col-xl-7">

                                        <div class="row mt-1">

                                            <div class="col-12 col-sm-6 col-xl-4">

                                                <div class="d-flex justify-content-start align-items-center mr-3 mb-1 p-1 bg-light">
                                                    <div class="font-weight-bold text-primary mr-2 checklist-attachment">Docs</div>
                                                    <div>
                                                        <button type="button" class="btn btn-sm btn-success add-document-button" data-checklist-id="{{ $transaction_checklist_id }}" data-checklist-item-id="{{ $transaction_checklist_item_id }}" data-target="documents_div_{{ $transaction_checklist_item_id }}"><i class="fa fa-plus"></i></button>
                                                    </div>

                                                    <div>
                                                        <button type="button" class="btn btn-sm btn-primary view-docs-button" data-toggle="collapse" data-target="#documents_div_{{ $transaction_checklist_item_id }}" aria-expanded="false" aria-controls="documents_div_{{ $transaction_checklist_item_id }}" @if($transaction_documents_count == 0) disabled @endif>View <span class="badge badge-pill bg-white text-danger font-weight-bold py-1 px-2 ml-2 doc-count">{{ $transaction_documents_count }}</span></button>
                                                    </div>

                                                </div>

                                            </div>

                                            <div class="col-12 col-sm-6 col-xl-4">

                                                <div class="d-flex justify-content-start align-items-center mr-3 mb-1 p-1 bg-light">

                                                    <div class="font-weight-bold text-primary mr-2 checklist-attachment">Comments</div>

                                                    <div>
                                                        <button type="button" class="btn btn-sm btn-success add-notes-button" data-add-notes-div="add_notes_div_{{ $transaction_checklist_item_id }}" data-toggle="collapse" data-target="#notes_div_{{ $transaction_checklist_item_id }}" aria-expanded="false" aria-controls="notes_div_{{ $transaction_checklist_item_id }}"><i class="fa fa-plus"></i></button>
                                                    </div>

                                                    <div>
                                                        <button type="button" class="btn btn-sm @if($notes_count_unread > 0) btn-secondary @else btn-primary @endif view-notes-button" data-toggle="collapse" data-target="#notes_div_{{ $transaction_checklist_item_id }}" aria-expanded="false" aria-controls="notes_div_{{ $transaction_checklist_item_id }}" @if($transaction_notes_count == 0) disabled @endif>@if($notes_count_unread > 0) New! @else View @endif<span class="badge badge-pill bg-white text-danger font-weight-bold py-1 px-2 ml-2">{{ $transaction_notes_count }}</span></button>
                                                    </div>

                                                </div>

                                            </div>

                                            <div class="col-12 col-sm-6 col-xl-4">

                                                @if(auth() -> user() -> group == 'admin')

                                                    @php
                                                    $bg_color = 'bg-light';
                                                    if($item_review_status == 'accepted') {
                                                        $bg_color = 'bg-green-light';
                                                    } else if($item_review_status == 'rejected') {
                                                        $bg_color = 'bg-red-light';
                                                    }
                                                    @endphp

                                                    <div class="review-options h-100 {{ $bg_color }}">

                                                        <div class="@if($item_review_status == 'not_reviewed') d-flex @else d-none @endif justify-content-around align-items-center mb-1 item-not-reviewed w-100 h-100 p-1">
                                                            <button type="button" class="btn btn-sm btn-success accept-checklist-item-button" data-checklist-item-id="{{ $transaction_checklist_item_id }}" @if($transaction_documents_count == 0) disabled @endif><i class="fa fa-check mr-2"></i> Accept</button>
                                                            <button type="button" class="btn btn-sm btn-danger reject-checklist-item-button" data-checklist-item-id="{{ $transaction_checklist_item_id }}" @if($checklist_item -> checklist_item_required == 'yes') data-required="yes" @endif @if($transaction_documents_count == 0) disabled @endif><i class="fa fa-minus-circle mr-2"></i> Reject</button>
                                                        </div>

                                                        <div class="@if($item_review_status == 'accepted') d-flex @else d-none @endif justify-content-around align-items-center mb-xl-1 item-accepted w-100 h-100 p-1">
                                                            <div class="text-success">
                                                                <i class="fad fa-check-circle mr-2"></i> Accepted
                                                            </div>
                                                            <div class="small ml-5">
                                                                <a href="javascript: void(0)" class="undo-accepted" data-checklist-item-id="{{ $transaction_checklist_item_id }}" @if($checklist_item -> checklist_item_required == 'yes') data-required="yes" @endif ><i class="fad fa-undo mr-1"></i> Undo</a>
                                                            </div>
                                                        </div>

                                                        <div class="@if($item_review_status == 'rejected') d-flex @else d-none @endif justify-content-around align-items-center mb-xl-1 item-rejected w-100 h-100 p-1">
                                                            <div class="text-danger">
                                                                <i class="fad fa-times-circle mr-2"></i> Rejected
                                                            </div>
                                                            <div class="small ml-3">
                                                                <a href="javascript: void(0)" class="undo-rejected" data-checklist-item-id="{{ $transaction_checklist_item_id }}" @if($checklist_item -> checklist_item_required == 'yes') data-required="yes" @endif ><i class="fad fa-undo mr-1"></i> Undo</a>
                                                            </div>
                                                        </div>

                                                    </div>

                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-12">

                                        <div class="row">
                                            <div class="col-12 col-lg-8 px-0 px-sm-2 mx-auto">
                                                <div class="collapse documents-collapse mx-4 mx-auto bg-white" id="documents_div_{{ $transaction_checklist_item_id }}">

                                                    <div class="p-3 mt-2 mb-4">

                                                        <div class="row">
                                                            <div class="col-12 mb-3">
                                                                <div class="h5 text-primary float-left">Submitted Documents</div>
                                                                <a class="text-danger float-right" data-toggle="collapse" href="#documents_div_{{ $transaction_checklist_item_id }}" aria-expanded="false" aria-controls="documents_div_{{ $transaction_checklist_item_id }}">
                                                                    <i class="fad fa-times-circle fa-2x"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        @foreach($transaction_documents as $transaction_document)
                                                            @php
                                                            $document_id = $transaction_document -> document_id;
                                                            $doc_info = $documents_model -> GetDocInfo($document_id);

                                                            $transaction_doc_notes = $transaction_checklist_item_notes_model -> GetNotes($transaction_checklist_item_id, $document_id);
                                                            $transaction_doc_notes_count = count($transaction_doc_notes);
                                                            $transaction_doc_notes_count_unread = $transaction_doc_notes -> where('note_status', 'unread') -> where('note_user_id', '!=', auth() -> user() -> user_id) -> count();
                                                            @endphp

                                                            <div class="d-flex justify-content-between align-items-center border-bottom document-row mb-2">
                                                                <div class="d-flex justify-content-start align-items-center">

                                                                    <div class="mx-2"><a href="{{ $doc_info['file_location_converted'] }}" target="_blank" class="btn btn-sm btn-primary">View</a></div>

                                                                    <div>
                                                                        {{ $doc_info['file_name'] }}
                                                                        <br>
                                                                        <span class="small text-gray">Added: {{ date('n/j/Y g:i:sA', strtotime($transaction_document -> created_at)) }} </span>
                                                                    </div>

                                                                </div>
                                                                <div>
                                                                    <button type="button" class="btn btn-sm btn-danger float-right delete-doc-button" data-document-id="{{ $document_id }}" data-target="#documents_div_{{ $transaction_checklist_item_id }}" @if($item_review_status == 'accepted' && $transaction_document -> doc_status == 'viewed') disabled @endif>
                                                                        <i class="fa fa-times text-white"></i>
                                                                    </button>
                                                                </div>
                                                            </div>

                                                        @endforeach

                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12 col-lg-8 px-0 px-sm-2 mx-auto">

                                                <div class="collapse notes-collapse mx-4 mx-auto bg-white" id="notes_div_{{ $transaction_checklist_item_id }}">

                                                    <div class="p-3  mt-2 mb-4">

                                                        <div class="row">

                                                            <div class="col-12">
                                                                <div class="d-flex justify-content-between">
                                                                    <div class="h5 text-primary float-left">Comments</div>
                                                                    <div class="mb-2">
                                                                        <a class="text-danger" data-toggle="collapse" href="#notes_div_{{ $transaction_checklist_item_id }}" aria-expanded="false" aria-controls="notes_div_{{ $transaction_checklist_item_id }}"><i class="fad fa-times-circle fa-2x"></i></a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-12 col-md-3">
                                                                <div class="add-notes-div" id="add_notes_div_{{ $transaction_checklist_item_id }}">

                                                                    <div>
                                                                        <textarea class="custom-form-element form-textarea" id="add_notes_textarea_{{ $transaction_checklist_item_id }}" data-notes-collapse="notes_div_{{ $transaction_checklist_item_id }}" data-checklist-id="{{ $transaction_checklist_id }}" data-checklist-item-id="{{ $transaction_checklist_item_id }}" data-label="Add Comments"></textarea>
                                                                    </div>
                                                                    <div class="text-center">
                                                                        <button class="btn btn-success save-notes-button" data-textarea="add_notes_textarea_{{ $transaction_checklist_item_id }}">Save</button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-12 col-md-9">
                                                                <div>

                                                                    <div class="notes-container px-2">

                                                                        @foreach($transaction_notes as $transaction_note)
                                                                        @php
                                                                        $user = $users -> where('user_id', $transaction_note -> note_user_id) -> first();
                                                                        $username = $user -> name;

                                                                        $unread = null;
                                                                        if($transaction_note -> note_status == 'unread' && $transaction_note -> note_user_id != auth() -> user() -> user_id) {
                                                                            $unread = 'unread';
                                                                        }

                                                                        @endphp
                                                                        <div class="p-2 pb-0 mt-2 note-div @if($unread) border-orange @else border-bottom @endif bg-white">

                                                                            <div class="row">
                                                                                <div class="col-6 col-lg-3">
                                                                                    <div class="text-gray font-italic">{{ $username }}</div>
                                                                                    <div class="text-gray small mt-1">{{ date('n/j/Y g:i:sA', strtotime($transaction_note -> created_at)) }}</div>
                                                                                </div>
                                                                                <div class="col-12 col-lg-7">
                                                                                    <div class="d-flex align-items-center h-100">{!! $transaction_note -> notes !!}</div>
                                                                                </div>
                                                                                <div class="col-12 col-lg-2">
                                                                                    <div class="d-flex justify-content-end align-items-center h-100">
                                                                                        @if($transaction_note -> note_status == 'unread')

                                                                                            @if($transaction_note -> note_user_id != auth() -> user() -> user_id)

                                                                                                <button class="btn btn-success btn-sm mark-read-button mb-0" data-note-id="{{ $transaction_note -> id }}" data-notes-collapse="notes_div_{{ $transaction_checklist_item_id }}"><i class="fa fa-check mr-2"></i> Mark Read</button>

                                                                                            @else

                                                                                                <span class="text-gray small">Not Read</span>

                                                                                            @endif

                                                                                        @else

                                                                                            <span class="text-success small"><i class="fa fa-check"></i> Read</span>

                                                                                        @endif
                                                                                    </div>
                                                                                </div>

                                                                            </div>

                                                                        </div>
                                                                        @endforeach

                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>

                        @endforeach

                    @else
                        <div class="text-gray">No Required Forms for this Group</div>
                    @endif

                @endforeach

            </div>
        </div>
    </div>
</div>

<input type="hidden" id="transaction_checklist_id" value="{{ $transaction_checklist_id }}">

<div class="modal fade draggable" id="email_agent_modal" tabindex="-1" role="dialog" aria-labelledby="email_agent_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary draggable-handle">
                <h4 class="modal-title" id="email_agent_modal_title">Email Agent</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="email_agent_form">
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-2">
                                    <div class="h-100 d-flex justify-content-end align-items-center">
                                        <div>From:</div>
                                    </div>
                                </div>
                                <div class="col-10 pl-0">
                                    <input type="text" class="custom-form-element form-input" id="email_agent_from" value="{{ \Auth::user() -> name }} <{{ \Auth::user() -> email }}>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-2">
                                    <div class="h-100 d-flex justify-content-end align-items-center">
                                        <div>To:</div>
                                    </div>
                                </div>
                                <div class="col-10 pl-0">
                                    <input type="text" class="custom-form-element form-input" id="email_agent_to" value="{{ $agent -> name }} <{{ $agent -> email }}>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-2">
                                    <div class="h-100 d-flex justify-content-end align-items-center">
                                        <div>CC:</div>
                                    </div>
                                </div>
                                <div class="col-10 pl-0">
                                    <input type="text" class="custom-form-element form-input" id="email_agent_cc">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-2">
                            <div class="h-100 d-flex justify-content-end align-items-center">
                                <div>Subject:</div>
                            </div>
                        </div>
                        <div class="col-10 pl-0">
                            <input type="text" class="custom-form-element form-input" id="email_agent_subject" value="{{ $property -> FullStreetAddress }} {{ $property -> City }}, {{ $property -> StateOrProvince }} {{ $property -> PostalCode }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2">
                            <div class="h-100 d-flex justify-content-end align-items-center">
                                <div>Message:</div>
                            </div>
                        </div>
                        <div class="col-10 pl-0">
                            <textarea class="custom-form-input form-textarea" id="email_agent_message" rows="4">&#13;&#10; &#13;&#10; Thank you,&#13;&#10; {{ \Auth::user() -> name }}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div id="email_agent_checklist_details"></div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-success" id="send_email_agent_button"><i class="fad fa-share mr-2"></i> Send Message</a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade draggable" id="confirm_remove_checklist_item_modal" tabindex="-1" role="dialog" aria-labelledby="remove_checklist_item_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary draggable-handle">
                <h4 class="modal-title" id="remove_checklist_item_title">Remove Checklist Item</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-start align-items-center p-3">
                                <div class="mr-3 text-danger"><i class="fad fa-exclamation-circle fa-2x"></i></div>
                                <div>
                                    Are you sure you want to remove this checklist item? All notes and assigned documents will also be removed.
                                    <div class="text-center w-100 mt-2">
                                        Continue?
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-success modal-confirm-button" id="confirm_remove_checklist_item_button"><i class="fad fa-check mr-2"></i> Confirm</a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade draggable" id="add_checklist_item_modal" tabindex="-1" role="dialog" aria-labelledby="add_checklist_item_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary draggable-handle">
                <h4 class="modal-title" id="add_checklist_item_modal_title">Add Checklist Item</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="add_checklist_item_form">

                    <div class="row">
                        <div class="col-12 col-md-6">
                            <select class="custom-form-element form-select form-select-no-search form-select-no-cancel required" id="add_checklist_item_group_id" data-label="Select Form Group">
                                @foreach($checklist_groups as $checklist_group)
                                    <option value="{{ $checklist_group -> resource_id }}">{{ $checklist_group -> resource_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">

                        <div class="col-12 col-md-6">

                            <div class="text-gray">Enter Name</div>
                            <input type="text" class="custom-form-element form-input" id="add_checklist_item_name" data-label="Enter Item Name">

                        </div>

                    </div>

                    <div class="row mt-3">

                        <div class="col-12">

                            <div class="text-gray">Or Select Standard Form</div>

                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <input type="text" class="custom-form-element form-input" id="form_search" data-label="Search">
                                </div>
                                <div class="col-12 col-md-6">
                                    <select class="custom-form-element form-select form-select-no-cancel form-select-no-search select-form-group mt-3" data-label="Select Form Group">
                                        <option value="all">All</option>
                                        @foreach($form_groups as $form_group)
                                        <option value="{{ $form_group -> resource_id }}" @if($loop -> first) selected @endif>{{ $form_group -> resource_state }} @if($form_group -> resource_state != $form_group -> resource_name) | {{ $form_group -> resource_name }} @endif</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-groups-container mt-3" data-simplebar data-simplebar-auto-hide="false">

                                        @foreach($form_groups as $form_group)

                                            <ul class="list-group form-group-div mb-3" data-form-group-id="{{ $form_group -> resource_id }}">
                                                <li class="list-group-header text-orange">
                                                    {{ $form_group -> resource_state }}
                                                    @if($form_group -> resource_state != $form_group -> resource_name) | {{ $form_group -> resource_name }} @endif
                                                </li>

                                                @php
                                                $forms = $files -> formGroupFiles($form_group -> resource_id, null, null, '');
                                                $forms = $forms['forms_available'];
                                                @endphp

                                                @foreach($forms as $form)

                                                    <li class="list-group-item list-group-item-action form-name" data-form-id="{{ $form -> file_id }}" data-form-name="{{ $form -> file_name_display }}">
                                                        <div class="d-flex justify-content-between">

                                                            <div title="{{ $form -> file_name_display }}">
                                                                <a href="{{ $form -> file_location }}" class="btn btn-sm btn-primary mr-2 form-link" target="_blank">View</a>
                                                                <a href="javascript: void(0)" class="btn btn-sm btn-primary mr-2">Select</a>
                                                                <span class="d-none checked-div mr-3"><i class="fa fa-check-circle text-success"></i></span>
                                                                <span class="text-primary form-name-display">{{ $form -> file_name_display }}</span>
                                                            </div>
                                                            <div>
                                                                @php $tags = explode(',', $form -> sale_type); @endphp
                                                                @foreach($tags as $tag)
                                                                    <span class="badge badge-pill text-white ml-1 form-pill" style="background-color: {{ $resource_items -> getTagColor($tag) }}">{{ $resource_items -> getResourceName($tag) }}</span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </li>

                                                @endforeach

                                            </ul>

                                        @endforeach

                                    </div>
                                </div>
                            </div>




                        </div>

                    </div>

                    <input type="hidden" id="add_checklist_item_checklist_id" value="{{ $transaction_checklist_id }}">
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-success" id="save_add_checklist_item_button"><i class="fad fa-check mr-2"></i> Save</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="reject_document_modal" tabindex="-1" role="dialog" aria-labelledby="reject_document_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary draggable-handle">
                <h4 class="modal-title" id="reject_document_modal_title">Reject Document</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="rejected-reasons-container">
                            <form id="rejected_reason_form">
                                Enter the reason you are rejecting the documents for this checklist item<br>
                                <input type="text" class="custom-form-element form-input required" id="rejected_reason" placeholder="Enter Reason Rejected">
                                Or select from the list below
                                <div class="rejected-reasons-div list-group w-100">
                                    @foreach($rejected_reasons as $rejected_reason)
                                        <div class="list-group-item list-group-item-action rejected-reason" data-reason="{{ $rejected_reason -> resource_name }}">
                                            <div class="d-flex justify-content-start align-items-center">
                                                <div class="rejected-selected text-success d-none"><i class="fad fa-check-circle"></i></div>
                                                <div class="ml-3"><a href="javascript:void(0)" class="w-100">{{ $rejected_reason -> resource_name }}</a></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-success" id="save_reject_document_button"><i class="fad fa-check mr-2"></i> Save</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable " id="change_checklist_modal" tabindex="-1" role="dialog" aria-labelledby="change_checklist_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary draggable-handle">
                <h4 class="modal-title" id="change_checklist_modal_title">Change Checklist</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <h5 class="text-primary">Edit the options below</h5>

                            <form id="change_checklist_form">

                                <div class="container property-options">

                                    <div class="row my-3">
                                        <div class="col-12">
                                            <select class="custom-form-element form-select form-select-no-search form-select-no-cancel transaction-option-trigger required" name="listing_type" id="listing_type" data-label="Sale/Rental" required>
                                                <option value="sale" @if($checklist -> checklist_sale_rent == 'sale') selected @endif>Sale</option>
                                                <option value="rental" @if($checklist -> checklist_sale_rent == 'rental') selected @endif>Rental</option>
                                                <option value="both" @if($checklist -> checklist_sale_rent == 'both') selected @endif>Both</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <div class="col-12">
                                            <select class="custom-form-element form-select form-select-no-search form-select-no-cancel transaction-option-trigger required" name="property_type" id="property_type" data-label="Listing Type" required>
                                                @foreach($property_types as $property_type)
                                                <option value="{{ $property_type -> resource_name}}" @if($property_type -> resource_id == $checklist -> checklist_property_type_id) selected @endif>{{ $property_type -> resource_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row my-3 property-sub-type">
                                        <div class="col-12">
                                            <select class="custom-form-element form-select form-select-no-search form-select-no-cancel transaction-option-trigger required" name="property_sub_type" id="property_sub_type" data-label="Property Type" required>
                                                @foreach($property_sub_types as $property_sub_type)
                                                    @if($property_sub_type -> resource_name != 'For Sale By Owner')
                                                    <option value="{{ $property_sub_type -> resource_name }}" @if($property_sub_type -> resource_id == $checklist -> checklist_property_sub_type_id) selected @endif>{{ $property_sub_type -> resource_name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row my-3 hoa disclosure">
                                        <div class="col-12">
                                            <select class="custom-form-element form-select form-select-no-search form-select-no-cancel required" name="hoa_condo" id="hoa_condo" data-label="HOA/Condo Association" required>
                                                <option value="hoa" @if($transaction_checklist_hoa_condo == 'hoa') selected @endif>HOA Fees</option>
                                                <option value="condo" @if($transaction_checklist_hoa_condo == 'condo') selected @endif>Condo Fees</option>
                                                <option value="none" @if($transaction_checklist_hoa_condo == 'none') selected @endif>None</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row my-3 year-built">
                                        <div class="col-12">
                                            <input type="text" class="custom-form-element form-input numbers-only required" name="year_built" id="year_built" value="{{ $transaction_checklist_year_built }}" data-label="Year Built" required>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-success" id="save_change_checklist_button"><i class="fad fa-check mr-2"></i> Save</a>
            </div>

        </div>
    </div>
</div>

<div class="modal fade draggable disable-scrollbars" id="confirm_change_checklist_modal" tabindex="-1" role="dialog" aria-labelledby="change_checklist_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary draggable-handle">
                <h4 class="modal-title" id="change_checklist_title">Change Checklist Title</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <i class="fad fa-exclamation-triangle fa-lg text-danger mr-2"></i> The checklist will be replaced to include the documents required for the new listing checklist. Any relevant documents will be kept in the checklist but some may need to be added or replaced.
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-success modal-confirm-button" id="confirm_change_checklist_button"><i class="fad fa-check mr-2"></i> Continue</a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade draggable disable-scrollbars" id="add_document_modal" tabindex="-1" role="dialog" aria-labelledby="add_document_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <form id="add_document_form">
                <div class="modal-header bg-primary draggable-handle">
                    <h4 class="modal-title" id="add_document_modal_title">Add Document To Checklist Item</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                @if(count($documents_available) > 0)
                                    @foreach($folders as $folder)
                                        @if(count($documents_available -> where('folder', $folder -> id)) > 0)
                                            <div class="h5-responsive text-orange">{{ $folder -> folder_name }}</div>
                                            @foreach($documents_available -> where('folder', $folder -> id) as $document_available)
                                                <div class="d-flex justify-content-start align-items-center border-bottom">
                                                    <div>
                                                        <button type="button" class="btn btn-sm btn-success select-document-button" data-document-id="{{ $document_available -> id }}">Add</button>
                                                    </div>
                                                    <div class="ml-2">{{ $document_available -> file_name_display }}</div>
                                                </div>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @else
                                    <div class="h5-responsive text-danger"><i class="fad fa-exclamation-triangle mr-2"></i> You do not have any available documents yet. Add documents in the "Documents" tab.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                </div>
                <input type="hidden" id="add_document_checklist_id">
                <input type="hidden" id="add_document_checklist_item_id">
            </form>
        </div>
    </div>
</div>

<div class="modal fade draggable disable-scrollbars" id="confirm_delete_checklist_item_doc_modal" tabindex="-1" role="dialog" aria-labelledby="delete_checklist_item_doc_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary draggable-handle">
                <h4 class="modal-title" id="delete_checklist_item_doc_title">Delete Document</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            Delete Document From Checklist Item?
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-success modal-confirm-button" id="delete_checklist_item_doc_button"><i class="fad fa-check mr-2"></i> Confirm</a>
            </div>
        </div>
    </div>
</div>
