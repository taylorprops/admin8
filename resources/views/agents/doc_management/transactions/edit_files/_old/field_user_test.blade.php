<?php

$field_type = $field_user -> field_type;
$field_inputs = $field_user -> field_inputs;
// $class = 'fillable-field-container';
// $class .= ' standard ' . $field_type;

if($field_inputs == 'no') {

    $custom_name = 'User Field';
    $common_name = '';

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

} else {

    $field_div_class = 'fillable-field-container';
    if ($field_type == 'number') {
        // hide written field since automatically filled in after numeric typed in
        if ($field_user -> number_type == 'written') {
            $field_div_class = 'fillable-field-container-hidden';
        }
    }

    $field_div_class .= ' standard ' . $field_type;

    $common_name = '';
    $custom_name = '';

    if ($field_user -> field_name_type == 'common') {
        $common_name = $field_user -> field_name_display;
    } elseif ($field_user -> field_name_type == 'custom') {
        $custom_name = $field_user -> field_name_display;
    }

    $top = $field_user -> top_perc;
    $left = $field_user -> left_perc;
    $height = $field_user -> height_perc;
    $width = $field_user -> width_perc;

    $data_div_classes = '';
    $data_div_styles = 'font-family: Helvetica, Arial, sans-serif;';

    if($field_type == 'radio' || $field_type == 'checkbox') {
        $data_div_classes = 'data-div-radio-check';
        if($field_type == 'checkbox') {
            $data_div_classes = $data_div_classes.' data-div-checkbox';
        }
    } else {
        $data_div_styles = $data_div_styles.' letter-spacing:0.03em;';
    }

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
    data-commonname="{{ $common_name }}"
    data-customname="{{ $custom_name }}">

    <div class="data-div {{ $data_div_classes }}" style="{{ $data_div_styles }}"></div>

    @if($field_type == 'user_text')

        <div class="modal fade" id="fill_fields_div_modal_{{$field_user -> field_id}}" tabindex="-1" role="dialog" aria-labelledby="fill_fields_div_modal_{{$field_user -> field_id}}_title" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
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

    @elseif($field_type == 'date')

        @php
        $input_id = $field_user -> field_id;
        $value = get_value($field_values, $input_id);

        if(!$value) {
            $value = $common_fields -> GetCommonNameValue($common_name, $field_user -> field_id, 'system', $Listing_ID, '', $Agent_ID);
        }
        if($value != '') {
            $value = date('n/j/Y', strtotime($value));
        }
        @endphp
        <input type="text" class="field-datepicker fillable-field-input" id="{{ $input_id }}" value="{{ $value }}" data-default-value="{{ $value }}" data-common-name="{{ $common_name }}">

    @elseif($field_type == 'radio')

        @php
        $input_id = $field_user -> field_id;
        $checked = get_value_radio_checkbox($field_values, $input_id);
        @endphp

        <input type="radio" class="fillable-field-input" value="{{ $field_user -> radio_value }}" id="{{ $input_id }}" {{ $checked }} data-common-name="{{ $common_name }}">

    @elseif($field_type == 'checkbox')

        @php
        $input_id = $field_user -> field_id;
        $checked = get_value_radio_checkbox($field_values, $input_id);
        @endphp

        <input type="checkbox" class="fillable-field-input" value="{{ $field_user -> checkbox_value }}" id="{{ $input_id }}" {{ $checked }} data-common-name="{{ $common_name }}">

    @else

        <div class="modal fade" id="fill_fields_div_modal_{{$field_user -> field_id}}" tabindex="-1" role="dialog" aria-labelledby="fill_fields_div_modal_{{$field_user -> field_id}}_title" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h4 class="modal-title" id="fill_fields_div_modal_{{$field_user -> field_id}}_title">{{ strtoupper($field_user -> field_name_display) }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="text-white">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="container form-div">

                            @if($field_type == 'textline')

                                @php
                                $input_id = $field_user -> field_id;
                                $value = get_value($field_values, $input_id);
                                if(!$value) {
                                    $value = $common_fields -> GetCommonNameValue($common_name, $field_user -> field_id, 'system', $Listing_ID, '', $Agent_ID);
                                }
                                $textline_class = $field_user -> textline_type ?? null;
                                @endphp

                                <div class="row p-3 mb-1">
                                    <div class="col-12">
                                        <div class="my-3">
                                            <textarea class="custom-form-element form-textarea fillable-field-input {{ $textline_class }}" id="{{ $input_id }}" data-label="{{ $field_user -> field_name_display }}" rows="3" data-default-value="{{ $value }}" data-common-name="{{ $common_name }}">{{  $value }}</textarea>
                                        </div>
                                    </div>
                                </div>

                            @elseif($field_type == 'name' || $field_type == 'address')

                                <div class="row p-3 mb-1">

                                    @foreach($fields_user_inputs as $user_input)

                                        @if($field_user -> field_id == $user_input -> field_id)

                                            @php
                                            $input_id = $field_user -> field_id.'_'.$user_input -> input_id;
                                            $value = get_value($field_values, $input_id);
                                            $common_name_input = $user_input -> input_name;

                                            if(!$value) {
                                                $value = $common_fields -> GetCommonNameValue($common_name_input, $user_input -> input_id, 'system', $Listing_ID, '', $Agent_ID);
                                            }

                                            $address_type = address_type($common_name_input);

                                            @endphp

                                            <div class="col-12">
                                                <div class=" my-3">

                                                    <input type="text"
                                                    class="custom-form-element form-input fillable-field-input"
                                                    id="{{ $input_id }}"
                                                    data-label="{{ $common_name_input }}"
                                                    data-type="{{ $common_name_input }}"
                                                    data-address-type="{{ $address_type }}"
                                                    value="{{ $value }}"
                                                    data-default-value="{{ $value }}"
                                                    data-common-name="{{ $common_name_input }}">

                                                </div>
                                            </div>

                                        @endif
                                    @endforeach

                                </div>

                            @elseif($field_type == 'number')

                                @php
                                $input_id = $field_user -> field_id;
                                $value = get_value($field_values, $input_id);
                                if(!$value) {
                                    $value = $common_fields -> GetCommonNameValue($common_name, $field_user -> field_id, 'system', $Listing_ID, '', $Agent_ID);
                                }
                                @endphp

                                <div class="row p-3 border border-secondary mb-1">
                                    <div class="col-12">

                                        <div class="my-1">
                                            <input type="text" class="custom-form-element form-input numbers-only fillable-field-input" id="{{ $input_id }}" data-label="{{ $field_user -> field_name_display }}" value="{{ $value }}" data-common-name="{{ $common_name }}">
                                        </div>
                                    </div>
                                </div>

                            @endif

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
