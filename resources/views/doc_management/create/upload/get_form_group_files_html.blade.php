@foreach ($files as $file)
<div class="p-2 mb-4 uploads-list @if($file -> published == 'yes') published @else notpublished @endif">
    <div class="container">
        <div class="row">
            <div class="col-8">
                <div class="h5 text-secondary">{{ $file -> file_name_display }}</div>
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
                        <a href="/doc_management/create/add_fields/{{ $file -> file_id }}" class="btn btn-sm btn-primary ml-0"><i class="fal fa-plus mr-2"></i> Add/Edit Fields</a>
                        @else
                        <a href="javascript: void(0" class="material-tooltip-main" data-toggle="tooltip" title="This form can no longer be edited or deleted"><span class="chip green text-white"><i class="fad fa-check mr-2"></i> Published</span></a>
                        @endif
                    </div>
                    <div>
                        <a href="javascript:void(0)" class="edit-upload btn btn-sm btn-primary" data-id="{{ $file -> file_id }}"><i class="fad fa-edit mr-2"></i> Edit Details</a>
                    </div>
                    <div>
                        <a href="javascript:void(0)" class="duplicate-upload btn btn-sm btn-primary" data-id="{{ $file -> file_id }}" data-state="{{ $state }}" data-form-group-id="{{ $form_group_id }}"><i class="fad fa-clone mr-2"></i> Duplicate</a>
                    </div>
                    <div>
                        @if($file -> published == 'no')
                        <a href="javascript:void(0)" class="publish-upload btn btn-sm btn-success" data-id="{{ $file -> file_id }}" data-state="{{ $state }}" data-form-group-id="{{ $form_group_id }}"><i class="fad fa-file-export mr-2"></i> Publish</a>
                        @endif
                    </div>
                    <div>
                        @if($file -> published == 'no')
                        <a href="javascript:void(0)" class="delete-upload btn btn-sm btn-danger" data-id="{{ $file -> file_id }}" data-state="{{ $state }}" data-form-group-id="{{ $form_group_id }}"><i class="fad fa-trash-alt mr-2"></i> Delete</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-2">
                <div>
                    <div class="small mt-3">Added: {{ date('M jS, Y', strtotime($file -> created_at)) }}</div>
                </div>
            </div>
        </div><!-- ./ .row -->
    </div><!-- ./ .container -->
</div>
@endforeach
<input type="hidden" id="files_count" value="{{ $files_count }}">
