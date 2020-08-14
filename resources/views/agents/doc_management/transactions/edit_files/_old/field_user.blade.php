<?php

$field_type = $field_user -> field_type;
// $class = 'fillable-field-container';
// $class .= ' standard ' . $field_type;

$custom_name = 'User Field';

$top = $field_user -> top_perc;
$left = $field_user -> left_perc;
$height = $field_user -> height_perc;
$width = $field_user -> width_perc;

$data_div_classes = $field_type;
$data_div_styles = '';
$field_div_class = '';
if($field_type == 'user_text') {
    $field_div_class = 'user-field-div textline-div';
    $data_div_classes .= ' w-100 h-100';
    $data_div_styles = 'font-family: Helvetica, Arial, sans-serif; letter-spacing:0.03em;';
} else if($field_type == 'strikeout') {
    $field_div_class = 'user-field-div strikeout-div ';
} else if($field_type == 'highlight') {
    $field_div_class = 'user-field-div highlight-div ';
    $data_div_classes .= ' w-100 h-100';
}


?>

<div class="field-div {{ $field_div_class }} group_{{ $field_user -> group_id }}"
    style="position: absolute;
    top: {{ $top }}%;
    left: {{ $left }}%;
    height: {{ $height + ($height * .1) }}%;
    width: {{ $width }}%;
    {{ $data_div_styles }}"
    id="field_{{ $field_user -> field_id }}"
    data-field-id="{{ $field_user -> field_id }}"
    data-group-id="{{ $field_user -> group_id }}"
    data-type="{{ $field_type }}"
    data-address-type="{{ $field_user -> address_type }}"
    data-number-type="{{ $field_user -> number_type }}"
    data-page="{{ $field_user -> page }}"
    data-x="{{ $field_user -> left }}"
    data-y="{{ $field_user -> top }}"
    data-h="{{ $field_user -> height }}"
    data-w="{{ $field_user -> width }}"
    data-xp="{{ $field_user -> left_perc }}"
    data-yp="{{ $field_user -> top_perc }}"
    data-hp="{{ $field_user -> height_perc }}"
    data-wp="{{ $field_user -> width_perc }}"
    data-field-name-type="{{ $field_user -> field_name_type }}"
    data-commonname=""
    data-customname="{{ $custom_name }}">

    <div class="data-div {{ $data_div_classes }}" style="{{ $data_div_styles }}"></div>

    @if($field_type == 'user_text')
        <div class="modal fade" id="fill_fields_div_modal_{{$field_user -> field_id}}" tabindex="-1" role="dialog" aria-labelledby="fill_fields_div_modal_{{$field_user -> field_id}}_title" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="fill_fields_div_modal_{{$field_user -> field_id}}_title">{{ strtoupper($field_user -> field_name_display) }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="text-white">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="container form-div">

                            @php
                            $input_id = $field_user -> field_id;
                            $value = get_value($field_values, $input_id);
                            $textline_class = $field_user -> textline_type ?? null;
                            @endphp

                            <div class="row p-3 mb-1">
                                <div class="col-12">
                                    <div class="my-3">
                                        <textarea class="custom-form-element form-textarea fillable-field-input {{ $textline_class }}" id="{{ $input_id }}" data-label="{{ $field_user -> field_name_display }}" rows="3" data-default-value="{{ $value }}" data-common-name="">{{  $value }}</textarea>
                                    </div>
                                </div>
                            </div>

                        </div> <!-- end container form-div -->

                    </div> <!-- end modal-body -->
                    <div class="modal-footer">
                        <a href="javascript: void(0);" class="btn btn-success btn-sm shadow save-fillable-fields" data-group-id="{{ $field_user -> group_id }}" data-type="{{ $field_type }}" data-field-name-type="{{ $field_user -> field_name_type }}">Save</a>
                        <a href="javascript:void(0);" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</a>
                    </div>

                </div> <!-- end modal-content -->

            </div> <!-- end modal-dialog -->

        </div> <!-- end modal -->
    @endif

</div> <!-- end field-div -->
