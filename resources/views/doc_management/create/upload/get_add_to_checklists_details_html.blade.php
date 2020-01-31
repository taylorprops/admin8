<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <h5 class="text-primary mb-0">Form Options</h5>
                    <input type="hidden" id="add_to_checklist_file_id" value="{{ $file_id }}">
                    <div class="d-flex justify-content-start">
                        <div class="">
                            <select class="custom-form-element form-select form-select-no-cancel form-select-no-search checklist-item-required required" data-label="Required">
                                <option value=""></option>
                                <option value="yes" @if( $checklist_item_required == 'yes') selected @endif>Yes</option>
                                <option value="no" @if( $checklist_item_required == 'no') selected @endif>No</option>
                            </select>
                        </div>
                        <div class="ml-2">
                            <select class="custom-form-element form-select form-select-no-cancel form-select-no-search checklist-item-group-id required" data-label="Form Group">
                                <option value=""></option>
                                @foreach($checklist_groups as $checklist_group)
                                <option value="{{ $checklist_group -> resource_id }}" @if( $checklist_item_group_id == $checklist_group -> resource_id) selected @endif>{{ $checklist_group -> resource_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="add-to-checklists-container">
                        <h5 class="text-primary">Checklists</h5>
                        <div class="border">
                            <div class="add-to-checklists-div">
                                <div class="checklist-filter-div d-flex justify-content-start">
                                    <div class="ml-2 mt-4 nowrap">
                                        <h5 class="text-orange">Filter Checklists</h5>
                                    </div>
                                    <div class="ml-2">
                                        <select class="custom-form-element form-select form-select-no-search checklist-filter" data-type="state" data-label="State">
                                            <option value="">All</option>
                                            @foreach($states as $state)
                                                @if($state != 'All')
                                                    <option value="{{ $state }}">{{ $state }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="ml-2">
                                        <select class="custom-form-element form-select form-select-no-search checklist-filter" data-type="region" data-label="Region">
                                            <option value="">All</option>
                                            @foreach($checklist_locations as $checklist_location)
                                                <option value="{{ $checklist_location -> resource_id }}">{{ $checklist_location -> resource_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="ml-2">
                                        <select class="custom-form-element form-select form-select-no-search checklist-filter" data-type="type" data-label="Type">
                                            <option value="">All</option>
                                            <option value="listing">Listing</option>
                                            <option value="contract">Contract</option>
                                        </select>
                                    </div>
                                    <div class="ml-2">
                                        <select class="custom-form-element form-select form-select-no-search checklist-filter" data-type="represent" data-label="Represent">
                                            <option value="">All</option>
                                            <option value="seller">Seller/Owner</option>
                                            <option value="buyer">Buyer/Renter</option>
                                        </select>
                                    </div>
                                    <div class="ml-2">
                                        <select class="custom-form-element form-select form-select-no-search checklist-filter" data-type="sale-rent" data-label="Sale/Rental">
                                            <option value="">All</option>
                                            <option value="sale">Sale</option>
                                            <option value="rental">Rental</option>
                                        </select>
                                    </div>
                                    <div class="ml-2">
                                        <select class="custom-form-element form-select form-select-no-search checklist-filter" data-type="property-type" data-label="Property Type">
                                            <option value="">All</option>
                                            @foreach($property_types as $property_type)
                                                <option value="{{ $property_type -> resource_id }}">{{ $property_type -> resource_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="ml-2">
                                        <select class="custom-form-element form-select form-select-no-search checklist-filter" data-type="property-sub-type" data-label="Property Sub Type">
                                            <option value="">All</option>
                                            @foreach($property_sub_types as $property_sub_type)
                                                <option value="{{ $property_sub_type -> resource_id }}">{{ $property_sub_type -> resource_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="ml-2 mr-3">
                                        <select class="custom-form-element form-select form-select-no-search" id="filter_selected" data-label="Selected">
                                            <option value="">All</option>
                                            <option value="selected">Selected</option>
                                            <option value="not_selected">Not Selected</option>
                                        </select>
                                    </div>
                                </div>
                                <table class="table table-hover table-sm" id="add_to_checklists_table">
                                    <thead>
                                        <tr class="sticky-top">
                                            <th class="my-0 py-0"><input type="checkbox" class="custom-form-element form-checkbox text-white" id="select_all_checklists" data-label=""></th>
                                            <th></th>
                                            <th>Order</th>
                                            <th>State</th>
                                            <th>County</th>
                                            <th>Type</th>
                                            <th>Represent</th>
                                            <th>Sale/Rent</th>
                                            <th>Property Type</th>
                                            <th>Property Sub Type</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($checklists as $checklist)
                                            @php
                                            $in_checklist = $checklists_items -> ifFormInChecklist($checklist -> id, $file_id);
                                            $checklist_order = $checklists_items -> where('checklist_form_id', $file_id) -> where('checklist_id', $checklist -> id) -> first();
                                            $order = 1;
                                            if($in_checklist) {
                                                $order = $checklist_order -> checklist_item_order + 1;
                                            }
                                            @endphp
                                            <tr class="checklist-items-tr filter-active @if($in_checklist) bg-blue-light @endif"
                                            data-state="{{ $checklist -> checklist_state }}"
                                            data-region="{{ $checklist -> checklist_location_id }}"
                                            data-type="{{ $checklist -> checklist_type }}"
                                            data-represent="{{ $checklist -> checklist_represent }}"
                                            data-sale-rent="{{ $checklist -> checklist_sale_rent }}"
                                            data-property-type="{{ $checklist -> checklist_property_type_id }}"
                                            data-property-sub-type="{{ $checklist -> checklist_property_sub_type_id }}">
                                                <td>
                                                    <input type="checkbox" class="custom-form-element form-checkbox checklist-item-checkbox" value="{{ $checklist -> id }}" data-label="" @if($in_checklist) checked @endif>
                                                    <div class="collapsible-container">
                                                        <ul class="collapse list-group checklist-items-collapsible border z-depth-3 mb-5" id="items_{{ $loop -> iteration }}">
                                                            @php
                                                            $items = $checklists_items -> getChecklistItems($checklist -> id);
                                                            $add = 0;
                                                            @endphp
                                                            @if($in_checklist === false)
                                                                @php $add = 1;
                                                                @endphp
                                                                <li class="list-group-item order-checklist-item order-checklist-item-sortable d-flex justify-content-start bg-orange-light">
                                                                    <i class="fad fa-arrows-v fa-lg text-primary order-checklist-item-sortable-handle mr-3 mt-2"></i>
                                                                    <span class="chip blue text-white checklist-item-order order-checklist-item-sortable-handle">1</span>
                                                                    <span class="mt-2 order-checklist-item-sortable-handle">{{ $uploaded_file -> file_name_display }}</span>
                                                                </li>
                                                            @endif
                                                            @foreach($items as $item)
                                                                @php
                                                                $handle = '';
                                                                $classes = '';
                                                                $fa = '';
                                                                $text_color = '';
                                                                $mr = 'mr-4';
                                                                if($item -> checklist_form_id == $file_id) {
                                                                    $fa = 'fa-arrows-v';
                                                                    $handle = 'order-checklist-item-sortable-handle';
                                                                    $classes = 'order-checklist-item-sortable bg-orange-light';
                                                                    $text_color = 'text-primary';
                                                                    $mr = 'mr-3';
                                                                }
                                                                @endphp
                                                                <li class="list-group-item order-checklist-item d-flex justify-content-start {{ $classes }}">
                                                                    <i class="fad {{ $fa }} fa-lg {{ $text_color }} {{ $handle }} {{ $mr }} mt-2"></i>
                                                                    <span class="chip blue text-white checklist-item-order {{ $handle }}">{{ $loop -> iteration + $add }}</span>
                                                                    <span class="mt-2 {{ $handle }}">{{ $upload -> getFormName($item -> checklist_form_id) }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <a class="btn btn-sm btn-primary show-checklist-items-collapsible" data-toggle="collapse" href="#items_{{ $loop -> iteration }}" aria-expanded="false" aria-controls="items_{{ $loop -> iteration }}">Show Checklist Items</a>
                                                    </div>
                                                </td>
                                                <td><input type="number" min="1" max="{{ count($items) + 1 }}" class="custom-form-element form-input numbers-only checklist-order" data-label="Order" style="width: 100px" value="{{ $order }}"></td>
                                                <td>{{ $checklist -> checklist_state }}</td>
                                                <td>{{ $resource_items -> getLocation($checklist -> checklist_location_id) }}</td>
                                                <td>{{ ucwords($checklist -> checklist_type) }}</td>
                                                <td>{{ ucwords($checklist -> checklist_represent) }}</td>
                                                <td>{{ ucwords($checklist -> checklist_sale_rent) }}</td>
                                                <td>{{ $resource_items -> getTagName($checklist -> checklist_property_type_id) }}</td>
                                                <td>{{ $resource_items -> getTagName($checklist -> checklist_property_sub_type_id) }}</td>
                                                <td><a href="/doc_management/checklists?checklist_id={{ $checklist -> id }}&checklist_location_id={{ $checklist -> checklist_location_id}}&checklist_type={{ $checklist -> checklist_type }}" class="btn btn-sm btn-primary" target="_blank">View Checklist</a></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- ./ .row -->
</div><!-- ./ .container -->
