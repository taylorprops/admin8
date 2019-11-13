@extends('layouts.main')
@section('title', 'Edit File')
@section('content')

<div class="container-fluid page-add-fields file-view-container p-0">
    <div class="container-fluid">
        <div class="row bg-default">
            <div class="col-11 pl-5">
                <div class="d-flex flex-row field-select-container p-2">
                    <div class="field-wrapper mr-4" data-type="textline">
                        <div class="textline-div-field rounded mt-1">
                            <div class="textline-html text-white ml-3 pt-1"><i class="fal fa-text fa-lg mr-2"></i> General Text</div>
                        </div>
                    </div>
                    <div class="field-wrapper mr-4" data-type="name">
                        <div class="textline-div-field rounded mt-1">
                            <div class="textline-html text-white ml-3 pt-1"><i class="fal fa-user-alt fa-lg mr-2"></i> Name</div>
                        </div>
                    </div>
                    <div class="field-wrapper mr-4" data-type="address">
                        <div class="textline-div-field rounded mt-1">
                            <div class="textline-html text-white ml-3 pt-1"><i class="fal fa-map-marker-alt fa-lg mr-2"></i> Address</div>
                        </div>
                    </div>
                    <div class="field-wrapper mr-4" data-type="date">
                        <div class="textline-div-field rounded mt-1">
                            <div class="textline-html text-white ml-3 pt-1"><i class="fal fa-calendar-alt fa-lg mr-2"></i> Date</div>
                        </div>
                    </div>
                    <div class="field-wrapper mr-4" data-type="number">
                        <div class="textline-div-field rounded mt-1">
                            <div class="textline-html text-white ml-3 pt-1"><span class="text-white mr-2">0-9</span> Number</div>
                        </div>
                    </div>
                    <div class="field-wrapper mr-4 d-flex justify-content-center" data-type="checkbox">
                        <div class="checkbox-div-field mt-2"></div>
                        <div class="mt-2 ml-1 text-primary"> Check Box</div>
                    </div>
                    <div class="field-wrapper mr-4 d-flex justify-content-center" data-type="radio">
                        <div class="radio-div-field mt-2"></div>
                        <div class="mt-2 ml-1 text-primary"> Radio Buttons</div>
                    </div>
                </div>
            </div>
            <div class="col-1 mt-3">
                <div><a href="javascript:void(0)" class="btn btn-success" id="save_fields">Save</a></div>
                <!--<div class="float-right text-center mr-4 zoom-container">Zoom<input type="range" class="form-control-range" id="zoom_control" min="50" max="100" value="100"></div>-->
            </div>
        </div>
    </div>
    <div class="container-90">
        <div class="row">

            <?php $total_pages = count($files); ?>

            <div class="col-11 pr-0">

                <div class="container-fluid p-0">
                    <div class="file-viewer-container mx-auto">
                        <div class="file-view" id="file_viewer">

                            @foreach($files as $file)

                                <?php $c = $file['page_number']; ?>

                                <div class="file-view-page-info bg-primary text-white p-2" id="page_{{ $c }}">
                                    Page <?php echo $c.' of '.$total_pages; ?>
                                </div>
                                <div class="file-view-page-container border border-primary w-100 <?php echo($c == 1) ? 'active' : ''; ?>" id="page_div_{{ $c }}" data-id="{{ $c }}">
                                    <div class="field-container w-100 h-100">
                                        <img class="w-100 h-100" src="{{ $file['file_location'] }}">

                                        @foreach($fields as $field)
                                            @if($field['page'] == $c)
                                                @php $type = 'existing'; @endphp
                                                @include('doc_management.create.fields.field', [$field, $common_fields, $c, $type])
                                            @endif
                                        @endforeach

                                    </div> <!-- end field-container -->
                                </div> <!-- end file-view-page-container -->
                                <div class="file-view-page-info bg-primary text-white p-2 mb-4">
                                    Page {{ $c.' of '.$total_pages }}
                                </div>

                            @endforeach

                        </div> <!-- ende file_viewer -->
                    </div> <!-- end file-viewer-container -->

                </div> <!-- end container-fluid p-0 -->
            </div> <!-- col-11 -->
            <div class="col-1 pl-0">
                <div class="file-view p-2" id="thumb_viewer">
                    @foreach($files as $file)
                        <?php $c = $file['page_number']; ?>
                        <div class="file-view-thumb-container w-100 <?php echo($c == 1) ? 'active' : ''; ?>" id="thumb_{{ $c }}" data-id="{{ $c }}">
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
    </div>

</div><!-- ./ .container -->

<input type="hidden" id="file_id" value="{{ $file['file_id'] }}">
<input type="hidden" id="active_page" value="1">
<input type="hidden" id="active_field">

@foreach($field_types as $fields)
    @php

    $field = $fields['field_type'];
    $height = '';
    $width = '';
    if($field == 'textline' || $field == 'address' || $field == 'name' || $field == 'number') {
        $height = '25';
        $width = '200';
    } else if($field == 'date') {
        $height = '25';
        $width = '130';
    } else if($field == 'checkbox' || $field == 'radio') {
        $height = '25';
        $width = '25';
    }
    @endphp
    <input type="hidden" id="field_{{$field}}_height" value="{{$height}}">
    <input type="hidden" id="field_{{$field}}_width" value="{{$width}}">
    <input type="hidden" id="field_{{$field}}_x">
    <input type="hidden" id="field_{{$field}}_y">
    <input type="hidden" id="field_{{$field}}_heightp">
    <input type="hidden" id="field_{{$field}}_widthp">
    <input type="hidden" id="field_{{$field}}_xp">
    <input type="hidden" id="field_{{$field}}_yp">
    <input type="hidden" id="field_{{$field}}_groupid">
@endforeach

@foreach($field_types as $fields)
    @php $field = $fields['field_type'] @endphp
    <input type="hidden" id="{{$field}}_select_options">
@endforeach

@endsection