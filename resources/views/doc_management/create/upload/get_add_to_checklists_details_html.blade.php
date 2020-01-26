<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <h5 class="text-primary mb-0">Form Options</h5>
                    <div class="d-flex justify-content-start">
                        <div class="">
                            <select class="custom-form-element form-select form-select-no-cancel form-select-no-search checklist-item-required required" data-label="Required">
                                <option value=""></option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                        <div class="ml-2">
                            <select class="custom-form-element form-select form-select-no-cancel form-select-no-search checklist-item-group-id required" data-label="Form Group">
                                <option value=""></option>
                                @foreach($checklist_groups as $checklist_group)
                                <option value="{{ $checklist_group -> resource_id }}">{{ $checklist_group -> resource_name }}</option>
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
                            <div class="add-to-checklists-div" data-simplebar data-simplebar-auto-hide="false">
                                <table class="table table-hover table-sm" id="add_to_checklists_table">
                                    <thead>
                                        <tr class="sticky-top bg-white">
                                            <th>Add</th>
                                            <th>Order</th>
                                            <th></th>
                                            <th></th>
                                            <th>State</th>
                                            <th>County</th>
                                            <th>Type</th>
                                            <th>Represent</th>
                                            <th>Sale/Rent</th>
                                            <th>Property Type</th>
                                            <th>Property Sub Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($checklists as $checklist)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="custom-form-element form-checkbox" value="{{ $checklist -> id }}" data-label="">
                                                <div class="collapsible-container">
                                                    <ul class="collapse list-group checklist-items-collapsible border z-depth-3" id="items_{{ $loop -> iteration }}">
                                                        @php
                                                        $items = $checklists_items -> getChecklistItems($checklist -> id);
                                                        @endphp
                                                        <li class="list-group-item order-checklist-item order-checklist-item-sortable d-flex justify-content-start bg-orange-light">
                                                            <span class="chip blue text-white checklist-item-order order-checklist-item-sortable-handle">1</span>
                                                            <span class="mt-2 order-checklist-item-sortable-handle">{{ $uploaded_file -> file_name_display }}</span>
                                                        </li>
                                                        @foreach($items as $item)
                                                        <li class="list-group-item order-checklist-item d-flex justify-content-start">
                                                            <span class="chip blue text-white checklist-item-order">{{ $loop -> iteration + 1 }}</span>
                                                            <span class="mt-2">{{ $upload -> getFormName($item -> checklist_form_id) }}</span>
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </td>
                                            <td><input type="number" min="1" max="{{ count($items) + 1 }}" class="custom-form-element form-input numbers-only checklist-order" data-label="Order" style="width: 100px" value="1"></td>
                                            <td><a href="/doc_management/checklists?checklist_id={{ $checklist -> id }}&checklist_location_id={{ $checklist -> checklist_location_id}}&checklist_type={{ $checklist -> checklist_type }}" class="btn btn-sm btn-primary" target="_blank">View Checklist</a></td>
                                            <td>
                                                <div>
                                                    <a class="btn btn-sm btn-primary show-checklist-items-collapsible" data-toggle="collapse" href="#items_{{ $loop -> iteration }}" aria-expanded="false" aria-controls="items_{{ $loop -> iteration }}">Show Checklist Items</a>
                                                </div>

                                            </td>
                                            <td>{{ $checklist -> checklist_state }}</td>
                                            <td>{{ $resource_items -> getLocation($checklist -> checklist_location_id) }}</td>
                                            <td>{{ ucwords($checklist -> checklist_type) }}</td>
                                            <td>{{ ucwords($checklist -> checklist_represent) }}</td>
                                            <td>{{ ucwords($checklist -> checklist_sale_rent) }}</td>
                                            <td>{{ $resource_items -> getTagName($checklist -> checklist_property_type_id) }}</td>
                                            <td>{{ $resource_items -> getTagName($checklist -> checklist_property_sub_type_id) }}</td>
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
