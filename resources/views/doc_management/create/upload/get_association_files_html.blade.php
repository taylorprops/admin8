@foreach ($files as $file)
<div class="border-bottom border-primary p-1 mb-4">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <div class="h5 text-secondary">{{ $file -> file_name_display }}</div>
                    <div class="small">Added: {{ date('M jS, Y g:i:sA', strtotime($file -> created_at)) }}</div>
                </div>
            </div>
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    @if($file -> published == 'yes')
                    <span class="badge badge-secondary">Published</span>
                    <a href="javascript:void(0)" class="edit-upload text-primary" data-id="{{ $file -> file_id }}"><i class="fad fa-edit mr-2"></i> Edit Details</a>
                    <a href="javascript:void(0)" class="duplicate-upload text-primary" data-id="{{ $file -> file_id }}" data-state="{{ $state }}" data-association-id="{{ $association_id }}"><i class="fad fa-clone mr-2"></i> Duplicate Form</a>
                    @else
                    <a href="/doc_management/create/add_fields/{{ $file -> file_id }}" class="text-primary"><i class="fal fa-plus mr-2"></i> Add Fields</a>
                    <a href="javascript:void(0)" class="edit-upload text-primary" data-id="{{ $file -> file_id }}"><i class="fad fa-edit mr-2"></i> Edit Details</a>
                    <a href="javascript:void(0)" class="duplicate-upload text-primary" data-id="{{ $file -> file_id }}" data-state="{{ $state }}" data-association-id="{{ $association_id }}"><i class="fad fa-clone mr-2"></i> Duplicate Form</a>
                    <a href="javascript:void(0)" class="publish-upload text-success" data-id="{{ $file -> file_id }}"><i class="fad fa-file-export mr-2"></i> Publish Form</a>
                    <a href="javascript:void(0)" class="delete-upload text-danger" data-id="{{ $file -> file_id }}" data-state="{{ $state }}" data-association-id="{{ $association_id }}"><i class="fad fa-trash-alt mr-2"></i> Delete Form</a>
                    @endif
                </div>
            </div>
        </div><!-- ./ .row -->
    </div><!-- ./ .container -->
</div>
@endforeach
<input type="hidden" id="files_count" value="{{ $files_count }}">
