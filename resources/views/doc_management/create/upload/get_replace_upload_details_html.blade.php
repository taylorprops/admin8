<div class="container">
    <div class="row">
        <div class="col-12">
            <h4 class="text-orange mt-2 mb-4">{{ $upload -> file_name_display }}</h4>
            <div class="row">
                <div class="col-6">
                    <h5 class="text-primary">Current Checklists</h5>
                    <div class="replace-current-checklists" data-simplebar data-simplebar-auto-hide="false">
                        @foreach($checklists as $checklist)
                        <div class="current-checklist-div">
                            {{ $checklist -> checklist_state }} | {{ $resource_items -> getLocation($checklist -> checklist_location_id) }} | {{ ucwords($checklist -> checklist_type) }} | Rep: {{ $checklist -> checklist_represent }} | {{ ucwords($checklist -> checklist_sale_rent) }} | {{ $checklist -> checklist_state }}
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="6">
                    <h5 class="text-primary">Select the Replacement</h5>
                    <div class="replace-form-options" data-simplebar data-simplebar-auto-hide="false">

                    </div>
                </div>
            </div>
        </div>
    </div><!-- ./ .row -->
</div><!-- ./ .container -->
