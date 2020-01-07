@foreach ($files as $file)
<div class="border-bottom border-primary p-1 mb-4 uploads-list @if($file -> published == 'yes') published @else notpublished @endif">
    <div class="container">
        <div class="row">
            <div class="col-7">
                <div class="h5 text-secondary">{{ $file -> file_name_display }}</div>
            </div>
            <div class="col-5">
                <div class="d-flex justify-content-end">
                    @php $tags = explode(',', $file -> sale_type); @endphp
                    @foreach($tags as $tag)
                    <span class="badge mr-2" style="background-color: {{ $resource_items -> getTagColor($tag) }}">{{ $resource_items -> getTagName($tag) }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 options-holder">
                <div class="row">
                    <div class="col">
                        @if($file -> published == 'no')
                        <a href="/doc_management/create/add_fields/{{ $file -> file_id }}" class="text-primary"><i class="fal fa-plus mr-2"></i> Add/Edit Fields</a>
                        @endif
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" class="edit-upload text-primary" data-id="{{ $file -> file_id }}"><i class="fad fa-edit mr-2"></i> Edit Details</a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" class="duplicate-upload text-primary" data-id="{{ $file -> file_id }}" data-state="{{ $state }}" data-form-group-id="{{ $form_group_id }}"><i class="fad fa-clone mr-2"></i> Duplicate</a>
                    </div>
                    <div class="col">
                        @if($file -> published == 'no')
                        <a href="javascript:void(0)" class="publish-upload text-success" data-id="{{ $file -> file_id }}" data-state="{{ $state }}" data-form-group-id="{{ $form_group_id }}"><i class="fad fa-file-export mr-2"></i> Publish</a>
                        @else
                        <a href="javascript: void(0" class="material-tooltip-main mr-5" data-toggle="tooltip" title="This form can no longer be edited or deleted"><span class="badge badge-success">Published</span></a>
                        @endif
                    </div>
                    <div class="col">
                        @if($file -> published == 'no')
                        <a href="javascript:void(0)" class="delete-upload text-danger" data-id="{{ $file -> file_id }}" data-state="{{ $state }}" data-form-group-id="{{ $form_group_id }}"><i class="fad fa-trash-alt mr-2"></i> Delete</a>
                        @endif
                    </div>
                    <div class="col">
                        <div class="small">Added: {{ date('M jS, Y', strtotime($file -> created_at)) }}</div>
                    </div>
                </div>
            </div>
        </div><!-- ./ .row -->
    </div><!-- ./ .container -->
</div>
@endforeach
<input type="hidden" id="files_count" value="{{ $files_count }}">
