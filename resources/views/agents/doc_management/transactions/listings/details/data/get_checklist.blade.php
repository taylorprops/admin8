<div class="container checklist-container">
    <div class="row">
        <div class="col-12">
            <div class="p3 mb-5">

                <div class="h4-responsive text-primary ml-3 mb-3"><i class="fad fa-tasks mr-2"></i> Listing Checklist</div>

                <hr class="mx-2">

                    @foreach($checklist_groups as $checklist_group)

                        <div class="h5 text-orange checklist-group-header pb-2 @if(!$loop -> first) mt-4 @else mt-3 @endif">{{ $checklist_group -> resource_name }}</div>

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

                                $notes = $transaction_checklist_item_notes_model -> GetNotes($transaction_checklist_item_id);
                                $notes_count = count($notes);
                                $notes_count_unread = $notes -> where('notes_status', 'unread') -> count();

                                // get status
                                $status_details = $transaction_checklist_items_model -> GetStatus($transaction_checklist_item_id);
                                $status = $status_details -> status;
                                $classes = $status_details -> classes;
                                $fa = $status_details -> fa;
                                $helper_text = $status_details -> helper_text;
                                @endphp

                                <div class="checklist-item-div p-2 border-bottom">

                                    <div class="row">

                                        <div class="col-12 col-lg-5">
                                            <div class="checklist-item-details d-flex align-items-center justify-content-between h-100">

                                                <div class="d-flex justify-content-start">
                                                    {{-- <div class="badge bg-blue-med pt-2 px-2 mr-3">{{ $loop -> index + 1 }}</div> --}}
                                                    <div class="status-badge badge {{ $classes }} mx-2" title="{{ $helper_text }}">{!! $fa . ' ' . $status !!}</div>

                                                    <div class="mx-2 helper-wrapper">
                                                        <a href="javascript: void(0)" role="button" class="checklist-item-helper" data-toggle="popover" data-html="true" data-trigger="focus" title="{{ $checklist_item_name }}" data-content="{{ $form_help_html }}">
                                                            <i class="fad fa-question-circle ml-2"></i>
                                                        </a>
                                                    </div>
                                                    <div class="mx-2">{{ $checklist_item_name }}</div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-7">
                                            <div class="d-flex justify-content-start align-items-center">
                                                @if($transaction_documents_count > 0)
                                                <div class="mr-2">
                                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="collapse" data-target="#documents_div_{{ $transaction_checklist_item_id }}" aria-expanded="false" aria-controls="documents_div_{{ $transaction_checklist_item_id }}">View Docs <span class="badge bg-blue-light text-primary py-1 px-1 ml-2">{{ $transaction_documents_count }}</span></button>
                                                </div>
                                                @endif
                                                <div>
                                                    <button type="button" class="btn btn-sm btn-success add-document-button" data-checklist-id="{{ $transaction_checklist_id }}" data-checklist-item-id="{{ $transaction_checklist_item_id }}"><i class="fa fa-plus mr-2"></i> Add Document</button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="collapse" id="documents_div_{{ $transaction_checklist_item_id }}">
                                            @foreach($transaction_documents as $transaction_document)
                                                @php
                                                $doc_info = $documents_model -> GetDocInfo($transaction_document -> document_id);
                                                @endphp
                                                <div class="p-2 border-bottom d-flex justify-content-between align-items-center">
                                                    <div>{{ $doc_info['file_name'] }}</div>
                                                    <div><i class="fa fa-times text-danger"></i></div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="collapse" id="notes_div_{{ $transaction_checklist_item_id }}">

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
                                        <div class="h5 text-orange">{{ $folder -> folder_name }}</div>
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

