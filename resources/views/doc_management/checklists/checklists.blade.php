@extends('layouts.main')
@section('title', 'Checklists')

@section('content')
<div class="container page-checklists">
    <h2>Checklists</h2>
    <div class="container">

        <div class="row">
            <div class="col-3">
                <div class="border-top border-bottom border-gray">
                    <div class="list-group-container" data-simplebar data-simplebar-auto-hide="false">
                        <div class="list-group" role="tablist">
                            @foreach ($locations as $location)
                            <a class="list-group-item list-group-item-action @if ($loop -> first) active @endif"
                                id="list_{{ $location -> resource_id }}"
                                data-toggle="list"
                                href="#list_div_{{ $location -> resource_id }}"
                                role="tab"
                                data-id="{{ $location -> resource_id }}">
                                @if($location -> resource_name != $location -> resource_state) {{ $location -> resource_state }} | @endif {{ $location -> resource_name }}
                                <span class="float-right badge bg-blue-med py-1 px-2" id="list_{{ $location -> resource_id }}_file_count">{{-- {{ $association -> getCountFormGroup($resource -> resource_id) }} --}}</span>
                            </a>

                            @endforeach
                        </div>
                    </div>
                </div>
            </div><!-- ./ .col -->

            <div class="col-9">

                <div class="tab-content">

                    @foreach($locations as $location)

                        <div class="list-div tab-pane fade @if ($loop -> first) show active @endif" id="list_div_{{ $location -> resource_id }}" role="tabpanel" aria-labelledby="list_{{ $location -> resource_id }}">

                            <div class="d-flex justify-content-between">
                                <div class="h3 text-primary">{{ $location -> resource_name }} @if($location -> resource_name != $location -> resource_state) | {{ $location -> resource_state }} @endif</div>
                                <div class="d-flex justify-content-end">
                                    <div>
                                        <select class="custom-form-element form-select form-select-no-search form-select-no-cancel checklist-type-option" data-label="Checklist Type">
                                            <option value="listing">Listing</option>
                                            <option value="contract">Contract</option>
                                        </select>
                                    </div>
                                    <div>
                                        <a href="javascript: void(0)" class="btn btn-primary ml-5 mt-3 duplicate-checklist-button"><i class="fad fa-clone mr-2"></i> Copy Checklist</a>
                                    </div>
                                    <div>
                                        <a href="javascript: void(0)" data-location-id="{{ $location -> resource_id }}" data-state="{{ $location -> resource_state }}" data-form-type="add" class="btn btn-success add-checklist-button ml-5 mt-3"><i class="fal fa-plus mr-2"></i> Add Checklist</a>
                                    </div>
                                </div>
                            </div>

                            <div class="border border-gray">
                                <div class="list-group-divs p-3" data-simplebar data-simplebar-auto-hide="false">

                                    <div class="container">
                                        <div class="row">
                                            <div class="col-12 checklist-data" id="list_div_{{ $location -> resource_id }}_files" data-location-id="{{ $location -> resource_id }}">

                                            </div>
                                        </div>
                                    </div>

                                </div> <!-- ./ .list-group-divs -->
                            </div>
                        </div> <!-- ./ .list-div -->

                    @endforeach

                </div>

            </div><!-- ./ .col -->

        </div><!-- ./ .row -->

    </div><!-- ./ .container -->

    <div class="modal fade" id="checklist_items_modal" tabindex="-1" role="dialog" aria-labelledby="checklist_items_modal_title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <form id="checklist_items_form">
                    <div class="modal-header bg-primary">
                        <h3 class="modal-title text-white" id="checklist_items_modal_title"></h3>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12" id="checklist_items_div"></div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-between">
                            <a class="btn btn-danger" data-dismiss="modal"><i class="fal fa-times mr-2"></i> Cancel</a>
                            <a class="btn btn-primary" id="save_checklist_items_button"><i class="fad fa-save mr-2"></i> Save Items</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade draggable" id="checklist_modal" tabindex="-1" role="dialog" aria-labelledby="checklist_modal_title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="checklist_form">
                    <div class="modal-header bg-primary draggable-handle">
                        <h3 class="modal-title" id="checklist_modal_title">Add/Edit Checklist</h3>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <select id="checklist_location_id" class="custom-form-element form-select form-select-no-cancel form-select-no-search required" data-label="Checklist Location">
                                        <option value=""></option>
                                        @foreach($locations as $location)
                                        <option value="{{ $location -> resource_id }}">@if ($location -> resource_state != $location -> resource_name){{ $location -> resource_state }}  | @endif{{ $location -> resource_name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <select id="checklist_sale_rent" class="custom-form-element form-select form-select-no-cancel form-select-no-search required" data-label="For Sale/Rental">
                                        <option value=""></option>
                                        <option value="sale">For Sale</option>
                                        <option value="rental">Rental</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <select id="checklist_property_type_id" class="custom-form-element form-select form-select-no-cancel form-select-no-search required" data-label="Checklist Property Type">
                                        <option value=""></option>
                                        @foreach($property_types as $property_type)
                                        <option value="{{ $property_type -> resource_id }}">{{ $property_type -> resource_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <select id="checklist_property_sub_type_id" class="custom-form-element form-select form-select-no-cancel form-select-no-search hidden required" data-label="Checklist Property Sub Type">
                                        <option value=""></option>
                                        @foreach($property_sub_types as $property_sub_type)
                                        <option value="{{ $property_sub_type -> resource_id }}">{{ $property_sub_type -> resource_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <select id="checklist_type" class="custom-form-element form-select form-select-no-cancel form-select-no-search required" data-label="Checklist Type">
                                        <option value=""></option>
                                        <option value="listing">Listing</option>
                                        <option value="contract">Contract/Lease</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <select id="checklist_represent" class="custom-form-element form-select form-select-no-cancel form-select-no-search required" data-label="Represent">
                                        <option value=""></option>
                                        <option value="seller">Seller/Owner</option>
                                        <option value="buyer">Buyer/Renter</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-center">
                            <a class="btn btn-primary" id="save_checklist_button"><i class="fad fa-save mr-2"></i> Save Details</a>
                        </div>
                    </div>
                    <input type="hidden" id="checklist_state">
                    <input type="hidden" id="form_type">
                    <input type="hidden" id="checklist_id">
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade draggable modal-confirm" id="confirm_delete_checklist_modal" tabindex="-1" role="dialog" aria-labelledby="confirm_delete_checklist_modal_title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal" role="document">
            <div class="modal-content">
                <form id="confirm_delete_checklist_form">
                    <div class="modal-header bg-danger draggable-handle">
                        <h3 class="modal-title" id="confirm_delete_checklist_modal_title">Delete Checklist</h3>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to permanently delete this checklist?
                    </div>
                    <div class="modal-footer d-flex justify-content-around">
                        <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</a>
                        <a class="btn btn-success modal-confirm-button" id="confirm_delete_checklist"><i class="fad fa-check"></i> Confirm</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<div class="modal fade modal-confirm" id="confirm_remove_file_modal" tabindex="-1" role="dialog" aria-labelledby="confirm_remove_file_modal_title"
    aria-hidden="true">

    <!-- Add .modal-dialog-centered to .modal-dialog to vertically center the modal -->
    <div class="modal-dialog modal-dialog-centered" role="document">

        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h3 class="modal-title" id="confirm_remove_file_modal_title">Remove Form From Checklist</h3>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to remove this form from the checklist?
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-success modal-confirm-button" id="confirm_remove_file"><i class="fad fa-check mr-2"></i> Confirm</a>
            </div>
        </div>
    </div>
</div>


@endsection

