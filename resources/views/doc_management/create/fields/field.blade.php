<?php
$common_name = '';
$custom_name = '';

if($field -> field_name_type == 'common') {
    $common_name = $field -> field_name_display;
} else if($field -> field_name_type == 'custom') {
    $custom_name = $field -> field_name_display;
}

$label = 'Custom Name';
$show_options = true;
if($field -> field_type == 'textline' || $field -> field_type == 'address' || $field -> field_type == 'name' || $field -> field_type == 'number') {
    $field_class = 'standard textline';
    $add_items = 'yes';
    $field_html = '<div class="textline-html"></div>';
    $icon = '<i class="fas fa-horizontal-rule fa-lg"></i>';
} else if($field -> field_type == 'radio') {
    $field_class = 'standard '.$field -> field_type;
    $add_items = 'yes';
    $field_html = '<div class="radio-html"></div>';
    $icon = '<i class="fas fa-circle fa-lg"></i>';
    $label = 'Radio Button Group Name';
} else if($field -> field_type == 'checkbox') {
    $field_class = 'standard '.$field -> field_type;
    $add_items = 'yes';
    $field_html = '<div class="checkbox-html"></div>';
    $icon = '<i class="fal fa-square-full fa-lg"></i>';
    $show_options = false;
} else {
    $field_class = 'standard '.$field -> field_type;
    $add_items = 'no';
    $field_html = '';
    $icon = '';
}
?>
<div
    class="field-div {{ $field_class }}-div group_{{ $field -> group_id }}"
    style="position: absolute;
    top: {{ $field -> top_perc }}%;
    left: {{ $field -> left_perc }}%;
    height: {{ $field -> height_perc }}%;
    width: {{ $field -> width_perc }}%;"
    id="field_{{ $field -> field_id }}"
    data-field-id="{{ $field -> field_id }}"
    data-group-id="{{ $field -> group_id }}"
    data-type="{{ $field -> field_type }}"
    data-page="{{ $field -> page }}"
    data-x="{{ $field -> left }}"
    data-y="{{ $field -> top }}"
    data-h="{{ $field -> height }}"
    data-w="{{ $field -> width }}"
    data-xp="{{ $field -> left_perc }}"
    data-yp="{{ $field -> top_perc }}"
    data-hp="{{ $field -> height_perc }}"
    data-wp="{{ $field -> width_perc }}"
    data-commonname="{{ $common_name }}"
    data-customname="{{ $custom_name }}">
    <div class="field-status-div d-flex justify-content-left">
        <div class="field-status-name-div"></div>
        <div class="field-status-group-div float-right"></div>
    </div>

    <div class="field-options-holder focused">

        <div class="ml-3">
            <a href="javascript: void(0)" class="close-field-options"><i class="fa fa-times text-danger fa-2x"></i></a>
        </div>
        <div class="btn-group" role="group" aria-label="Field Options">
            <a type="button" class="btn btn-primary field-handle"><i class="fal fa-arrows fa-lg"></i></a>
            <a type="button" class="btn btn-primary mini-slider-button"><i class="fal fa-arrows-v fa-lg"></i></a>
            @if($show_options)
                @if($add_items == 'yes')
                    <a type="button" class="btn btn-primary field-add-item" data-group-id="{{ $field -> group_id }}""><i class="fal fa-plus fa-lg"></i></a>
                @endif
                <a type="button" class="btn btn-primary field-properties" data-group-id="{{ $field -> group_id }}" data-field-id="{{ $field -> field_id }}" data-field-type="{{ $field -> field_type }}"><i class="fal fa-info-circle fa-lg"></i></a>
            @endif
            <a type="button" class="btn btn-primary remove-field"><i class="fal fa-times-circle fa-lg"></i></a>
        </div>

    </div>
    <div class="mini-slider-div">
        <ul class="mini-slider list-group list-group-flush border border-primary p-0">
            <li class="list-group-item text-center p-0"><a href="javascript:void(0);" class="mini-slider-option w-100 h-100 d-block p-2" data-direction="up"><i class="fal fa-arrow-up text-primary"></i></a></li>
            <li class="list-group-item text-center p-0"><a href="javascript:void(0);" class="mini-slider-option w-100 h-100 d-block p-2" data-direction="down"><i class="fal fa-arrow-down text-primary"></i></a></li>
        </ul>
    </div>
    <div class="modal fade edit-properties-div draggable" id="edit_properties_modal_{{$field -> field_id}}" tabindex="-1" role="dialog" aria-labelledby="edit_properties_modal_{{$field -> field_id}}_title" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-content">
                    <div class="modal-header draggable-handle">
                        <h4 class="modal-title" id="edit_properties_modal_{{$field -> field_id}}_title">Field Properties</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="text-white">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body pb-5">
                        <div class="h5-responsive text-orange my-2">{{ strtoupper($field -> field_type) }}</div>
                        <div class="form-div">
                            <div class="container">
                                <div class="row">
                                    {{-- <div class="col-12">
                                        <h5 class="text-primary">Field Name</h5>
                                    </div> --}}
                                    @if($field -> field_type != 'checkbox' && $field -> field_type != 'radio')
                                    <div class="col-12">
                                        <select class="custom-form-element form-select field-data-name" id="name_select_{{$field -> field_id}}" data-field-type="common" data-default-value="{{$common_name}}" data-label="Select Common Name (Shared)">
                                            <option value="">&nbsp;</option>
                                            @foreach ($common_fields as $field_type => $field_names)
                                                @if($field -> field_type == $field_type)
                                                    @foreach($field_names as $field_name)
                                                        <option value="{{ $field_name[0] }}" @if($field_name[0]==$common_name) selected @endif>{{ $field_name[0] }}</option>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="text-primary text-center w-100">OR</div>
                                    @endif
                                    <div class="col-12">
                                        <input type="text" class="custom-form-element form-input field-data-name" id="name_input_{{$field -> field_id}}" data-field-type="custom" value="{{ $custom_name }}" data-default-value="{{ $custom_name }}" data-label="{{ $label }}">
                                        <div class="collapse custom-name-results">
                                            <div class="list-group dropdown-results-div"></div>
                                        </div>
                                    </div>
                                    @if($field -> field_type != 'checkbox' && $field -> field_type != 'radio')
                                        <div class="col-12"><hr></div>
                                    @endif

                                    @if($field -> field_type == 'number')
                                        <div class="col-12">
                                            <select class="custom-form-element form-select field-data-number-type" id="number_select_{{$field -> field_id}}" data-field-type="number-type" data-default-value="{{ $field -> number_type }}" data-label="Number Type">
                                                <option value="">&nbsp;</option>
                                                <option value="numeric" @if($field -> number_type=='numeric' ) selected @endif>Numeric - 3,000</option>
                                                <option value="written" @if($field -> number_type=='written' ) selected @endif>Written - Three Thousand</option>
                                            </select>
                                        </div>
                                    @elseif($field -> field_type == 'textline')
                                        <div class="col-12">
                                            <select class="custom-form-element form-select field-data-textline-type" id="textline_select_{{$field -> field_id}}" data-field-type="textline-type" data-default-value="{{ $field -> textline_type }}" data-label="Format Type - Optional">
                                                <option value="">&nbsp;</option>
                                                <option value="number numbers-only" @if($field -> textline_type=='number numbers-only' ) selected @endif>Number</option>
                                                <option value="phone numbers-only" @if($field -> textline_type=='phone numbers-only' ) selected @endif>Phone Number</option>
                                            </select>
                                        </div>
                                    @elseif($field -> field_type == 'address')
                                        <div class="col-12">
                                            <select class="custom-form-element form-select field-data-address-type" id="address_select_{{$field -> field_id}}" data-field-type="address-type" data-default-value="{{ $field -> address_type }}" data-label="Address Type">
                                                <option value="">&nbsp;</option>
                                                <option value="full" @if($field -> address_type=='full' ) selected @endif>Full Address</option>
                                                <option value="street" @if($field -> address_type=='street' ) selected @endif>Street Address</option>
                                                <option value="city" @if($field -> address_type=='city' ) selected @endif>City</option>
                                                <option value="state" @if($field -> address_type=='state' ) selected @endif>State</option>
                                                <option value="zip" @if($field -> address_type=='zip' ) selected @endif>Zip Code</option>
                                                <option value="county" @if($field -> address_type=='county' ) selected @endif>County</option>
                                            </select>
                                        </div>
                                    {{-- @elseif($field -> field_type == 'radio')
                                        <div class="col-12">
                                            <input type="text" class="custom-form-element form-input field-data-radio-value" id="field_value_input_{{$field -> field_id}}" value="{{ $field -> radio_value }}" data-default-value="{{ $field -> radio_value }}" data-label="Field Value">
                                        </div>
                                    @elseif($field -> field_type == 'checkbox')
                                        <div class="col-12">
                                            <input type="text" class="custom-form-element form-input field-data-checkbox-value" id="field_value_input_{{$field -> field_id}}" value="{{ $field -> checkbox_value }}" data-default-value="{{ $field -> checkbox_value }}" data-label="Field Value">
                                        </div> --}}
                                    @endif

                                    @if($field -> field_type != 'checkbox' && $field -> field_type != 'radio')
                                        <div class="col-12">
                                            <input type="text" class="custom-form-element form-input field-data-helper-text" id="helper_text_input_{{$field -> field_id}}" value="{{ $field -> helper_text }}" data-default-value="{{ $field -> helper_text }}" data-label="Helper Text - Optional">
                                        </div>
                                    @endif
                                    @if($field -> field_type == 'address' || $field -> field_type == 'name')
                                        <div class="col-12">
                                            <div class="text-gray font-weight-bold mt-3">Inputs</div>
                                            <div class="container field-data-inputs-container">
                                                @foreach($field_inputs as $field_input)
                                                    @if($field_input -> field_id == $field -> field_id)
                                                        <div class="row field-data-inputs-div">
                                                            <div class="col-12">
                                                                <div class="border border-gray p-2 mb-3 shadow">
                                                                    <div class="clearfix">
                                                                        <a href="javascript: void(0)" class="delete-input float-right mr-2 mt-1"><i class="fas fa-times-square text-danger fa-lg"></i></a>
                                                                    </div>
                                                                    <div class="mt-1">
                                                                        <input type="text" class="custom-form-element form-input field-data-input" id="input_name_{{ $field -> field_id }}_{{ $field_input -> input_id }}" value="{{ $field_input -> input_name }}" data-default-value="{{ $field_input -> input_name }}" data-id="{{ $field_input -> input_id }}" data-label="Input Name">
                                                                    </div>
                                                                    <div class="mt-3 mb-2">
                                                                        <input type="text" class="custom-form-element form-input field-data-input-helper-text" id="input_helper_text_{{ $field -> field_id }}_{{ $field_input -> input_id }}" value="{{ $field_input -> input_helper_text }}" data-default-value="{{ $field_input -> input_helper_text }}" data-id="{{ $field_input -> input_id }}" data-label="Input Helper Text">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                            <div class="my-3">
                                                <a href="javascript: void(0);" class="text-green add-input" data-field-id="{{ $field -> field_id }}"><i class="fa fa-plus"></i> Add Input</a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div> <!-- end container -->
                        </div>

                    </div>
                    <div class="modal-footer d-flex justify-content-around">
                        <button href="javascript:void(0);" class="btn btn-danger" data-dismiss="modal"><i class="fad fa-ban mr-2"></i> Cancel</button>
                        @if($published == 'no')<button href="javascript: void(0);" class="btn btn-success field-save-properties" data-group-id="{{ $field -> group_id }}" data-type="{{ $field -> field_type }}"><i class="fad fa-save mr-2"></i> Save</button>@endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    @if($field -> field_type != 'checkbox' && $field -> field_type != 'radio')
        <div class="ui-resizable-handle ui-resizable-e focused"></div>
        <div class="ui-resizable-handle ui-resizable-w focused"></div>
    @endif
    {!! $field_html !!}
</div> <!-- end field-div -->
