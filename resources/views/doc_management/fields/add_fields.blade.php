@extends('layouts.main')
@section('title', 'Edit File')
@section('content')
<div class="container-fluid page-add-fields file-view-container">
    <div class="row">
        <div class="col-8 ml-2">
            <div class="d-flex flex-row field-container-textline p-2">
                <div class="field-wrapper mr-4" data-type="textline">
                    <div class="text-line-div-feild not-resizable rounded mt-1">
                        <div class="text-line"></div>
                    </div>
                </div>
                <div class="field-wrapper mr-4"><a href="javascript:void(0)" class="btn btn-primary">Check Box</a></div>
                <div><a href="javascript:void(0)" class="btn btn-success" id="save_fields">Save</a></div>
            </div>
        </div>
        <div class="col-2">
            <!--<div class="float-right text-center mr-4 zoom-container">Zoom<input type="range" class="form-control-range" id="zoom_control" min="50" max="100" value="100"></div>-->
        </div>
        <div class="col-2"></div>
    </div>
    <div class="row">

        <?php $total_pages = count($files); ?>

        <div class="col-10">

            <div class="container-fluid">
                <div class="file-viewer-container p-1 mx-auto">
                    <div class="file-view" id="file_viewer">

                        @foreach($files as $file)

                        <?php $c = $file['page_number']; ?>

                        <div class="file-view-page-info bg-primary text-white p-2" id="page_{{ $c }}">
                            Page <?php echo $c.' of '.$total_pages; ?>
                        </div>
                        <div class="file-view-page-container border border-primary w-100 <?php echo($c == 1) ? 'active' : ''; ?>" id="page_div_{{ $c }}" data-id="{{ $c }}">
                            <div class="file-view-image w-100 h-100">
                                <img class="file-image w-100 h-100" src="{{ $file['file_location'] }}">

                                @foreach($fields as $field)

                                    <?php
                                    $common_name = '';
                                    $custom_name = '';
                                    if($field['field_name_type'] == 'common') {
                                        $common_name = $field['field_name'];
                                    } else if($field['field_name_type'] == 'new') {
                                        $custom_name = $field['field_name'];
                                    }
                                    ?>

                                    @if($field['page'] == $c)

                                        @if($field['field_type'] == 'textline')

                                                <div
                                                class="field-div text-line-div standard rounded group_{{ $field['group_id'] }}" style="position: absolute; top: {{ $field['top_perc'] }}%; left: {{ $field['left_perc'] }}%; height: {{ $field['height_perc'] }}%; width: {{ $field['width_perc'] }}%;" id="field_{{ $field['field_id'] }}" data-fieldid="{{ $field['field_id'] }}" data-groupid="{{ $field['group_id'] }}" data-type="textline" data-x="{{ $field['left'] }}" data-y="{{ $field['top'] }}" data-h="{{ $field['height'] }}" data-w="{{ $field['width'] }}" data-xp="{{ $field['left_perc'] }}" data-yp="{{ $field['top_perc'] }}" data-hp="{{ $field['height_perc'] }}" data-wp="{{ $field['width_perc'] }}" data-page="{{ $field['page'] }}">
                                                    <div class="field-options-holder focused shadow container text-center">
                                                        <div class="row m-0 p-0">
                                                            <div class="col-2 p-0">
                                                                <div class="field-handle"><i class="fal fa-ellipsis-v-alt fa-lg text-primary"></i></div>
                                                            </div>
                                                            <div class="col-8 p-0">
                                                                <div class="d-flex justify-content-around">
                                                                    <div class="field-textline-addline-container">
                                                                        <div class="field-textline-addline mr-3">
                                                                            <i class="fas fa-horizontal-rule fa-lg text-primary"></i>
                                                                            <i class="fal fa-plus fa-xs ml-1 text-primary add-line-plus"></i>
                                                                        </div>
                                                                        <div class="add-new-line-div shadow-lg field-popup">
                                                                            <div class="add-new-line-content">
                                                                                Add Line To Group?
                                                                                <div class="d-flex justify-content-around">
                                                                                    <a href="javascript: void(0);" class="btn btn-success btn-sm add-new-line shadow" data-groupid="{{ $field['group_id'] }}">Confirm</a>
                                                                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm field-close-newline">Cancel</a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <div class="field-properties">
                                                                            <i class="fal fa-info-circle fa-lg text-primary"></i>
                                                                        </div>
                                                                        <div class="edit-properties-div shadow field-popup">
                                                                            <div class="form-div">
                                                                                <strong>Field Name</strong><br>
                                                                                <div class="container">
                                                                                    <div class="row">
                                                                                        <div class="col-6">
                                                                                            Common Field<br>
                                                                                            <select class="custom-select field-data-name" data-fieldtype="common" data-defaultvalue="{{ $common_name }}">
                                                                                                <option value=""></option>
                                                                                                @foreach($common_fields as $common_field)
                                                                                                <option value="{{ $common_field['field_name'] }}"
                                                                                                @if($common_field['field_name'] == $common_name) selected @endif
                                                                                                >
                                                                                                {{ $common_field['field_name'] }}
                                                                                                </option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                        <div class="col-1">
                                                                                            <div class="small">Or</div>
                                                                                        </div>
                                                                                        <div class="col-5">
                                                                                            Add New Name<br>
                                                                                            <input type="text" class="form-control field-data-name" data-fieldtype="new" value="{{ $custom_name }}" data-defaultvalue="{{ $custom_name }}">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="d-flex justify-content-around mt-3">
                                                                                    <a href="javascript: void(0);" class="btn btn-success btn-sm shadow save_field_properties_textline" data-groupid="{{ $field['group_id'] }}">Save</a>
                                                                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm field-close-properties-textline">Cancel</a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-2 p-0">
                                                                <div class="remove-field-textline"><i class="fal fa-times-circle fa-lg text-danger"></i></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="ui-resizable-handle ui-resizable-ne focused"></div>
                                                    <div class="ui-resizable-handle ui-resizable-se focused"></div>
                                                    <div class="ui-resizable-handle ui-resizable-nw focused"></div>
                                                    <div class="ui-resizable-handle ui-resizable-sw focused"></div>
                                                    <div class="text-line"></div>
                                                </div>

                                        @endif

                                    @endif

                                @endforeach

                            </div>
                        </div>
                        <div class="file-view-page-info bg-primary text-white p-2 mb-4">
                            Page {{ $c.' of '.$total_pages }}
                        </div>

                        @endforeach

                    </div>
                </div>

            </div>
        </div>
        <div class="col-2">
            <div class="file-view p-2" id="thumb_viewer">
                @foreach($files as $file)
                <?php $c = $file['page_number']; ?>
                <div class="file-view-thumb-container w-75 mx-auto <?php echo($c == 1) ? 'active' : ''; ?>" id="thumb_{{ $c }}" data-id="{{ $c }}">
                    <div class="file-view-thumb">
                        <a href="javascript: void(0)"><img class="file-thumb w-100 h-100" src="{{ $file['file_location'] }}"></a>
                    </div>
                    <div class="file-view-thumb-footer text-center mb-4">
                        Page {{ $c }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div><!-- ./ .container -->
<input type="hidden" id="file_id" value="{{ $file['file_id'] }}">
<input type="hidden" id="active_page" value="1">
<input type="hidden" id="active_field">
<input type="hidden" id="field_textline_height" value="30">
<input type="hidden" id="field_textline_width" value="200">
<input type="hidden" id="field_textline_x">
<input type="hidden" id="field_textline_y">
<input type="hidden" id="field_textline_heightp" value="30">
<input type="hidden" id="field_textline_widthp" value="200">
<input type="hidden" id="field_textline_xp">
<input type="hidden" id="field_textline_yp">
<input type="hidden" id="field_textline_groupid">
<input type="hidden" id="name_select_options">

@endsection