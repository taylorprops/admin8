<div class="container checklist-container p-1 p-md-4">
    <div class="row">
        <div class="col-12">
            <div class="mb-5">

                <div class="row">
                    <div class="col-12 col-sm-6">
                        <div class="d-flex justify-content-start align-items-center">
                            <div class="font-14 text-primary ml-3"><i class="fad fa-tasks mr-3"></i> {{ $checklist_type }} Checklist</div>
                            @if(auth() -> user() -> group == 'admin')
                            <div class="ml-4">
                                <button type="button" class="btn btn-sm btn-primary email-agent-button"><i class="fal fa-envelope mr-2"></i> Email Agent</button>
                            </div>
                            @endif
                        </div>
                    </div>
                    @if($transaction_type != 'referral')
                    <div class="col-12 col-sm-6">
                        <div class="small text-danger text-right">Wrong Checklist? <a href="javascript: void(0)" class="btn btn-sm btn-primary" id="change_checklist_button" data-checklist-id="{{ $transaction_checklist_id }}"><i class="fad fa-repeat-alt mr-0 mr-sm-2"></i><span class="d-none d-sm-inline-block"> Change Checklist</span></a></div>
                    </div>
                    @endif
                </div>


                <hr class="mx-2 mt-0">

                @foreach($checklist_groups as $checklist_group)

                    @php
                    $group_name = $checklist_group -> resource_name;
                    if($group_name == 'Transaction Docs') {
                        $group_name = 'Listing Docs';
                        if($transaction_type == 'contract') {
                            $group_name = 'Contract Docs';
                            if($for_sale == false) {
                                $group_name = 'Lease Docs';
                            }
                        } else if($transaction_type == 'referral') {
                            $group_name = 'Referral Docs';
                        }
                    }
                    @endphp

                    {{-- Remove release docs for rentals --}}
                    @if($group_name == 'Release Docs' && $for_sale == false)
                    @else

                        <div class="h5 text-orange checklist-group-header pb-2 @if(!$loop -> first) mt-4 @else mt-3 @endif">
                            {{ $group_name }}
                            @if(auth() -> user() -> group == 'admin')
                            <button type="button" class="btn btn-sm btn-success add-checklist-item-button" data-group-id="{{ $checklist_group -> resource_id }}"><i class="fal fa-plus"></i></button>
                            @endif
                        </div>

                        <div>

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
                                $checklist_item_id = $checklist_item -> id;
                                $transaction_documents = $transaction_checklist_item_docs_model -> GetDocs($checklist_item_id);
                                $transaction_documents_count = count($transaction_documents);

                                $notes = $transaction_checklist_item_notes_model -> GetNotes($checklist_item_id);
                                $notes_count = count($notes);

                                $notes_count_unread = $notes -> where('note_status', 'unread');
                                if(auth() -> user() -> group == 'agent') {
                                    $notes_count_unread = $notes_count_unread -> where('note_user_id', '!=', auth() -> user() -> id) -> count();
                                } else if(auth() -> user() -> group == 'admin') {
                                    $notes_count_unread = $notes_count_unread -> where('Agent_ID', '>', '0') -> count();
                                }


                                // get status
                                $status_details = $transaction_checklist_items_model -> GetStatus($checklist_item_id);
                                $status = $status_details -> status;
                                $badge_classes = $status_details -> agent_classes;
                                if(auth() -> user() -> group == 'admin') {
                                    $badge_classes = $status_details -> admin_classes;
                                }
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
                                                    <div class="status-badge badge {{ $badge_classes }} mr-2" title="{{ $helper_text }}">
                                                        {!! $fa . ' ' . $status !!}
                                                    </div>

                                                    @if(auth() -> user() -> group == 'admin')
                                                        <div>
                                                            <div class="dropdown">

                                                                <button class="btn btn-primary dropdown-toggle checklist-item-dropdown" type="button" id="checklist_item_options_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>

                                                                <div class="dropdown-menu dropdown-primary">

                                                                    <a class="dropdown-item mark-required no @if(!$show_mark_not_required) d-none @else d-block @endif" href="javascript: void(0)" data-checklist-item-id="{{ $checklist_item_id }}" data-required="no">Make If Applicable</a>

                                                                    <a class="dropdown-item mark-required yes @if(!$show_mark_required) d-none @else d-block @endif" href="javascript: void(0)" data-checklist-item-id="{{ $checklist_item_id }}" data-required="yes">Make Required</a>

                                                                    <a class="dropdown-item remove-checklist-item" href="javascript: void(0)" data-checklist-item-id="{{ $checklist_item_id }}">Remove</a>

                                                                </div>

                                                            </div>
                                                        </div>
                                                    @endif

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

                                                <div class="col-12 col-sm-6 col-md-4 ">

                                                    <div class="checklist-item-options d-flex justify-content-between align-items-center p-1 my-1 my-md-0 mx-auto bg-light">
                                                        <div class="font-weight-bold text-primary checklist-attachment">Docs</div>
                                                        <div>
                                                            <button type="button" class="btn btn-sm btn-success add-document-button" data-checklist-id="{{ $transaction_checklist_id }}" data-checklist-item-id="{{ $checklist_item_id }}" data-target="documents_div_{{ $checklist_item_id }}"><i class="fa fa-plus"></i></button>
                                                        </div>

                                                        <div>
                                                            <button type="button" class="btn btn-sm btn-primary view-docs-button" data-toggle="collapse" data-target="#documents_div_{{ $checklist_item_id }}" aria-expanded="false" aria-controls="documents_div_{{ $checklist_item_id }}" @if($transaction_documents_count == 0) disabled @endif>View <span class="badge badge-pill bg-white text-danger font-weight-bold py-1 px-2 ml-2 doc-count">{{ $transaction_documents_count }}</span></button>
                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="col-12 col-sm-6 col-md-4 mx-auto">

                                                    <div class="checklist-item-options d-flex justify-content-betwen align-items-center p-1 pr-2 my-1 my-md-0 mx-auto bg-light">

                                                        <div class="font-weight-bold text-primary checklist-attachment">Comments</div>

                                                        <div>
                                                            <button type="button" class="btn btn-sm btn-success add-notes-button" data-add-notes-div="add_notes_{{ $checklist_item_id }}" data-toggle="collapse" data-target="#notes_{{ $checklist_item_id }}" aria-expanded="false" aria-controls="notes_{{ $checklist_item_id }}"><i class="fa fa-plus"></i></button>
                                                        </div>

                                                        <div>
                                                            <button type="button" class="btn btn-sm @if($notes_count_unread > 0) btn-secondary @else btn-primary @endif view-notes-button" data-toggle="collapse" data-target="#notes_{{ $checklist_item_id }}" aria-expanded="false" aria-controls="notes_{{ $checklist_item_id }}" @if($notes_count == 0) disabled @endif>@if($notes_count_unread > 0) New! @else View @endif<span class="badge badge-pill bg-white text-danger font-weight-bold py-1 px-2 ml-2">{{ $notes_count }}</span></button>
                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="col-12 col-sm-6 col-md-4 mx-auto">


                                                    @if(auth() -> user() -> group == 'admin')

                                                        @php
                                                        $bg_color = 'bg-light';
                                                        if($item_review_status == 'accepted') {
                                                            $bg_color = 'bg-green-light';
                                                        } else if($item_review_status == 'rejected') {
                                                            $bg_color = 'bg-red-light';
                                                        }
                                                        @endphp

                                                        <div class="checklist-item-options d-flex justify-content-between align-items-center my-1 my-md-0 mx-auto">

                                                            <div class="review-options p-1 {{ $bg_color }}">

                                                                <div class="@if($item_review_status == 'not_reviewed') d-flex @else d-none @endif justify-content-around align-items-center item-not-reviewed">
                                                                    <button type="button" class="btn btn-sm btn-success accept-checklist-item-button" data-checklist-item-id="{{ $checklist_item_id }}" @if($transaction_documents_count == 0) disabled @endif><i class="fa fa-check mr-2"></i> Accept</button>
                                                                    <button type="button" class="btn btn-sm btn-danger reject-checklist-item-button" data-checklist-item-id="{{ $checklist_item_id }}" @if($checklist_item -> checklist_item_required == 'yes') data-required="yes" @endif @if($transaction_documents_count == 0) disabled @endif><i class="fa fa-minus-circle mr-2"></i> Reject</button>
                                                                </div>

                                                                <div class="@if($item_review_status == 'accepted') d-flex @else d-none @endif justify-content-around align-items-center item-accepted">
                                                                    <button type="button" class="btn btn-sm btn-success" disabled><i class="fa fa-check mr-2"></i> Accepted</button>
                                                                    <div class="small mx-3">
                                                                        <a href="javascript: void(0)" class="undo-accepted" data-checklist-item-id="{{ $checklist_item_id }}" @if($checklist_item -> checklist_item_required == 'yes') data-required="yes" @endif ><i class="fad fa-undo mr-1"></i> Undo</a>
                                                                    </div>
                                                                </div>

                                                                <div class="@if($item_review_status == 'rejected') d-flex @else d-none @endif justify-content-around align-items-center  item-rejected">
                                                                    <div class="small mx-3">
                                                                        <a href="javascript: void(0)" class="undo-rejected" data-checklist-item-id="{{ $checklist_item_id }}" @if($checklist_item -> checklist_item_required == 'yes') data-required="yes" @endif ><i class="fad fa-undo mr-1"></i> Undo</a>
                                                                    </div>
                                                                    <button type="button" class="btn btn-sm btn-danger" disabled><i class="fa fa-minus-circle mr-2"></i> Rejected</button>
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
                                                    <div class="collapse documents-collapse mx-4 bg-white" id="documents_div_{{ $checklist_item_id }}">

                                                        <div class="p-3 mt-2 mb-4">

                                                            <div class="row">
                                                                <div class="col-12 mb-3">
                                                                    <div class="h5 text-primary float-left">Submitted Documents</div>
                                                                    <a class="text-danger float-right" data-toggle="collapse" href="#documents_div_{{ $checklist_item_id }}" aria-expanded="false" aria-controls="documents_div_{{ $checklist_item_id }}">
                                                                        <i class="fad fa-times-circle fa-2x"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            @foreach($transaction_documents as $transaction_document)
                                                                @php
                                                                $document_id = $transaction_document -> document_id;
                                                                $doc_info = $documents_model -> GetDocInfo($document_id);

                                                                /* $transaction_doc_notes = $transaction_checklist_item_notes_model -> GetNotes($checklist_item_id, $document_id);
                                                                $transaction_doc_notes_count = count($transaction_doc_notes);
                                                                $transaction_doc_notes_count_unread = $transaction_doc_notes -> where('note_status', 'unread') -> where('note_user_id', '!=', auth() -> user() -> id) -> count(); */
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
                                                                        <button type="button" class="btn btn-sm btn-danger float-right delete-doc-button" data-document-id="{{ $document_id }}" data-target="#documents_div_{{ $checklist_item_id }}" @if($item_review_status == 'accepted' && $transaction_document -> doc_status == 'viewed') disabled @endif>
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
                                                <div class="col-12 col-lg-5 px-0 px-sm-2 mx-auto mb-4">

                                                    <div class="collapse notes-collapse mx-4 mx-auto bg-white" id="notes_{{ $checklist_item_id }}">

                                                        <div class="p-3 mt-3">

                                                            <div class="row">

                                                                <div class="col-12">
                                                                    <div class="d-flex justify-content-between">
                                                                        <div class="h5 text-primary float-left">Comments</div>
                                                                        <div class="mb-2">
                                                                            <a class="text-danger" data-toggle="collapse" href="#notes_{{ $checklist_item_id }}" aria-expanded="false" aria-controls="notes_{{ $checklist_item_id }}"><i class="fad fa-times-circle fa-2x"></i></a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <hr>

                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="notes-div" data-checklist-item-id="{{ $checklist_item_id }}">
                                                                        <div class="text-gray">No Comments</div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>

                                                        <div class="row no-gutters bg-blue-light d-flex align-items-center">
                                                            <div class="col-11">
                                                                <div>
                                                                    <textarea class="custom-form-element form-textarea notes-input-{{ $checklist_item_id }}" data-label="Add Comment"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-1">
                                                                <a href="javascript: void(0)" class="btn btn-success save-notes-button ml-2" data-checklist-id="{{ $transaction_checklist_id }}" data-checklist-item-id="{{ $checklist_item_id }}"><i class="fa fa-save"></i></a>
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

                    @endif

                @endforeach

            </div>
        </div>
    </div>
</div>

<input type="hidden" id="transaction_checklist_id" value="{{ $transaction_checklist_id }}">
