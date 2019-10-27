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
            <div class="float-right text-center mr-4 zoom-container">Zoom<input type="range" class="form-control-range" id="zoom_control" min="50" max="100" value="100"></div>
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
<input type="hidden" id="document_id" value="{{ $file['id'] }}">
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

@endsection