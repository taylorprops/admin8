@extends('layouts.main')
@section('title', 'Uploaded Files')
@section('content')
<div class="container page-files">
    <h2>Forms</h2>
    <div class="row">
        <div class="col-4">

            <div class="border-top border-bottom border-gray">
                <div class="list-group-container" data-simplebar data-simplebar-auto-hide="false">
                    <div class="list-group" role="tablist">
                        @foreach ($resources as $resource)
                        @if($resource -> resource_type == 'form_groups')
                        <a class="list-group-item list-group-item-action @if ($loop -> first) active @endif"
                            id="list_{{ $resource -> resource_id }}"
                            data-toggle="list"
                            href="#list_div_{{ $resource -> resource_id }}"
                            role="tab"
                            data-id="{{ $resource -> resource_id }}">
                            {{ $resource -> resource_name }}
                            <span class="float-right badge bg-blue-med py-1 px-2" id="list_{{ $resource -> resource_id }}_file_count">{{-- {{ $association -> getCountFormGroup($resource -> resource_id) }} --}}</span>
                        </a>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-8">
            <div class="tab-content"{{--  id="association" --}}>
                @foreach ($resources as $resource)
                @if($resource -> resource_type == 'form_groups')

                <div class="list-div tab-pane fade @if ($loop -> first) show active @endif" id="list_div_{{ $resource -> resource_id }}" role="tabpanel" aria-labelledby="list_{{ $resource -> resource_id }}">

                    <div class="d-flex justify-content-between mt-3 mb-3">
                        <div class="h3 text-primary">{{ $resource -> resource_name }}</div>
                        <div class="d-flex justify-content-end">
                            <select class="form-select form-select-no-search form-select-no-search uploads-filter-options" data-label="Filter Results">
                                <option value="all">Show All</option>
                                <option value="published">Show Published</option>
                                <option value="notpublished">Show Unpublished</option>
                            </select>
                            <a href="javascript: void(0)" data-state="{{ $resource -> resource_state }}" data-form-group-id="{{ $resource -> resource_id }}" class="btn btn-success upload-file-button ml-5"><i class="fal fa-plus mr-2"></i> Add Form</a>
                        </div>
                    </div>

                    <div class="border border-gray">
                        <div class="list-group-divs pt-4" data-simplebar data-simplebar-auto-hide="false">
                            <div class="container">
                                <div class="row">
                                    <div class="col-12 forms-data" id="list_div_{{ $resource -> resource_id }}_files" data-form-group-id="{{ $resource -> resource_id }}" data-state="{{ $resource -> resource_state }}">
                                        {{-- @foreach ($files as $file)

                                        @if($file -> form_group_id == $resource -> resource_id)
                                        <div class="border-bottom border-primary p-1 mb-4 uploads-list @if($file -> published == 'yes') published @else notpublished @endif">
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-7">
                                                        <div class="h5 text-secondary">{{ $file -> file_name_display }}</div>
                                                    </div>
                                                    <div class="col-5">
                                                        <div class="d-flex justify-content-end">
                                                            @php $tags = explode(',', $file -> sale_type); @endphp
                                                            @foreach($tags as $tag)
                                                            <span class="badge mr-2" style="background-color: {{ $resource_items -> getTagColor($tag) }}">{{ $resource_items -> getTagName($tag) }}</span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">

                                                    <div class="col-12 options-holder">

                                                        <div class="row">
                                                            <div class="col">
                                                                @if($file -> published == 'no')
                                                                <a href="/doc_management/create/add_fields/{{ $file -> file_id }}" class="text-primary"><i class="fal fa-plus mr-2"></i> Add/Edit Fields</a>
                                                                @endif
                                                            </div>
                                                            <div class="col">
                                                                <a href="javascript:void(0)" class="edit-upload text-primary" data-id="{{ $file -> file_id }}"><i class="fad fa-edit mr-2"></i> Edit Details</a>
                                                            </div>
                                                            <div class="col">
                                                                <a href="javascript:void(0)" class="duplicate-upload text-primary" data-id="{{ $file -> file_id }}" data-state="{{ $resource -> resource_state }}" data-form-group-id="{{ $resource -> resource_id }}"><i class="fad fa-clone mr-2"></i> Duplicate</a>
                                                            </div>
                                                            <div class="col">
                                                                @if($file -> published == 'no')
                                                                <a href="javascript:void(0)" class="publish-upload text-success" data-id="{{ $file -> file_id }}" data-state="{{ $resource -> resource_state }}" data-form-group-id="{{ $resource -> resource_id }}"><i class="fad fa-file-export mr-2"></i> Publish</a>
                                                                @else
                                                                <a href="javascript: void(0" class="material-tooltip-main mr-5" data-toggle="tooltip" title="This form can no longer be edited or deleted"><span class="badge badge-success">Published</span></a>
                                                                @endif
                                                            </div>
                                                            <div class="col">
                                                                @if($file -> published == 'no')
                                                                <a href="javascript:void(0)" class="delete-upload text-danger" data-id="{{ $file -> file_id }}" data-state="{{ $resource -> resource_state }}" data-form-group-id="{{ $resource -> resource_id }}"><i class="fad fa-trash-alt mr-2"></i> Delete</a>
                                                                @endif
                                                            </div>
                                                            <div class="col">
                                                                <div class="small">Added: {{ date('M jS, Y', strtotime($file -> created_at)) }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><!-- ./ .row -->
                                            </div><!-- ./ .container -->
                                        </div>
                                        @endif
                                        @endforeach --}}
                                    </div>
                                </div><!-- ./ .row -->
                            </div>
                        </div>
                    </div>
                </div>
                @endif
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
                <div class="modal-header bg-primary draggable-handle">
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
                                <select name="edit_sale_type[]" id="edit_sale_type" class="form-select form-select-no-cancel required" data-label="Select Form Types" multiple>
                                    <option value=""></option>
                                    @foreach($resources as $resource)
                                    @if($resource -> resource_type == 'form_tags')
                                    <option value="{{ $resource -> resource_id }}">{{ $resource -> resource_name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-3">
                                <select name="edit_form_group_id" id="edit_form_group_id" class="form-select form-select-no-cancel required" data-label="Select From Group">
                                    <option value=""></option>
                                    @foreach($resources as $resource)
                                    @if($resource -> resource_type == 'form_groups')
                                    <option value="{{ $resource -> resource_id }}" data-state="{{ $resource -> resource_state }}">{{ $resource -> resource_name }}</option>
                                    @endif
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
                    <a class="btn btn-primary" id="save_edit_file_button"><i class="fad fa-save mr-2"></i> Save Details</a>
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
                <div class="modal-header bg-primary draggable-handle">
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
                                <select name="sale_type[]" id="sale_type" class="form-select form-select-no-cancel required" data-label="Select Form Types" multiple>
                                    <option value=""></option>
                                    @foreach($resources as $resource)
                                    @if($resource -> resource_type == 'form_tags')
                                    <option value="{{ $resource -> resource_id }}">{{ $resource -> resource_name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-3">
                                <select name="form_group_id" id="form_group_id" class="form-select form-select-no-cancel required" data-label="Select Form Group">
                                    <option value=""></option>
                                    @foreach($resources as $resource)
                                    @if($resource -> resource_type == 'form_groups')
                                    <option value="{{ $resource -> resource_id }}" data-state="{{ $resource -> resource_state }}">{{ $resource -> resource_name }}</option>
                                    @endif
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

<div class="modal fade" id="confirm_publish_modal" tabindex="-1" role="dialog" aria-labelledby="confirm_publish_modal_title"
    aria-hidden="true">

    <!-- Add .modal-dialog-centered to .modal-dialog to vertically center the modal -->
    <div class="modal-dialog modal-dialog-centered" role="document">

        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h3 class="modal-title" id="confirm_publish_modal_title">Delete Form</h3>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to publish this form?
                <div class="alert alert-danger"><i class="fad fa-exclamation-triangle mr-2 text-danger"></i> Once published you can no longer add or edit fields</div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-sm btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-success" id="confirm_publish"><i class="fad fa-check mr-2"></i> Confirm</a>
            </div>
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
