<div class="container-fluid manage-form-container">
    <div class="row">
        <div class="col-12">
            <h4 class="text-orange mt-2 mb-4">{{ $uploaded_file -> file_name_display }}</h4>
            <div class="row">
                <div class="col-2">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="text-primary mb-3">Add To Checklists <i class="fad fa-question-circle ml-2" data-toggle="popover" title="Add To Checklists" data-content="Add this form to multiple checklists"></i></h4>
                    <button type="button" class="btn btn-primary btn-lg btn-block mt-4" id="add_to_checklists_button"><i class="fal fa-plus fa-lg mr-2"></i> Add</button>
                        </div>
                        @if(count($checklists) > 0)
                        <div class="col-12">
                            <h4 class="text-primary">Remove From Checklists <i class="fad fa-question-circle ml-2 mt-5" data-toggle="popover" title="Remove Form" data-content="Remove this form from all checklists"></i></h4>
                    <button type="button" class="btn btn-danger btn-lg btn-block mt-4" id="remove_from_checklist_button"><i class="fal fa-ban fa-lg mr-2"></i> Remove</button>
                        </div>
                        @endif
                    </div>
                </div>
                @if(count($checklists) > 0)
                <div class="col-5">
                    <div class="ml-2">
                        <h4 class="text-primary mb-3">Replace Form in Checklists <i class="fad fa-question-circle ml-2" data-toggle="popover"data-html="true"  title="Replace Form" data-content="Replace this form with another. This will replace the form in all checklists. <br><br>Only forms that have NOT been added to checklists are available to select from"></i></h4>
                        <h5 class="text-primary">Select form to replace  - <span class="text-orange">{{ $uploaded_file -> file_name_display }}</span></h5>
                        <div class="border-bottom border-top">
                            <div class="manage-form-options" data-simplebar data-simplebar-auto-hide="false">
                                <ul class="list-group">
                                    @foreach($uploads as $upload)
                                    <li class="list-group-item list-group-item-action" title="{{ $upload -> file_name_display }}">
                                        <button type="button" class="btn btn-sm btn-primary select-form-button" data-form-id="{{ $upload -> file_id }}" data-form-name="{{ $upload -> file_name_display }}" @if($upload -> file_id == $file_id) disabled @endif>Select</button>
                                        {{ shorten_text($upload -> file_name_display, 80) }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if(count($checklists) > 0)
                <div class="col-5">
                    <h4 class="text-primary mb-3">Current Checklists</h4>
                    <div class="border">
                        <div class="manage-current-checklists" data-simplebar data-simplebar-auto-hide="false">
                            <table class="table table-hover table-sm">
                                @foreach($checklists as $checklist)
                                <tr>
                                <td><a href="/doc_management/checklists?checklist_id={{ $checklist -> id }}&checklist_location_id={{ $checklist -> checklist_location_id}}&checklist_type={{ $checklist -> checklist_type }}" class="btn btn-sm btn-primary" target="_blank">View</a></td>
                                    <td>{{ $checklist -> checklist_state }}</td>
                                    <td>{{ $resource_items -> getLocation($checklist -> checklist_location_id) }}</td>
                                    <td>{{ ucwords($checklist -> checklist_type) }}</td>
                                    <td>{{ ucwords($checklist -> checklist_represent) }}</td>
                                    <td>{{ ucwords($checklist -> checklist_sale_rent) }}</td>
                                    <td>{{ $resource_items -> getTagName($checklist -> checklist_property_type_id) }}</td>
                                    <td>{{ $resource_items -> getTagName($checklist -> checklist_property_sub_type_id) }}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div><!-- ./ .row -->
</div><!-- ./ .container -->
<input type="hidden" id="manage_form_id" value="{{ $file_id }}">
<input type="hidden" id="manage_form_name" value="{{ $uploaded_file -> file_name_display }}">
<input type="hidden" id="manage_form_state" value="{{ $uploaded_file -> state }}">
