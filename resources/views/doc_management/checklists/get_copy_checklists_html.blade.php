<div class="container-fluid">
    <div class="row">
        <div class="col-6">
            <h5 class="text-orange">Select which <span class="font-weight-bold text-uppercase">{{ $checklist_type }}</span> checklists to export</h5>
            <div class="checklists-to-export-div" data-simplebar data-simplebar-auto-hide="false">
                @foreach($property_types as $property_type)
                <h5 class="text-primary mt-3">{{ $property_type -> resource_name }}</h5>
                    <div class="list-group">
                        @php
                        $property_type_checklists = $checklists_functions -> getChecklistsByPropertyType($property_type -> resource_id, $location_id, $checklist_type);
                        @endphp
                        @foreach($property_type_checklists as $checklist)
                            <div class="list-group-item list-group-item-action pt-1 pb-0 px-0">
                                <div class="row">
                                    <div class="col-2">
                                        <input type="checkbox" class="custom-form-element form-checkbox checklists-to-export-id" value="{{ $checklist -> id }}" data-label="" checked>
                                    </div>
                                    <div class="col-2">
                                        <span class="text-primary-dark font-weight-bold ml-2">
                                            {{ ucwords($checklist -> checklist_sale_rent) }}
                                        </span>
                                    </div>
                                    <div class="col-3">
                                        <span class="text-primary">
                                            Client: <span class="font-weight-bold">{{ ucwords($checklist -> checklist_represent) }}</span>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <span class="text-orange">
                                            @if($checklist -> checklist_property_sub_type_id > 0)
                                            {{ $resource_items -> getTagName($checklist -> checklist_property_sub_type_id) }}
                                            @else
                                            Standard
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-6">
            <h5 class="text-orange">Select which regions to export to</h5>
            <div class="export-to-form-groups-div" data-simplebar data-simplebar-auto-hide="false">

                <div class="row list-group-columns bg-white sticky-top mb-1 h5 text-primary">
                    <div class="col-1"></div>
                    <div class="col-3">Region</div>
                    <div class="col">Requires Addenda</div>
                </div>
                <div class="list-group">
                    @foreach($form_groups as $form_group)
                        <div class="list-group-item pt-1 pb-0 px-0">
                            <div class="row">
                                <div class="col-2">
                                    <input type="checkbox" class="custom-form-element form-checkbox export-to-form-group" value="{{ $form_group -> resource_id }}">
                                </div>
                                <div class="col-3">
                                    <span class="">{{ $form_group -> resource_name }}</span>
                                </div>
                                <div class="col addendums_{{ $form_group -> resource_addendums }}">{{ ucwords($form_group -> resource_addendums) }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div><!-- ./ .row -->
</div><!-- ./ .container -->
