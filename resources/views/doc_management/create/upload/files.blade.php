@extends('layouts.main')
@section('title', 'Uploaded Files')
@section('content')
<div class="container page-files pt-4">

    <div class="row">
        <div class="col-4">
            <h1 class="text-primary mb-3">Forms</h1>
            <div class="border-top border-bottom border-gray">
                <div id="associations_list_container" data-simplebar data-simplebar-auto-hide="false">
                    <div class="list-group" id="associations_list" role="tablist">
                        @foreach ($associations as $association)
                        <a class="list-group-item list-group-item-action @if ($loop -> first) active @endif" id="association_list_{{ $association -> id }}" data-toggle="list" href="#association_{{ $association -> id }}" role="tab" data-id="{{ $association -> id }}">{{ $association -> association }} <span class="float-right badge bg-blue-med py-1 px-2" id="association_{{ $association -> id }}_file_count">{{ $association -> getCountAssociationForms($association -> id) }}</span></a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-8">
            <div class="tab-content" id="association">
                @foreach ($associations as $association)
                <div class="tab-pane fade @if ($loop -> first) show active @endif" id="association_{{ $association -> id }}" role="tabpanel" aria-labelledby="association_list_{{ $association -> id }}">
                    <div class="d-flex justify-content-between mt-3">
                        <div class="h3 text-primary">{{ $association -> association }}</div>
                        <div>
                            <a href="javascript: void(0)" data-state="{{ $association -> state }}" data-association-id="{{ $association -> id }}" class="btn btn-sm btn-primary upload-file-button"><i class="fal fa-plus mr-2"></i> Add Form</a>
                        </div>
                    </div>
                    <div class="border border-gray">
                        <div id="associations_container" class="pt-4" data-simplebar data-simplebar-auto-hide="false">
                            <div class="container" id="uploaded_files">
                                <div class="row">
                                    <div class="col-12" id="association_{{ $association -> id }}_files">
                                        @foreach ($files as $file)

                                        @if($file -> association_id == $association -> id)
                                        <div class="border-bottom border-primary p-1 mb-4">
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="h5 text-secondary">{{ $file -> file_name_display }}</div>
                                                            <div class="small">Added: {{ date('M jS, Y g:i:sA', strtotime($file -> created_at)) }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="d-flex justify-content-between">
                                                            @if($file -> published == 'yes')
                                                            <a href="javascript: void(0" class="material-tooltip-main" data-toggle="tooltip"
                                                            title="This form can no longer be edited or deleted"><span class="badge badge-secondary">Published</span></a>
                                                            <a href="javascript:void(0)" class="edit-upload text-primary" data-id="{{ $file -> file_id }}"><i class="fad fa-edit mr-2"></i> Edit Details</a>
                                                            <a href="javascript:void(0)" class="duplicate-upload text-primary" data-id="{{ $file -> file_id }}" data-state="{{ $association -> state }}" data-association-id="{{ $association -> id }}"><i class="fad fa-clone mr-2"></i> Duplicate Form</a>
                                                            @else
                                                            <a href="/doc_management/create/add_fields/{{ $file -> file_id }}" class="text-primary"><i class="fal fa-plus mr-2"></i> Add Fields</a>
                                                            <a href="javascript:void(0)" class="edit-upload text-primary" data-id="{{ $file -> file_id }}"><i class="fad fa-edit mr-2"></i> Edit Details</a>
                                                            <a href="javascript:void(0)" class="duplicate-upload text-primary" data-id="{{ $file -> file_id }}" data-state="{{ $association -> state }}" data-association-id="{{ $association -> id }}"><i class="fad fa-clone mr-2"></i> Duplicate Form</a>
                                                            <a href="javascript:void(0)" class="publish-upload text-success" data-id="{{ $file -> file_id }}"><i class="fad fa-file-export mr-2"></i> Publish Form</a>
                                                            <a href="javascript:void(0)" class="delete-upload text-danger" data-id="{{ $file -> file_id }}" data-state="{{ $association -> state }}" data-association-id="{{ $association -> id }}"><i class="fad fa-trash-alt mr-2"></i> Delete Form</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div><!-- ./ .row -->
                                            </div><!-- ./ .container -->
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>
                                </div><!-- ./ .row -->
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div><!-- ./ .container -->

<!-- Modals -->
<div class="modal fade draggable" id="edit_file_modal" tabindex="-1" role="dialog" aria-labelledby="edit_file_modal_title" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered" role="document">

        <div class="modal-content">
            <form id="edit_file_form">
                <div class="modal-header bg-primary">
                    <h3 class="modal-title" id="edit_file_modal_title">Edit Form Details</h3>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    @csrf
                    <div class="container">
                        <div class="row">
                            <div class="col-12 my-3">
                                <input type="text" class="form-input required" name="edit_file_name_display" id="edit_file_name_display" data-label="Form Name">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-3">
                                <select name="edit_sale_type[]" id="edit_sale_type" class="form-select form-select-no-cancel required" data-label="Select Form Type" multiple>
                                    <option value=""></option>
                                    <option value="listing">Listing</option>
                                    <option value="contract">Contract</option>
                                    <option value="rental">Rental</option>
                                    <option value="custom">Custom</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-3">
                                <select name="edit_association_id" id="edit_association_id" class="form-select form-select-no-cancel required" data-label="Select Association" >
                                    <option value=""></option>
                                    @foreach($associations as $association)
                                    <option value="{{ $association -> id }}" data-state="{{ $association -> state }}">{{ $association -> association }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-3">
                                <select name="edit_state" id="edit_state" class="form-select form-select-no-cancel required" data-label="Select State">
                                    <option value=""></option>
                                    @foreach($states as $state)
                                    <option value="{{ $state }}">{{ $state }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <a class="btn btn-primary" id="save_edit_file_button"><i class="fad fa-upload mr-2"></i> Save Details</a>
                </div>
                <input type="hidden" name="edit_file_id" id="edit_file_id">
            </form>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="add_upload_modal" tabindex="-1" role="dialog" aria-labelledby="add_upload_modal_title" aria-hidden="true">

    <!-- Add .modal-dialog-centered to .modal-dialog to vertically center the modal -->
    <div class="modal-dialog modal-dialog-centered" role="document">

        <div class="modal-content">
            <form id="upload_file_form" enctype="multipart/form-data">
                <div class="modal-header bg-primary">
                    <h3 class="modal-title" id="add_upload_modal_title">Add Form</h3>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    @csrf
                    <div class="container">
                        <div class="row">
                            <div class="col-12 my-3">
                                <input type="file" class="form-input-file required" name="file_upload" id="file_upload" data-label="Select File">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-3">
                                <input type="text" class="form-input required" name="file_name_display" id="file_name_display" data-label="Form Name">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-3">
                                <select name="sale_type[]" id="sale_type" class="form-select form-select-no-cancel required" data-label="Select Form Type" multiple>
                                    <option value=""></option>
                                    <option value="listing">Listing</option>
                                    <option value="contract">Contract</option>
                                    <option value="rental">Rental</option>
                                    <option value="custom">Custom</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-3">
                                <select name="association_id" id="association_id" class="form-select form-select-no-cancel required" data-label="Select Association" >
                                    <option value=""></option>
                                    @foreach($associations as $association)
                                    <option value="{{ $association -> id }}" data-state="{{ $association -> state }}">{{ $association -> association }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-3">
                                <select name="state" id="state" class="form-select form-select-no-cancel required" data-label="Select State">
                                    <option value=""></option>
                                    @foreach($states as $state)
                                    <option value="{{ $state }}">{{ $state }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <a class="btn btn-primary" id="upload_file_button"><i class="fad fa-upload mr-2"></i> Upload Form</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="confirm_delete_modal" tabindex="-1" role="dialog" aria-labelledby="confirm_delete_modal_title"
    aria-hidden="true">

    <!-- Add .modal-dialog-centered to .modal-dialog to vertically center the modal -->
    <div class="modal-dialog modal-dialog-centered" role="document">

        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h3 class="modal-title" id="confirm_delete_modal_title">Delete Form</h3>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to permanently delete this form?
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-sm btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-success" id="confirm_delete"><i class="fad fa-check mr-2"></i> Confirm</a>
            </div>
        </div>
    </div>
</div>
@endsection
