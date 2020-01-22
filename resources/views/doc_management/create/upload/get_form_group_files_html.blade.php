@foreach ($files as $file)
@php
$checklist_count = $checklists -> inChecklist($file -> file_id);
$file_name = $file -> file_name_display;
$show_title = false;
if(strlen($file -> file_name_display) > 65) {
    $file_name = substr($file -> file_name_display, 0, 65).'...';
    $show_title = true;
}
@endphp
<div class="p-2 mb-4 uploads-list @if($file -> published == 'yes') published @else notpublished @endif @if($file -> active == 'yes') active @else notactive @endif">
    <div class="container">
        <div class="row">
            <div class="col-8">
                <div class="h5 text-secondary" @if($show_title) title="{{ $file -> file_name_display }}" @endif>{{ $file_name }}</div>
            </div>
            <div class="col-4">
                <div class="d-flex justify-content-end">
                    @php $tags = explode(',', $file -> sale_type); @endphp
                    @foreach($tags as $tag)
                    <span class="badge badge-pill text-white ml-1" style="background-color: {{ $resource_items -> getTagColor($tag) }}">{{ $resource_items -> getTagName($tag) }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-10 options-holder">
                <div class="d-flex justify-content-start">
                    <div>
                        @if($file -> published == 'no')
                            <a href="/doc_management/create/add_fields/{{ $file -> file_id }}" class="btn btn-sm btn-primary ml-0" title="Add fields to the form"><i class="fal fa-plus mr-2"></i> Add/Edit Fields</a>
                        @else
                            <a href="javascript: void(0" class="material-tooltip-main" title="This form can no longer be edited or deleted"><span class="chip @if($file -> active == 'yes') green @else red @endif text-white"><i class="fad @if($file -> active == 'yes') fa-check @else fa-ban @endif mr-2"></i> Published</span></a>
                        @endif
                    </div>
                    @if($file -> published == 'yes')
                        <div>
                            <div class="chip blue text-white" title="Found in {{ $checklist_count }} checklists">
                                {{ $checklist_count }}
                            </div>
                        </div>
                        <div>
                            @if($file -> active == 'yes')
                                <span @if($checklist_count > 0) title="You can only deactivate a form that is not in any checklists. It must first be removed from all checklists" @else title="Once deactivated you can no longer add the form to checklists" @endif>
                                    <button type="button" class="activate-upload btn btn-sm btn-danger" data-id="{{ $file -> file_id }}" data-active="no" data-state="{{ $state }}" data-form-group-id="{{ $form_group_id }}" @if($checklist_count > 0) disabled @endif><i class="fad fa-toggle-on mr-2"></i> Deactivate</button>
                                </span>
                            @else
                                <button class="activate-upload btn btn-sm btn-success" data-id="{{ $file -> file_id }}" data-active="yes" data-state="{{ $state }}" data-form-group-id="{{ $form_group_id }}" title="Reactivate form"><i class="fad fa-toggle-off mr-2"></i> Activate</button>
                            @endif
                        </div>
                        @if($file -> active == 'yes')
                            <div>
                                <span @if($checklist_count == 0) title="Form is not in any checklists so it cannot be replaced" @else title="Replace this form with another in checklists" @endif>
                                    <button class="replace-upload btn btn-sm btn-primary" data-id="{{ $file -> file_id }}" @if($checklist_count == 0) disabled @endif><i class="fad fa-retweet-alt mr-2"></i> Replace</button>
                                </span>
                            </div>
                        @endif
                    @endif
                    <div>
                        <button class="edit-upload btn btn-sm btn-primary" data-id="{{ $file -> file_id }}" title="Edit form details"><i class="fad fa-edit mr-2"></i> Edit</button>
                    </div>
                    <div>
                        <button class="duplicate-upload btn btn-sm btn-primary" data-id="{{ $file -> file_id }}" data-state="{{ $state }}" data-form-group-id="{{ $form_group_id }}" title="Create a duplicate of the file including all added fields"><i class="fad fa-clone mr-2"></i> Duplicate</button>
                    </div>
                    @if($file -> published == 'no')
                        <div>
                            <button class="publish-upload btn btn-sm btn-success" data-id="{{ $file -> file_id }}" data-state="{{ $state }}" data-form-group-id="{{ $form_group_id }}" title="Once published you can add the form to checklists. It will also be available for agents to access. It cannot be unpublished!"><i class="fad fa-file-export mr-2"></i> Publish</button>
                        </div>
                        <div>
                            <button class="delete-upload btn btn-sm btn-danger" data-id="{{ $file -> file_id }}" data-state="{{ $state }}" data-form-group-id="{{ $form_group_id }}" title="Permantly delete form"><i class="fad fa-trash-alt mr-2"></i> Delete</button>
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
