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
                                    <div class="form-groups-container mt-3">

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
