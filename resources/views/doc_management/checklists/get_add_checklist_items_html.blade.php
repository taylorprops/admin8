@php
$location = $resource_items -> getLocation($checklist -> checklist_location_id);
@endphp

<div class="row">
    <div class="col-9">
        <div class="row">
            <div class="col-8">
                <h4>Checklist Items</h4>
            </div>
            <div class="col-4">
                <a href="javascript: void(0);" class="btn btn-sm btn-primary add-checklist-item-no-form-button float-right"><i class="fa fa-plus mr-2"></i>Add Checklist Item</a>
            </div>
        </div>
        <div class="checklist-items-selected border border-primary" data-simplebar data-simplebar-auto-hide="false">
            <ul class="list-group sortable-checklist-items">
                @foreach($checklist_items as $checklist_item)
                @php
                $form_id = $checklist_item -> checklist_form_id ?? null;
                $form_name = '';
                $form_name_orig = '';
                if($form_id) {
                    $form_name = $files -> getFormName($form_id);
                    $form_name_orig = $form_name;
                    if(strlen($form_name) > 40) {
                        $form_name = substr($form_name, 0, 40).'...';
                    }
                }
                @endphp

                <li class="list-group-item checklist-item w-100 pt-1 pb-0" data-form-id="{{ $checklist_item -> checklist_form_id }}">
                    <div class="row">
                        <div class="col-4">
                            <div class="row">
                                <div class="col-1">
                                    <i class="fas fa-sort fa-lg mr-1 mt-4 text-primary checklist-item-handle ui-sortable-handle"></i>
                                </div>
                                <div class="col-11">
                                    <input type="text" class="custom-form-element form-input checklist-item-name required" value="{{ $checklist_item -> checklist_item_name }}" data-label="Form Display Name">
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            @if($form_name)
                            <span class="font-8 text-secondary">Form</span>
                            <div class="h5 text-primary mt-2" title="{{ $form_name_orig }}"><a href="/{{ $files -> getFormLocation($form_id) }}" target="_blank">{{ $form_name }}</a></div>
                            @endif
                        </div>
                        <div class="col-4">
                            <div class="row">
                                <div class="col">
                                    <select class="custom-form-element form-select form-select-no-cancel checklist-item-required required" data-label="Required">
                                        <option value=""></option>
                                        <option value="yes" @if( $checklist_item -> checklist_item_required == 'yes') selected @endif>Yes</option>
                                        <option value="no" @if( $checklist_item -> checklist_item_required == 'no') selected @endif>No</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <select class="custom-form-element form-select form-select-no-cancel checklist-item-form-group-id required" data-label="Form Group">
                                        <option value=""></option>
                                        @foreach($checklist_groups as $checklist_group)
                                        <option value="{{ $checklist_group -> resource_id }}" @if( $checklist_item -> checklist_item_group_id == $checklist_group -> resource_id) selected @endif>{{ $checklist_group -> resource_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <a class="btn btn-danger delete-checklist-item-button ml-3 mt-3"><i class="fa fa-trash"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="col-3">

        <h4 class="mb-3">Forms</h4>

        <div>
            <select class="custom-form-element form-select form-select-no-cancel form-select-no-search select-form-group mt-3" data-label="Select Form Group">
                <option value="all">All</option>
                @foreach($form_groups as $form_group)
                <option value="{{ $form_group -> resource_id }}" @if($loop -> first) selected @endif>{{ $form_group -> resource_state }} @if($form_group -> resource_state != $form_group -> resource_name) | {{ $form_group -> resource_name }} @endif</option>
                @endforeach
            </select>
        </div>

        <div class="mt-3">
            <div class="d-flex justify-content-start">
                <i class="fal fa-search text-primary mt-2 mr-3 fa-2x"></i>
                <input type="text" class="custom-form-element form-input mr-5" id="form_search" data-label="Search">
            </div>
        </div>

        <div class="form-groups-container mt-3" data-simplebar data-simplebar-auto-hide="false">

            @foreach($form_groups as $form_group)

                <ul class="list-group form-group-div" data-form-group-id="{{ $form_group -> resource_id }}">
                    <li class="list-group-header">
                        {{ $form_group -> resource_state }}
                        @if($form_group -> resource_state != $form_group -> resource_name) | {{ $form_group -> resource_name }} @endif
                    </li>

                    @php
                    $forms = $files -> formGroupFiles($form_group -> resource_id);
                    @endphp

                    @foreach($forms as $form)

                    <li class="list-group-item form-name" data-form-id="{{ $form -> file_id }}" data-text="{{ $form -> file_name_display }}">
                        <div class="d-flex justify-content-start">
                            <div class="mr-2">
                                <a href="javascript: void(0)" class="btn btn-sm btn-primary add-to-checklist-button" data-form-id="{{ $form -> file_id }}" data-text="{{ $form -> file_name_display }}">Add</a>
                            </div>
                            <div>{{ $form -> file_name_display }}</div>
                        </div>
                    </li>

                    @endforeach

                </ul>

            @endforeach

        </div>

    </div>
</div>
<input type="hidden" id="checklist_id" value="{{ $checklist -> id }}">
<input type="hidden" id="add_item_checklist_location_id" value="{{ $checklist -> checklist_location_id }}">
<input type="hidden" id="add_item_checklist_type" value="{{ $checklist -> checklist_type }}">


<input type="hidden" id="checklist_header_val" value="{{ $checklist -> checklist_state }} @if($checklist -> checklist_state != $location) | {{ $location }} @endif - {{ ucwords($checklist -> checklist_sale_rent) }} - {{ ucwords($checklist -> checklist_type) }} - {{ $checklist -> checklist_property_type }} @if($checklist -> checklist_property_sub_type != '') - {{ $checklist -> checklist_property_sub_type }}  @endif - {{ ucwords($checklist -> checklist_represent) }}">


{{-- options for checklist group select --}}
<div class="d-none" id="checklist_groups_options">
@foreach($checklist_groups as $checklist_group)
<option value="{{ $checklist_group -> resource_id }}">{{ $checklist_group -> resource_name }}</option>
@endforeach
</div>
