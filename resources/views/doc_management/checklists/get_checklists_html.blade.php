<div class="h3 text-secondary mb-4"><span class="checklist-type">Listing</span> Checklists</div>
@foreach($checklist_property_types_items as $checklist_property_types)

<div class="property-type-div mb-4 mt-1 pb-3">

    <div class="d-flex justify-content-start">
        <h4 class="text-primary">{{ $checklist_property_types[0]['checklist_property_type'] }}</h4>
        <a href="javascript: void(0)"
            data-location-id="{{ $checklist_property_types[0]['checklist_location_id'] }}"
            data-state="{{ $checklist_property_types[0]['checklist_state'] }}"
            data-property-type="{{ $checklist_property_types[0]['checklist_property_type'] }}"
            data-form-type="add"
            class="text-success add-checklist-button mt-1 ml-5">
            <i class="fal fa-plus mr-2"></i>
            Add Checklist
        </a>
    </div>

    <div class="sortable-checklist list-group">
        @foreach($checklist_property_types as $checklist_property_type)

            @php $checklist_type = $checklist_property_type['checklist_type']; @endphp

            <div class="checklist-items-container border-bottom border-gray checklist-items-{{ $checklist_type }} @if($checklist_type == 'contract') hidden @endif" data-checklist-id="{{ $checklist_property_type['checklist_id'] }}">
                <div class="row my-2">
                    <div class="col-6">
                        <div class="row mt-2 checklist-items">
                            <div class="col">
                                <i class="fas fa-sort mr-2 mt-1 list-item-handle text-primary"></i>
                                <span class="text-primary-dark font-weight-bold ml-2 list-item-handle">
                                    {{ ucwords($checklist_property_type['checklist_sale_rent']) }}
                                </span>
                            </div>
                            <div class="col">
                                <span class="text-primary list-item-handle">
                                    Client: <span class="font-weight-bold">{{ ucwords($checklist_property_type['checklist_represent']) }}</span>
                                </span>
                            </div>
                            <div class="col-5">
                                <span class="text-orange list-item-handle">
                                    {{ $checklist_property_type['checklist_property_sub_type'] ?? 'Standard' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-start">
                            <div>
                                <span class="chip bg-primary text-white">{{ $checklist_property_type['checklist_count'] ?? 0 }}</span>
                            </div>
                            <div>
                                <a href="javascript: void(0)"
                                    data-checklist-id="{{ $checklist_property_type['checklist_id'] }}"
                                    class="btn btn-sm btn-primary float-right add-items-button mx-2">
                                    <i class="fal fa-plus mr-2"></i>
                                    Add Items
                                </a>
                            </div>
                            <div>
                                <a href="javascript: void(0)"
                                    data-checklist-id="{{ $checklist_property_type['checklist_id'] }}"
                                    data-location-id="{{ $checklist_property_type['checklist_location_id'] }}"
                                    data-state="{{ $checklist_property_type['checklist_state'] }}"
                                    data-property-type="{{ $checklist_property_type['checklist_property_type'] }}"
                                    data-sale-rent="{{ $checklist_property_type['checklist_sale_rent'] }}"
                                    data-represent="{{ $checklist_property_type['checklist_represent'] }}"
                                    data-property-sub-type="{{ $checklist_property_type['checklist_property_sub_type'] }}"
                                    data-form-type="edit"
                                    class="btn btn-sm btn-primary float-right edit-checklist-button mx-2">
                                    <i class="fad fa-edit mr-2"></i>
                                    Edit
                                </a>
                            </div>
                            <div>
                                <a href="javascript: void(0)"
                                    data-checklist-id="{{ $checklist_property_type['checklist_id'] }}"
                                    data-checklist-location-id="{{ $checklist_property_type['checklist_location_id'] }}"
                                    data-checklist-type="{{ $checklist_property_type['checklist_type'] }}"
                                    class="btn btn-sm btn-danger float-right delete-checklist-button mx-2">
                                    <i class="fad fa-trash mr-2"></i>
                                    Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endforeach
    </div>

</div> <!-- ./ .property-type-div -->

@endforeach
<input type="hidden" id="files_count" value="{{ $checklists_count }}">


