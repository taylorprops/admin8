@php
$location = $resource_items -> getLocation($checklist -> checklist_location_id);
@endphp

<div class="row">
    <div class="col-9">
        <h4>Checklist Items</h4>
        <div class="checklist-items-selected border border-primary" data-simplebar data-simplebar-auto-hide="false">
            <ul class="list-group sortable-checklist-items">

            </ul>
        </div>
    </div>
    <div class="col-3">

        <h4 class="mb-3">Forms</h4>

        <div>
            <select class="form-select form-select-no-cancel form-select-no-search select-form-group mt-3" data-label="Select Form Group">
                <option value="all">All</option>
                @foreach($form_groups as $form_group)
                <option value="{{ $form_group -> resource_id }}" @if($loop -> first) selected @endif>{{ $form_group -> resource_state }} @if($form_group -> resource_state != $form_group -> resource_name) | {{ $form_group -> resource_name }} @endif</option>
                @endforeach
            </select>
        </div>

        <div class="mt-3">
            <div class="d-flex justify-content-start">
                <i class="fal fa-search text-primary mt-2 mr-3 fa-2x"></i>
                <input type="text" class="form-input mr-5" id="form_search" data-label="Search">
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
                                <a href="javascript: void(0)" class="btn btn-sm btn-primary add-to-checklist" data-form-id="{{ $form -> file_id }}" data-text="{{ $form -> file_name_display }}">Add</a>
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
<input type="hidden" id="checklist_header_val" value="{{ $checklist -> checklist_state }} @if($checklist -> checklist_state != $location) | {{ $location }} @endif - {{ ucwords($checklist -> checklist_sale_rent) }} - {{ ucwords($checklist -> checklist_type) }} - {{ $checklist -> checklist_property_type }} @if($checklist -> checklist_property_sub_type != '') - {{ $checklist -> checklist_property_sub_type }}  @endif - {{ ucwords($checklist -> checklist_represent) }}">


{{-- options for checklist group select --}}
<div class="d-none" id="checklist_groups_options">
@foreach($checklist_groups as $checklist_group)
<option value="{{ $checklist_group -> resource_name }}">{{ $checklist_group -> resource_name }}</option>
@endforeach
</div>
