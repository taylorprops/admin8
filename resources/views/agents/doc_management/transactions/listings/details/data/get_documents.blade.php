<div class="p-1 p-md-4 documents-container">

    <div class="row mb-3 mb-sm-2 mb-md-1">
        <div class="col-12 col-sm-5">
            <div class="h4 text-primary ml-3 mt-2 mb-3"><i class="fad fa-copy mr-2"></i> Listing Documents</div>
        </div>
        <div class="col-12 col-sm-7">
            <div class="add-buttons-div">
                <div class="row">
                    <div class="col-6 mt-2">
                        <a class="btn btn-success" data-toggle="collapse" href="#add_documents_div" aria-expanded="false" aria-controls="add_documents_div"><i class="fa fa-plus mr-2"></i> Add Documents </a>
                    </div>
                    <div class="col-6 mt-2">
                        <a href="javascript: void(0)" class="btn btn-success add-folder-button"><i class="fa fa-plus mr-2"></i> Add Folder</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="collapse mt-0 mb-3" id="add_documents_div">
        <div class="row">
            <div class="col-12 pr-4 mb-2">
                <div class="float-right">
                    <a data-toggle="collapse" href="#add_documents_div" aria-expanded="false" aria-controls="add_documents_div"><i class="fal fa-times text-danger fa-3x"></i></a>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-12 col-md-12 col-lg-6 px-1">
                <div class="add-docs-div bg-blue-light p-3 mb-1 mb-sm-3 border border-primary rounded-lg text-center">
                    <i class="fad fa-clone fa-3x text-primary mb-2"></i>
                    <div class="h5 text-primary mb-3">Templates <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Adding Template Documents" data-content="body"><i class="fad fa-question-circle ml-2"></i></a></div>
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            Checklist Documents<br>
                            <a href="javascript:void(0);" class="btn btn-primary" id="add_checklist_template_button"><i class="fa fa-plus mr-2"></i> Add Template</a>
                        </div>
                        <div class="col-12 col-sm-6">
                            Individual Documents<br>
                            <a href="javascript:void(0);" class="btn btn-primary" id="add_individual_template_button"><i class="fa fa-plus mr-2"></i> Add Documents</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3 px-1">
                <div class="add-docs-div bg-blue-light p-3 mb-1 border border-primary rounded-lg text-center">
                    <i class="fad fa-file-upload fa-3x text-primary mb-2"></i>
                    <div class="h5 text-primary mb-3">Upload Documents <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Uploading Documents" data-content="body"><i class="fad fa-question-circle ml-2"></i></a></div>
                    <a href="javascript:void(0);" class="btn btn-primary mt-1 mt-md-4" id="upload_documents_button"><i class="fa fa-plus mr-2"></i> Upload Documents</a>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3 px-1">
                <div class="add-docs-div bg-blue-light p-3 mb-1 border border-primary rounded-lg text-center">
                    <i class="fad fa-envelope-square fa-3x text-primary mb-2"></i>
                    <div class="h5 text-primary mb-3">Email Documents <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Emailing Documents" data-content="body"><i class="fad fa-question-circle ml-2"></i></a></div>
                    <a href="mailto:" class="d-block mt-1 mt-md-5">email@email.com</a>
                </div>
            </div>
        </div>

    </div>


    <div class="collapse" id="bulk_options_div">
        <div class="h5 text-orange d-none d-md-block">Bulk Options</div>
        <div class="d-flex justify-content-between justify-content-sm-start">

            <button type="button" class="btn btn-sm btn-primary rounded-pill add-to-checklist-button" title="Assign To Checklist" data-toggle="tooltip" data-checklist-id="{{ $checklist_id }}"><i class="fad fa-tasks mr-0 mr-sm-2"></i><span class="d-none d-sm-inline-block"> Assign To Checklist</span></button>

            <button type="button" class="btn btn-sm btn-primary rounded-pill" id="sign_documents_button" title="Get Signed" data-toggle="tooltip"><i class="fad fa-signature mr-0 mr-sm-2"></i><span class="d-none d-sm-inline-block"> Get Signed</span></button>

            <div class="dropright" title="Print Documents">
                <button type="button" class="btn btn-sm btn-primary rounded-pill" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fad fa-envelope mr-0 mr-sm-2"></i><span class="d-none d-sm-inline-block"> Email</span></button>
                <div class="dropdown-menu">
                    <a class="text-primary dropdown-item docs-email-button" data-docs-type="merged" href="javascript: void(0)"><i class="fad fa-file-alt mr-2 fa-lg"></i> As One Document</a>
                    <a class="text-primary dropdown-item docs-email-button" data-docs-type="single" href="javascript: void(0)"><i class="fal fa-file mr-2 fa-lg"></i> As Individual Documents</a>
                </div>
            </div>

            <div class="dropright" title="Print Documents">
                <button type="button" class="btn btn-sm btn-primary rounded-pill" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fad fa-print mr-0 mr-sm-2"></i><span class="d-none d-sm-inline-block"> Print</span></button>
                <div class="dropdown-menu">
                    <a class="text-primary dropdown-item docs-print-button" data-type="filled" href="javascript: void(0)"><i class="fad fa-file-alt mr-2 fa-lg"></i> Print Filled</a>
                    <a class="text-primary dropdown-item docs-print-button" data-type="blank" href="javascript: void(0)"><i class="fal fa-file mr-2 fa-lg"></i> Print Blank</a>
                </div>
            </div>

            <div class="dropright" title="Download Documents">
                <button type="button" class="btn btn-sm btn-primary rounded-pill" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fad fa-download mr-0 mr-sm-2"></i><span class="d-none d-sm-inline-block"> Download</span></button>
                <div class="dropdown-menu">
                    <a class="text-primary dropdown-item docs-download-button" data-type="filled" href="javascript: void(0)"><i class="fad fa-file-alt mr-2 fa-lg"></i> Download Filled</a>
                    <a class="text-primary dropdown-item docs-download-button" data-type="blank" href="javascript: void(0)"><i class="fal fa-file mr-2 fa-lg"></i> Download Blank</a>
                </div>
            </div>

            <button type="button" class="btn btn-sm btn-primary rounded-pill" id="move_documents_button" title="Move Documents" data-toggle="tooltip"><i class="fad fa-exchange mr-0 mr-sm-2"></i><span class="d-none d-sm-inline-block"> Move</span></button>

            <button type="button" class="btn btn-sm btn-danger rounded-pill" id="delete_documents_button" title="Move Documents To Trash" data-toggle="tooltip"><i class="fad fa-trash mr-0 mr-sm-2"></i><span class="d-none d-sm-inline-block"> Trash</span></button>
        </div>
    </div>

    @foreach($folders as $folder)

    @php
    $docs_count = $documents -> where('folder', $folder -> id) -> count();
    @endphp
    <div class="folder-div mb-2 border-top border-bottom border-primary" data-folder="{{ $folder -> id }}">

        <div class="folder-header d-flex justify-content-between">
            <div class="d-flex justify-content-start align-items-center">
                <div class="mt-1 mr-2 mr-sm-4">
                    <input type="checkbox" class="custom-form-element form-checkbox check-all">
                </div>
                <div class="h5 mt-2">
                    <a class="text-gray folder-collapse" data-toggle="collapse" href="#documents_folder_{{ $loop -> index }}" aria-expanded="false" aria-controls="documents_folder_{{ $loop -> index }}">
                        <i class="fal @if($folder -> folder_name == 'Trash' || $docs_count == 0) fa-angle-right @else fa-angle-down @endif fa-lg mr-3"></i>
                        <i class="fad fa-folder mr-1 mr-sm-3 fa-lg"></i>
                        {{ $folder -> folder_name }}
                    </a>
                    <span class="badge badge-pill badge-primary ml-1 ml-sm-3 py-1">{{ $docs_count }}</span>
                </div>
            </div>
            @if(!$loop -> first && !$loop -> last && $documents -> where('folder', $folder -> id) -> where('assigned', 'yes') -> count() == 0)
            <div class="pt-1">
                <a href="javascript: void(0)" class="btn btn-sm btn-danger delete-folder-button" data-folder-id="{{ $folder -> id }}"><i class="fa fa-trash"></i> <span class="d-none d-sm-inline-block ml-2">Delete Folder</span></a>
            </div>
            @endif
        </div>

        <div class="collapse sortable-documents @if($folder -> folder_name != 'Trash' && $docs_count > 0) show @endif" id="documents_folder_{{ $loop -> index }}">

            @foreach($documents as $document)

                @if($document -> folder == $folder -> id)

                @php
                $assigned = $document -> assigned == 'yes' ? 'assigned' : null;
                $disabled = $assigned == 'yes' ? 'disabled' : null;
                @endphp

                <div class="document-div row mx-0 py-0" data-folder-id="{{ $folder -> id }}" data-document-id="{{ $document -> id }}">

                    <div class="col-10 col-xl-4">

                        <div class="d-flex justify-content-start align-items-center">
                            <div class="mt-1">
                                <a href="javascript:void(0)" class="document-handle text-blue"><i class="fal fa-bars fa-lg"></i></a>
                            </div>
                            <div class="mt-1 ml-1 mr-1 mr-md-4">
                                <input type="checkbox" class="custom-form-element form-checkbox check-document  {{ $assigned }}" data-document-id="{{ $document -> id }}">
                            </div>
                            <div class="text-gray document-title py-1 py-sm-2">
                                <a href="{{ $document -> file_location_converted }}" target="_blank">{{ $document -> file_name_display }}</a>
                                <br>
                                <span class="small">Added: {{ date('n/j/Y g:i:sA', strtotime($document -> created_at)) }} @if($document -> file_type == 'user') <span class="badge badge-secondary p-1 ml-2">User File</span> @else <span class="badge badge-primary p-1 ml-2">System File</span>@endif</span>
                            </div>
                        </div>

                    </div>

                    <div class="col-2 col-xl-8">

                        <div class="d-flex justify-content-end align-items-center h-100">

                            @if($folder -> folder_name != 'Trash')

                                @php
                                $menu_options = '';

                                if($assigned) {

                                    $menu_options .= '<div class="mr-1  text-success"><i class="fal fa-check mr-2"></i> <span class="d-inline-block d-xl-inline-block"> Assigned</span></div>';

                                    $menu_options .= '<button type="button" class="dropdown-item text-primary doc-rename-button" data-document-id="'.$document -> id.'" data-document-name="'.$document -> file_name_display.'" title="Rename Document"><i class="fad fa-repeat mr-1 "></i> Rename</button>';

                                } else {

                                    $menu_options .= '<button type="button" class="dropdown-item text-primary add-to-checklist-button" data-document-id="'.$document -> id.'"  data-checklist-id="'.$checklist_id.'" title="Assign Document To Checklist Item"><i class="fad fa-tasks mr-1 "></i> Assign</button>';

                                    $menu_options .= '<button type="button" class="dropdown-item text-primary doc-rename-button" data-document-id="'.$document -> id.'" data-document-name="'.$document -> file_name_display.'" title="Rename Document"><i class="fad fa-repeat mr-1 "></i> Rename</button>';

                                    if($document -> pages_total > 1) {
                                        $menu_options .= '<button type="button" class="dropdown-item text-primary doc-split-button" data-document-id="'.$document -> id.'" data-checklist-id="'.$checklist_id.'" data-file-name="'.$document -> file_name_display.'" data-file-type="'.$document -> file_type.'" data-folder="'.$folder -> id.'" title="Split Document"><i class="fad fa-page-break mr-1 "></i> Split</button>';
                                    }

                                    $menu_options .= '<button type="button" class="dropdown-item text-primary doc-edit-button" onClick="window.open(\'/agents/doc_management/transactions/edit_files/'.$document -> id.'\')" data-document-id="'.$document -> id.'" title="Fill Fields"><i class="fad fa-edit mr-1 "></i> Fill Fields</button>';

                                    $menu_options .= '<button type="button" class="dropdown-item text-primary doc-get-signed-button" data-document-id="'.$document -> id.'" title="Get Signed"><i class="fad fa-signature mr-1 "></i> Get Signed</button>';

                                }



                                $menu_options .= '<button type="button" class="dropdown-item text-primary doc-duplicate-button" data-document-id="'.$document -> id.'" data-file-type="'.$document -> file_type.'" title="Make Copy Of Form"><i class="fad fa-clone mr-2 mr-xl-0"></i><span class="d-inline-block d-xl-none"> Make Copy</span></button>';

                                $menu_options .= '<button type="button" class="dropdown-item text-primary doc-email-button" data-document-id="'.$document -> id.'" title="Email Form"><i class="fad fa-envelope mr-2 mr-xl-0"></i><span class="d-inline-block d-xl-none"> Email</span></button>';

                                $menu_options .= '
                                <div class="dropdown-submenu">
                                    <button type="button" class="dropdown-item text-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Download Form"><i class="fad fa-download mr-2 mr-xl-0"></i><span class="d-inline-block d-xl-none"> Download</span></button>
                                    <div class="dropdown-menu">
                                        <a class="text-primary dropdown-item" href="'.$document -> file_location_converted.'" download="'.$document -> file_name_display.'"><i class="fad fa-file-alt mr-2 fa-lg"></i> Download Filled</a>
                                        <a class="text-primary dropdown-item" href="'.$document -> file_location.'" download="'.$document -> file_name_display.'"><i class="fal fa-file mr-2 fa-lg"></i> Download Blank</a>
                                    </div>
                                </div>';

                                $menu_options .= '
                                <div class="dropdown-submenu">
                                    <button type="button" class="dropdown-item text-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Print Form"><i class="fad fa-print mr-2 mr-xl-0"></i><span class="d-inline-block d-xl-none"> Print</span></button>
                                    <div class="dropdown-menu">
                                        <a class="text-primary dropdown-item doc-print-button" href="javascript: void(0)" data-link="'.$document -> file_location_converted.'"><i class="fad fa-file-alt mr-2 fa-lg"></i> Print Filled</a>
                                        <a class="text-primary dropdown-item doc-print-button" href="javascript: void(0)" data-link="'.$document -> file_location.'" data-filename="'.$document -> file_name_display.'"><i class="fal fa-file mr-2 fa-lg"></i> Print Blank</a>
                                    </div>
                                </div>';

                                $menu_options .= '<div class="dropdown-divider d-block d-xl-none"></div>';

                                if(!$assigned) {

                                    $menu_options .= '<button type="button" class="dropdown-item text-danger doc-delete-button" data-document-id="'.$document -> id.'" data-document-name="' . $document -> file_name_display . '" title="Delete Form"><i class="fad fa-trash mr-2 mr-xl-0"></i><span class="d-inline-block d-xl-none"> Trash</span></button>';

                                }

                                $menu_options_large = preg_replace('/dropdown-item\stext-primary/', 'btn btn-primary', $menu_options);
                                $menu_options_large = preg_replace('/dropdown-item\stext-danger/', 'btn btn-danger', $menu_options_large);
                                $menu_options_large = preg_replace('/dropdown-submenu/', 'dropleft', $menu_options_large);
                                @endphp

                                <div class="d-block d-xl-none dropleft">
                                    <button type="button" class="btn btn-primary dropdown-toggle pl-2 pr-1 py-0 pl-sm-2 pt-sm-1 pb-sm-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                    <div class="dropdown-menu">
                                        {!! $menu_options !!}
                                    </div>
                                </div>

                                <div class="d-none d-xl-flex align-items-center">
                                    {!! $menu_options_large !!}
                                </div>

                            @endif

                        </div>

                    </div>

                </div>

                @endif

            @endforeach

        </div>

    </div>

    @endforeach

</div>

<div class="modal fade draggable" id="send_email_modal" tabindex="-1" role="dialog" aria-labelledby="send_email_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary draggable-handle">
                <h4 class="modal-title" id="send_email_modal_title">Email Documents</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">

                <div class="container">

                    <div class="row">

                        <div class="col-12">

                            <div class="card">

                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-2">
                                            <div class="h-100 d-flex justify-content-end align-items-center">
                                                <div>From:</div>
                                            </div>
                                        </div>
                                        <div class="col-10">
                                            <input type="text" class="custom-form-element form-input" id="email_from" value="<{{ \Auth::user() -> name }}> {{ \Auth::user() -> email }}">
                                        </div>
                                    </div>

                                    @foreach($members as $member)
                                    <div class="row to-addresses">
                                        <div class="col-2">
                                            @if($loop -> first)
                                                <input type="hidden" class="email-address-type" value="to">
                                                <div class="h-100 d-flex justify-content-end align-items-center">
                                                    <div>To:</div>
                                                </div>
                                            @else
                                                <select class="custom-form-element form-select form-select-no-cancel form-select-no-search email-address-type">
                                                    <option value="to">To:</option>
                                                    <option value="cc">Cc:</option>
                                                    <option value="bcc">Bcc:</option>
                                                </select>
                                            @endif
                                        </div>
                                        <div class="@if($loop -> first) col-10 @else col-9 @endif">
                                            <input type="text" class="custom-form-element form-input email-to-address" value="<{{ $member -> first_name.' '.$member -> last_name }}> {{ $member -> email }}">
                                        </div>
                                        @if(!$loop -> first)
                                        <div class="col-1">
                                            <div class="h-100 d-flex justify-content-end align-items-center">
                                                <button class="btn btn-sm btn-danger delete-address-button"><i class="fal fa-times"></i></button>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach

                                    <div class="row">
                                        <div class="col-2"></div>
                                        <div class="col-10">
                                            <a class="add-address-button"><i class="fal fa-plus mr-1 text-success"></i> Add Recipient</a>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="container mt-2">

                    <div class="row">

                        <div class="col-12">

                            <div class="card">

                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-2">
                                            <div class="h-100 d-flex justify-content-end align-items-center">
                                                <div>Subject:</div>
                                            </div>
                                        </div>
                                        <div class="col-10">
                                            <input type="text" class="custom-form-element form-input" id="email_subject" value="Documents - {{ $listing -> FullStreetAddress }} {{ $listing -> City }}, {{ $listing -> StateOrProvince }} {{ $listing -> PostalCode }}">
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-2">
                                            <div class="h-100 d-flex justify-content-end align-items-center">
                                                <div>Attachments:</div>
                                            </div>
                                        </div>
                                        <div class="col-10">
                                            <div class="w-100 border p-2" id="email_attachments"></div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-2">
                                            <div class="h-100 d-flex justify-content-end align-items-center">
                                                <div>Message:</div>
                                            </div>
                                        </div>
                                        <div class="col-10">
                                            <textarea class="custom-form-input form-textarea" id="email_message" rows="4">&#13;&#10; &#13;&#10; Thank you,&#13;&#10; {{ \Auth::user() -> name }}</textarea>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>
            <div class="d-flex justify-content-around pb-3">
                <a class="btn btn-success" id="send_email_button"><i class="fad fa-share mr-2"></i> Send Email</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="image_zoom_modal" tabindex="-1" role="dialog" aria-labelledby="image_zoom_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="image_zoom_div" class="text-center"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="split_document_modal" tabindex="-1" role="dialog" aria-labelledby="split_document_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" id="split_document_modal_dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary draggable-handle">
                <h4 class="modal-title" id="split_document_modal_title">Split Document</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body pt-0">
                <div id="split_document_container"></div>
                <input type="hidden" id="folder_id">
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="rename_document_modal" tabindex="-1" role="dialog" aria-labelledby="rename_document_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <form id="rename_document_form">
                <div class="modal-header bg-primary draggable-handle">
                    <h4 class="modal-title" id="rename_document_modal_title">Rename Document</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center">
                                    Enter New Name<br>
                                    <input type="text" class="custom-form-element form-input" id="new_document_name" data-title="Enter Document Name">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                    <a class="btn btn-success" id="save_rename_document_button"><i class="fad fa-check mr-2"></i> Save</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="add_to_checklist_modal" tabindex="-1" role="dialog" aria-labelledby="add_to_checklist_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form id="add_to_checklist_form">
                <div class="modal-header bg-primary draggable-handle">
                    <h4 class="modal-title" id="add_to_checklist_modal_title">Assign To Checklist</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body p-2">
                    <div class="container p-0 p-sm-1 p-md-2">
                        <div class="row">
                            <div class="col-12">
                                <div id="add_items_to_checklist_div"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                    <a class="btn btn-success" id="save_add_to_checklist_button" data-checklist-id="{{ $checklist_id }}"><i class="fad fa-check mr-2"></i> Save</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="confirm_delete_document_modal" tabindex="-1" role="dialog" aria-labelledby="delete_document_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary draggable-handle">
                <h4 class="modal-title" id="delete_document_title">Delete Document</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            Are you sure you want to move this form to Trash?
                            <br>
                            <div class="font-weight-bold text-primary mt-2" id="delete_document_name"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-success modal-confirm-button" id="confirm_delete_document_button"><i class="fad fa-check mr-2"></i> Confirm</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="confirm_delete_folder_modal" tabindex="-1" role="dialog" aria-labelledby="delete_folder_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary draggable-handle">
                <h4 class="modal-title" id="delete_folder_title">Delete Folder</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-12 text-center">
                            This will remove the folder and place all of its forms in the Trash Folder.<br>Continue?
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-success modal-confirm-button" id="confirm_delete_folder_button"><i class="fad fa-check mr-2"></i> Confirm</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="confirm_delete_documents_modal" tabindex="-1" role="dialog" aria-labelledby="delete_documents_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary draggable-handle">
                <h4 class="modal-title" id="delete_documents_title">Move To Trash</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    Move Documents To Trash?
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-success modal-confirm-button" id="confirm_delete_documents_button"><i class="fad fa-check mr-2"></i> Confirm</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="move_documents_modal" tabindex="-1" role="dialog" aria-labelledby="move_documents_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="move_documents_form">
                <div class="modal-header bg-primary draggable-handle">
                    <h4 class="modal-title" id="move_documents_modal_title">Move Documents</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-4">Move Documents To:</div>
                                <select class="custom-form-element form-select form-select-no-search form-select-no-cancel required" id="move_documents_folder" data-label="Select Folder">
                                    @foreach($folders as $folder)
                                        @if($folder -> folder_name != 'Trash')
                                        <option value="{{ $folder -> id }}">{{ $folder -> folder_name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                    <a class="btn btn-success" id="save_move_documents_button"><i class="fad fa-check mr-2"></i> Move Documents</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="add_individual_template_modal" tabindex="-1" role="dialog" aria-labelledby="add_individual_template_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form id="add_individual_template_form">
                <div class="modal-header bg-primary draggable-handle">
                    <h4 class="modal-title" id="add_individual_template_modal_title">Add Individual Template Documents</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body pb-0">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="row mt-2 mb-3">
                                    <div class="col-12 col-lg-6">
                                        <select class="custom-form-element form-select form-select-no-cancel form-select-no-search select-form-group mt-3" data-label="Select Form Group">
                                            <option value="all">All</option>
                                            @foreach($form_groups as $form_group)
                                            <option value="{{ $form_group -> resource_id }}" @if($loop -> first) selected @endif>{{ $form_group -> resource_state }} @if($form_group -> resource_state != $form_group -> resource_name) | {{ $form_group -> resource_name }} @endif</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-lg-6">
                                        <div id="form_search_div">
                                            <input type="text" class="custom-form-element form-input" id="form_search" data-label="Search All Forms">
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-6 mt-1 mt-lg-0">
                                        <select class="custom-form-element form-select form-select-no-search" id="form_tag_search" multiple data-label="Search Form Tags">
                                            @foreach($form_tags as $form_tag)
                                            <option value="{{ $form_tag -> resource_id }}">{{ $form_tag -> resource_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-groups-container border p-1 p-md-3 p-lg-4" data-simplebar data-simplebar-auto-hide="false">

                                    @foreach($form_groups as $form_group)

                                    <ul class="list-group form-group-div" data-form-group-id="{{ $form_group -> resource_id }}">
                                        <li class="h4 text-orange list-group-header mt-3">
                                            {{ $form_group -> resource_state }}
                                            @if($form_group -> resource_state != $form_group -> resource_name) | {{ $form_group -> resource_name }} @endif
                                        </li>

                                        @php
                                        $forms = $available_files -> formGroupFiles($form_group -> resource_id, $Listing_ID);
                                        $forms_available = $forms['forms_available'];
                                        $forms_in_use = $forms['forms_in_use'] -> toArray();
                                        @endphp

                                        @foreach($forms_available as $form)

                                            @php
                                            $form_tags = explode(',', $form -> sale_type);
                                            $form_status_class = '';
                                            if(in_array($form -> file_id, $forms_in_use)) {
                                                $form_status_class = 'form-in-use';
                                            }
                                            @endphp

                                            <li class="list-group-item form-name p-1 {{ $form_status_class }}" data-form-id="{{ $form -> file_id }}" data-text="{{ $form -> file_name_display }}" data-tags="@foreach($form_tags as $tag){{ $tag }} @endforeach">
                                                <div class="d-flex justify-content-between">
                                                    <div class="d-flex justify-content-start align-items-center">
                                                        <div class="mr-3 mt-1">
                                                            <input type="checkbox" class="custom-form-element form-checkbox individual-template-form" data-file-id="{{ $form -> file_id }}" data-file-name="{{ $form -> file_name }}" data-file-name-display="{{ $form -> file_name_display }}" data-pages-total="{{ $form -> pages_total }}" data-file-location="{{ $form -> file_location }}">
                                                        </div>
                                                        <div title="{{ $form -> file_name_display }}">
                                                            <a href="{{ $form -> file_location }}" target="_blank">{{ shorten_text($form -> file_name_display, 65) }}</a>
                                                        </div>
                                                    </div>
                                                    <div class="mr-3 d-none d-lg-block">
                                                        @foreach($form_tags as $tag)
                                                        <span class="badge badge-pill form-pill text-white ml-1" style="background-color: {{ $resource_items -> getTagColor($tag) }}">{{ $resource_items -> getResourceName($tag) }}</span>
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

                        <div class="row">
                            <div class="col-12 col-lg-4 pt-5 mx-auto">
                                <select class="custom-form-element form-select form-select-no-search form-select-no-cancel required" id="individual_templates_folder" data-label="Select Folder To Add Forms To">
                                    @foreach($folders as $folder)
                                        @if($folder -> folder_name != 'Trash')
                                        <option value="{{ $folder -> id }}">{{ $folder -> folder_name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                    <a class="btn btn-success" id="save_add_individual_template_button"><i class="fad fa-check mr-2"></i> Add Documents</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="add_checklist_template_modal" tabindex="-1" role="dialog" aria-labelledby="add_checklist_template_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form id="add_checklist_template_form">
                <div class="modal-header bg-primary draggable-handle">
                    <h4 class="modal-title" id="add_checklist_template_modal_title">Add Checklist Template Documents</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="h5 text-primary">Import the selected templates below</div>
                                <br>
                                <div class="row">
                                    <div class="col-12 col-lg-6 col-xl-4">
                                        <select class="custom-form-element form-select form-select-no-search form-select-no-cancel required" id="checklist_templates_folder" data-label="Select Folder">
                                            @foreach($folders as $folder)
                                            @if($folder -> folder_name != 'Trash')
                                            <option value="{{ $folder -> id }}">{{ $folder -> folder_name }}</option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <ul class="list-group">
                                    @foreach($checklist_forms as $checklist_form)
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-start">
                                            <div>
                                                <input type="checkbox" class="custom-form-element form-checkbox checklist-template-form" data-file-id="{{ $checklist_form -> file_id }}" data-file-name="{{ $checklist_form -> file_name }}" data-file-name-display="{{ $checklist_form -> file_name_display }}" data-pages-total="{{ $checklist_form -> pages_total }}" data-file-location="{{ $checklist_form -> file_location }}" checked>
                                            </div>
                                            <div class="ml-3">
                                                <a href="{{ $checklist_form -> file_location }}" target="_blank">{{ $checklist_form -> file_name_display }}</a>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                    <a class="btn btn-success" id="save_add_checklist_template_button"><i class="fad fa-check mr-2"></i> Add Documents</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="upload_documents_modal" tabindex="-1" role="dialog" aria-labelledby="upload_documents_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form id="upload_documents_form">
                <div class="modal-header bg-primary draggable-handle">
                    <h4 class="modal-title" id="upload_documents_modal_title">Upload Documents</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 col-md-5">
                                <select class="custom-form-element form-select form-select-no-search form-select-no-cancel required" id="documents_folder" data-label="Select Folder">
                                    @foreach($folders as $folder)
                                    @if($folder -> folder_name != 'Trash')
                                    <option value="{{ $folder -> id }}">{{ $folder -> folder_name }}</option>
                                    @endif
                                    @endforeach
                                </select>

                                <div id="file_upload" class="dm-uploader p-5 mt-3">
                                    <h3 class="mb-5 mt-5 text-muted">Drag &amp; drop files here</h3>

                                    <div class="btn btn-primary btn-block mb-5">
                                        <span>Click to browse files</span>
                                        <input type="file" title='Click to browse files' />
                                    </div>
                                </div>

                            </div>
                            <div class="col-12 col-md-7">
                                <div class="card h-100">
                                    <div class="card-header bg-primary text-white">
                                        Pending File List
                                    </div>

                                    <ul class="list-unstyled p-2 d-flex flex-column col" id="files_queue">
                                        <li class="text-muted text-center empty">No files uploaded.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                    <a class="btn btn-success" id="save_upload_documents_button"><i class="fad fa-check mr-2"></i> Upload Documents</a>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade draggable" id="add_folder_modal" tabindex="-1" role="dialog" aria-labelledby="add_folder_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="add_folder_form">
                <div class="modal-header bg-primary draggable-handle">
                    <h4 class="modal-title" id="add_folder_modal_title">Add Folder</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="p-3">
                        <div class="h5 text-primary text-center mb-4">Enter Folder Name <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Adding a Folder" data-content="You can create multiple folders for different types of documents. Examples include 'Original Files', 'Signed Docs'"><i class="fad fa-question-circle ml-2"></i></a>
                        </div>
                        <input type="text" class="custom-form-element form-input required" id="new_folder_name" data-label="Folder Name">
                    </div>
                    <div class="modal-footer d-flex justify-content-around">
                        <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                        <a class="btn btn-success" id="save_add_folder_button"><i class="fad fa-check mr-2"></i> Save Folder</a>
                    </div>
            </form>
        </div>
    </div>
</div>
