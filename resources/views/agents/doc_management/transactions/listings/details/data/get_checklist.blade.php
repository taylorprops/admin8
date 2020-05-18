<div class="container checklist-container">
    <div class="row">
        <div class="col-12">
            <div class="mb-5">

                <div class="h4 text-primary ml-3 my-3"><i class="fad fa-tasks mr-2"></i> Listing Checklist</div>

                <hr class="mx-2">

                    @foreach($checklist_groups as $checklist_group)

                        <div class="h5 responsive text-orange checklist-group-header pb-2 @if(!$loop -> first) mt-4 @else mt-3 @endif">{{ $checklist_group -> resource_name }}</div>

                        @if(count($transaction_checklist_items -> where('checklist_item_group_id', $checklist_group -> resource_id)) > 0)

                            @foreach($transaction_checklist_items -> where('checklist_item_group_id', $checklist_group -> resource_id) as $checklist_item)

                                @php

                                $checklist_item_name = $checklist_items_model -> GetFormName($checklist_item -> checklist_form_id);
                                // details for helper popup
                                $form_help_details = $checklist_items_model -> GetFormHelpDetails($checklist_item -> checklist_form_id);
                                $form_help_details = $form_help_details['details'];
                                $form_help_html = $form_help_details -> helper_text;
                                if($form_help_details -> file_location != '') {
                                    $form_help_html .= '<hr>View Sample File<br><a href="'.$form_help_details -> file_location.'" class="btn btn-primary" target="_blank">Open File</a>';
                                }
                                // get docs and notes for checklist item
                                $transaction_checklist_item_id = $checklist_item -> id;
                                $transaction_documents = $transaction_checklist_item_docs_model -> GetDocs($transaction_checklist_item_id);
                                $transaction_documents_count = count($transaction_documents);

                                $transaction_notes = $transaction_checklist_item_notes_model -> GetNotes($transaction_checklist_item_id, '');
                                $transaction_notes_count = count($transaction_notes);
                                $notes_count_unread = $transaction_notes -> where('note_status', 'unread') -> where('note_user_id', '!=', auth() -> user() -> id) -> count();

                                // get status
                                $status_details = $transaction_checklist_items_model -> GetStatus($transaction_checklist_item_id);
                                $status = $status_details -> status;
                                $classes = $status_details -> classes;
                                $fa = $status_details -> fa;
                                $helper_text = $status_details -> helper_text;

                                $text_color = 'text-primary';
                                if($status != 'Required' && $status != 'Incomplete') {
                                    $text_color = 'text-gray';
                                }
                                @endphp

                                <div class="checklist-item-div p-2 border z-depth-1 mb-2">

                                    <div class="row">

                                        <div class="col-12 col-lg-5">
                                            <div class="checklist-item-details d-flex justify-content-start align-items-center h-100 mb-4 mb-lg-0">


                                                    <div>
                                                        <div class="status-badge badge {{ $classes }} mr-2" title="{{ $helper_text }}">{!! $fa . ' ' . $status !!}</div>
                                                    </div>
                                                    <div class="mx-2 helper-wrapper">
                                                        <a href="javascript: void(0)" role="button" class="checklist-item-helper" data-toggle="popover" data-html="true" data-trigger="focus" title="{{ $checklist_item_name }}" data-content="{{ $form_help_html }}">
                                                            <i class="fad fa-question-circle"></i>
                                                        </a>
                                                    </div>
                                                    <div class="h6 d-inline-block d-md-none mt-2 {{ $text_color }}">{{ $checklist_item_name }}</div>
                                                    <div class="d-none d-md-inline-block mx-md-2 {{ $text_color }}">{{ $checklist_item_name }}</div>



                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-7">

                                            <div class="row">

                                                <div class="col-12 col-sm-6">

                                                    <div class="d-flex justify-content-start align-items-center">
                                                        <div class="font-weight-bold text-primary mr-2">Docs</div>
                                                        <div>
                                                            <button type="button" class="btn btn-sm btn-success add-document-button" data-checklist-id="{{ $transaction_checklist_id }}" data-checklist-item-id="{{ $transaction_checklist_item_id }}" data-target="documents_div_{{ $transaction_checklist_item_id }}"><i class="fa fa-plus mr-2"></i> Add</button>
                                                        </div>
                                                        @if($transaction_documents_count > 0)
                                                        <div>
                                                            <button type="button" class="btn btn-sm btn-primary view-docs-button" data-toggle="collapse" data-target="#documents_div_{{ $transaction_checklist_item_id }}" aria-expanded="false" aria-controls="documents_div_{{ $transaction_checklist_item_id }}">View <span class="badge badge-pill bg-white text-danger font-weight-bold py-1 px-2 ml-2">{{ $transaction_documents_count }}</span></button>
                                                        </div>
                                                        @endif
                                                    </div>

                                                </div>

                                                <div class="col-12 col-sm-6">

                                                    <div class="d-flex justify-content-start align-items-center">

                                                        <div class="font-weight-bold text-primary mr-2">Comments</div>

                                                        <div>
                                                            <button type="button" class="btn btn-sm btn-success add-notes-button" data-add-notes-div="add_notes_div_{{ $transaction_checklist_item_id }}" data-toggle="collapse" data-target="#notes_div_{{ $transaction_checklist_item_id }}" aria-expanded="false" aria-controls="notes_div_{{ $transaction_checklist_item_id }}"><i class="fa fa-plus mr-2"></i> Add</button>
                                                        </div>

                                                        @if($transaction_notes_count > 0)
                                                        <div>
                                                            <button type="button" class="btn btn-sm @if($notes_count_unread > 0) btn-secondary @else btn-primary @endif view-notes-button" data-toggle="collapse" data-target="#notes_div_{{ $transaction_checklist_item_id }}" aria-expanded="false" aria-controls="notes_div_{{ $transaction_checklist_item_id }}">View <span class="badge badge-pill bg-white text-danger font-weight-bold py-1 px-2 ml-2">{{ $transaction_notes_count }}</span></button>
                                                        </div>
                                                        @endif

                                                        @if($notes_count_unread > 0)
                                                        <div class="ml-2">
                                                            <span class="text-orange font-weight-bold"><i class="fal fa-exclamation-triangle mr-1"></i> New</span>
                                                        </div>
                                                        @endif

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-12">

                                            <div class="row">
                                                <div class="col-12 col-md-9 col-lg-7 mx-auto">
                                                    <div class="collapse documents-collapse mt-2 mb-4 mx-4 p-3 mx-auto bg-white" id="documents_div_{{ $transaction_checklist_item_id }}">

                                                        <div class="row">
                                                            <div class="col-12 mb-3">
                                                                <div class="h4 responsive text-primary float-left">Submitted Documents</div>
                                                                <a class="text-danger float-right"
                                                                    data-toggle="collapse"
                                                                    href="#documents_div_{{ $transaction_checklist_item_id }}"
                                                                    aria-expanded="false"
                                                                    aria-controls="documents_div_{{ $transaction_checklist_item_id }}">
                                                                    <i class="fal fa-times fa-2x mr-2"></i>
                                                                </a>
                                                            </div>
                                                        </div>

                                                        @foreach($transaction_documents as $transaction_document)
                                                            @php
                                                            $document_id = $transaction_document -> document_id;
                                                            $doc_info = $documents_model -> GetDocInfo($document_id);

                                                            $transaction_doc_notes = $transaction_checklist_item_notes_model -> GetNotes($transaction_checklist_item_id, $document_id);
                                                            $transaction_doc_notes_count = count($transaction_doc_notes);
                                                            $transaction_doc_notes_count_unread = $transaction_doc_notes -> where('note_status', 'unread') -> where('note_user_id', '!=', auth() -> user() -> id) -> count();
                                                            @endphp

                                                            <div class="d-flex justify-content-between align-items-center border-bottom document-row">
                                                                <div class="d-flex justify-content-start align-items-center">

                                                                    <div class="mx-2"><a href="{{ $doc_info['file_location_converted'] }}" target="_blank" class="btn btn-sm btn-primary">View</a></div>

                                                                    <div>{{ $doc_info['file_name'] }}</div>

                                                                </div>
                                                                <div>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-danger float-right delete-doc-button"
                                                                        data-document-id="{{ $document_id }}"
                                                                        data-target="#documents_div_{{ $transaction_checklist_item_id }}">
                                                                        <i class="fa fa-times text-white"></i>
                                                                    </button>
                                                                </div>
                                                            </div>

                                                        @endforeach

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-lg-11 px-0 px-sm-2 mx-auto">
                                                <div class="collapse notes-collapse mt-2 mb-4 mx-0 mx-sm-1 mx-md-2 mx-lg-4 p-3 bg-white" id="notes_div_{{ $transaction_checklist_item_id }}">

                                                    <div class="row">

                                                        <div class="col-12">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="font-weight-bold text-primary">Add Comments</div>
                                                                <div>
                                                                    <a class="text-danger" data-toggle="collapse" href="#notes_div_{{ $transaction_checklist_item_id }}" aria-expanded="false" aria-controls="notes_div_{{ $transaction_checklist_item_id }}"><i class="fal fa-times fa-2x mr-2"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12 col-md-3">
                                                            <div class="add-notes-div" id="add_notes_div_{{ $transaction_checklist_item_id }}">

                                                                <div>
                                                                    <textarea class="custom-form-element form-textarea"
                                                                    id="add_notes_textarea_{{ $transaction_checklist_item_id }}"
                                                                    data-notes-collapse="notes_div_{{ $transaction_checklist_item_id }}"
                                                                    data-checklist-id="{{ $transaction_checklist_id }}"
                                                                    data-checklist-item-id="{{ $transaction_checklist_item_id }}"
                                                                    data-label="Enter Comments"></textarea>
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
                                                                        $user_details = $users_model -> UserDetails($transaction_note -> note_user_id);
                                                                        $user = collect($user_details['user']);
                                                                        $unread = null;
                                                                        if($transaction_note -> note_status == 'unread' && $transaction_note -> note_user_id != auth() -> user() -> id) {
                                                                            $unread = 'unread';
                                                                        }

                                                                        @endphp
                                                                        <div class="p-2 pb-0 mt-2 note-div @if($unread) border-orange @else border-bottom @endif bg-white">

                                                                            <div class="row">
                                                                                <div class="col-6 col-lg-2">
                                                                                    <div class="text-gray font-italic">{{ $user['name'] }}</div>
                                                                                </div>
                                                                                <div class="col-6 col-lg-2">
                                                                                    <div class="text-gray small mt-1">{{ date('n/j/Y g:i:sA', strtotime($transaction_note -> created_at)) }}</div>
                                                                                </div>
                                                                                <div class="col-12 col-lg-6">
                                                                                    <div>{{ $transaction_note -> notes }}</div>
                                                                                </div>
                                                                                <div class="col-12 col-lg-2">
                                                                                    <div class="d-flex justify-content-end">
                                                                                        @if($transaction_note -> note_status == 'unread')

                                                                                            @if($transaction_note -> note_user_id != auth() -> user() -> id)

                                                                                                <button class="btn btn-success btn-sm mark-read-button mb-0" data-note-id="{{ $transaction_note -> id }}" data-notes-collapse="notes_div_{{ $transaction_checklist_item_id }}"><i class="fa fa-check mr-2"></i> Mark Read</button>

                                                                                            @else

                                                                                                <span class="text-gray small">Not Read</span>

                                                                                            @endif

                                                                                        @else

                                                                                            <span class="text-success small"><i class="fa fa-check"></i> Comment Read</span>

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

                            @endforeach

                        @else
                        <div class="text-gray">No Required Forms for this Group</div>
                        @endif

                    @endforeach

            </div>
        </div>
    </div>
</div>


<div class="modal fade draggable" id="add_document_modal" tabindex="-1" role="dialog" aria-labelledby="add_document_modal_title" aria-hidden="true">
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
                                @foreach($folders as $folder)
                                    @if(count($documents_available -> where('folder', $folder -> id)) > 0)
                                        <div class="h5 responsive text-orange">{{ $folder -> folder_name }}</div>
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

<div class="modal fade draggable" id="confirm_delete_checklist_item_doc_modal" tabindex="-1" role="dialog" aria-labelledby="delete_checklist_item_doc_title" aria-hidden="true">
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

