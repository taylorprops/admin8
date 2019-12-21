@extends('layouts.main')
@section('title', 'Edit File')
@section('content')

<div class="container-fluid page-add-fields file-view-container p-0">
    <div class="container-fluid">
        <div class="row bg-blue-light">
            <div class="col-12 ml-1">
                <div class="d-flex justify-content-center field-select-container">
                    <div class="field-wrapper pt-3 px-3" data-type="textline">
                        <div class="textline-div-field rounded my-auto">
                            <div class="textline-html text-white ml-3 pt-1"><i class="fal fa-text fa-lg mr-2"></i> General Text</div>
                        </div>
                    </div>
                    <div class="field-wrapper pt-3 px-3" data-type="name">
                        <div class="textline-div-field rounded my-auto">
                            <div class="textline-html text-white ml-3 pt-1"><i class="fal fa-user-alt fa-lg mr-2"></i> Name</div>
                        </div>
                    </div>
                    <div class="field-wrapper pt-3 px-3" data-type="address">
                        <div class="textline-div-field rounded my-auto">
                            <div class="textline-html text-white ml-3 pt-1"><i class="fal fa-map-marker-alt fa-lg mr-2"></i> Address</div>
                        </div>
                    </div>
                    <div class="field-wrapper pt-3 px-3" data-type="date">
                        <div class="textline-div-field rounded my-auto">
                            <div class="textline-html text-white ml-3 pt-1"><i class="fal fa-calendar-alt fa-lg mr-2"></i> Date</div>
                        </div>
                    </div>
                    <div class="field-wrapper pt-3 px-3" data-type="number">
                        <div class="textline-div-field rounded my-auto">
                            <div class="textline-html text-white ml-3 pt-1"><span class="text-white mr-2">0-9</span> Number</div>
                        </div>
                    </div>
                    <div class="field-wrapper pt-2 px-3 d-flex justify-content-center" data-type="checkbox">
                        <div class="checkbox-div-field my-auto"></div>
                        <div class="my-auto ml-1 text-primary"> Check Box</div>
                    </div>
                    <div class="field-wrapper pt-2 px-3 d-flex justify-content-center" data-type="radio">
                        <div class="radio-div-field my-auto"></div>
                        <div class="my-auto ml-1 text-primary"> Radio Buttons</div>
                    </div>
                    <div class="mr-1 pl-2">
                        <div><a href="javascript:void(0)" class="btn btn-success" id="save_add_fields">Save</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">

            <?php $total_pages = count($images); ?>
            <div class="col-2 p-0">

                <div class="field-list-container file-view p-1"></div>
            </div>
            <div class="col-8 p-0">

                <div class="container-fluid p-0">
                    <div class="file-viewer-container mx-auto">
                        <div class="file-view" id="file_viewer">

                            @foreach($images as $image)

                                <?php $c = $image['page_number']; ?>

                                <div class="file-view-page-info bg-primary text-white p-2" id="page_{{ $c }}">
                                    Page <?php echo $c.' of '.$total_pages; ?>
                                </div>
                                <div class="file-view-page-container border border-primary w-100 <?php echo($c == 1) ? 'active' : ''; ?>" id="page_div_{{ $c }}" data-id="{{ $c }}">
                                    <div class="field-container w-100 h-100">
                                        <img class="w-100 h-100 file-image" src="{{ $image['file_location'] }}">

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
            <div class="col-2 p-0">

                <div class="file-view" id="thumb_viewer">
                    <div class="h3 text-white bg-primary-dark p-2"><i class="fad fa-send-backward mr-3"></i> Pages</div>
                    @foreach($images as $image)
                        <?php $c = $image['page_number']; ?>
                        <div class="file-view-thumb-container  w-50 mx-auto" <?php echo($c == 1) ? 'active' : ''; ?>" id="thumb_{{ $c }}" data-id="{{ $c }}">
                            <div class="file-view-thumb">
                                <a href="javascript: void(0)"><img class="file-thumb w-100 h-100" src="{{ $image['file_location'] }}"></a>
                            </div>
                            <div class="file-view-thumb-footer text-center mb-1">
                                Page {{ $c }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div><!-- ./ .container -->

<input type="hidden" id="file_id" value="{{ $file[0]['file_id'] }}">
<input type="hidden" id="active_page" value="1">
<input type="hidden" id="active_field">

@foreach($field_types as $fields)
    @php

    $field = $fields['field_type'];
    $heightp = '1.1';
    $widthp = '15';
    if($field == 'date') {
        $widthp = '10';
    } else if($field == 'checkbox' || $field == 'radio') {
        $heightp = '1.1';
        $widthp = '1.45';
    }
    @endphp
    <input type="hidden" id="field_{{$field}}_height">
    <input type="hidden" id="field_{{$field}}_width">
    <input type="hidden" id="field_{{$field}}_x">
    <input type="hidden" id="field_{{$field}}_y">
    <input type="hidden" id="field_{{$field}}_heightp" value="{{$heightp}}">
    <input type="hidden" id="field_{{$field}}_widthp" value="{{$widthp}}">
    <input type="hidden" id="field_{{$field}}_xp">
    <input type="hidden" id="field_{{$field}}_yp">
    <input type="hidden" id="field_{{$field}}_group-id">
@endforeach

@foreach($field_types as $fields)
    @php $field = $fields['field_type'] @endphp
    <input type="hidden" id="{{$field}}_select_options">
@endforeach

<input type="hidden" id="inputs_html">

@endsection
