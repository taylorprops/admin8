@foreach ($files as $file)
@php
$checklist_count = $checklists -> countInChecklist($file -> file_id);
$show_title = false;
@endphp
<div class="p-2 mb-4 uploads-list @if($file -> published == 'yes') published @else notpublished @endif @if($file -> active == 'yes') active @else notactive @endif">
    <div class="container">
        <div class="row">
            <div class="col-8">
                <div class="h5 text-secondary" @if($show_title) title="{{ $file -> file_name_display }}" @endif>@if($file -> file_location != '') <i class="fad fa-file-plus mr-2 text-success"></i> @else <i class="fad fa-file-minus mr-2 text-gray"></i> @endif {{ shorten_text($file -> file_name_display, 65) }}</div>
            </div>
            <div class="col-4">
                <div class="d-flex justify-content-end">
                    @php $tags = explode(',', $file -> sale_type); @endphp
                    @foreach($tags as $tag)
                    <span class="badge badge-pill text-white ml-1" style="background-color: {{ $resource_items -> getTagColor($tag) }}">{{ $resource_items -> getResourceName($tag) }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-10 options-holder">
                <div class="d-flex justify-content-start">
                    <div>
                        @if($file -> published == 'no')
                            @if($file -> file_location != '')
                                <a href="/doc_management/create/add_fields/{{ $file -> file_id }}" class="btn btn-sm btn-primary ml-0" data-toggle="tooltip" data-html="true" title="Add fields to the form" target="_blank"><i class="fal fa-plus mr-2"></i> Add/Edit Fields</a>
                            @endif
                        @else
                            <span class="chip @if($file -> active == 'yes') green @else red @endif text-white" data-toggle="tooltip" data-html="true" title="Fields for this form can no longer be edited and the from can no longer be deleted"><i class="fad @if($file -> active == 'yes') fa-check @else fa-ban @endif mr-2"></i> Published</span>
                        @endif
                    </div>
                    @if($file -> published == 'yes')
                        <div>
                            <div class="chip @if($checklist_count > 0) blue text-white @else blue-light text-orange @endif checklist-count-chip" data-toggle="tooltip" data-html="true" title="Found in {{ $checklist_count }} checklists">
                                {{ $checklist_count }}
                            </div>
                        </div>
                        <div>
                            @if($file -> active == 'yes')
                                <span data-toggle="tooltip" data-html="true" @if($checklist_count > 0) title="You can only deactivate a form that is not in any checklists. It must first be removed from all checklists" @else title="Once deactivated you can no longer add the form to checklists" @endif>
                                    <button type="button" class="activate-upload btn btn-sm btn-danger" data-id="{{ $file -> file_id }}" data-active="no" data-state="{{ $state }}" data-form-group-id="{{ $form_group_id }}" @if($checklist_count > 0) disabled @endif><i class="fad fa-toggle-on mr-2"></i> Deactivate</button>
                                </span>
                            @else
                                <button class="activate-upload btn btn-sm btn-success" data-id="{{ $file -> file_id }}" data-active="yes" data-state="{{ $state }}" data-form-group-id="{{ $form_group_id }}" data-toggle="tooltip" data-html="true" title="Reactivate form"><i class="fad fa-toggle-off mr-2"></i> Activate</button>
                            @endif
                        </div>
                        @if($file -> active == 'yes')
                            <div>
                                <span data-toggle="tooltip" data-html="true" title="Manage this form and its checklist relations">
                                    <button class="manage-upload btn btn-sm btn-primary" data-id="{{ $file -> file_id }}" data-form-group-id="{{ $form_group_id }}"><i class="fad fa-bars mr-2"></i> Manage Form</button>
                                </span>
                            </div>
                        @endif
                    @endif
                    <div>
                        <button class="edit-upload btn btn-sm btn-primary" data-id="{{ $file -> file_id }}" data-toggle="tooltip" data-html="true" title="Edit form details"><i class="fad fa-edit mr-2"></i> Edit</button>
                    </div>
                    <div>
                        <button class="duplicate-upload btn btn-sm btn-primary" data-id="{{ $file -> file_id }}" data-state="{{ $state }}" data-form-group-id="{{ $form_group_id }}"  data-toggle="tooltip" data-html="true" title="Create a duplicate of the file including all added fields"><i class="fad fa-clone mr-2"></i> Duplicate</button>
                    </div>
                    @if($file -> published == 'no')
                        <div>
                            <button class="publish-upload btn btn-sm btn-success" data-id="{{ $file -> file_id }}" data-state="{{ $state }}" data-form-group-id="{{ $form_group_id }}"  data-toggle="tooltip" data-html="true" title="Once published you can add the form to checklists. It will also be available for agents to access. It cannot be unpublished!"><i class="fad fa-file-export mr-2"></i> Publish</button>
                        </div>
                        <div>
                            <button class="delete-upload btn btn-sm btn-danger" data-id="{{ $file -> file_id }}" data-state="{{ $state }}" data-form-group-id="{{ $form_group_id }}"  data-toggle="tooltip" data-html="true" title="Permantly delete form"><i class="fad fa-trash-alt mr-2"></i> Delete</button>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-2">
                <div>
                    <div class="small text-right">Added: {{ date('M jS, Y', strtotime($file -> created_at)) }}<br>{{ date('g:i A', strtotime($file -> created_at)) }}</div>
                </div>
            </div>
        </div><!-- ./ .row -->
    </div><!-- ./ .container -->
</div>
@endforeach
<input type="hidden" class="files-count" value="{{ $files_count }}">
<input type="hidden" class="form-group-state" value="{{ $state }}">
<input type="hidden" class="form-group-id" value="{{ $form_group_id }}">
