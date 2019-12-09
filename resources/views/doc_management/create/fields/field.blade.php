<?php
$common_name = '';
$custom_name = '';
if($field['field_name_type'] == 'common') {
    $common_name = $field['field_name_display'];
} else if($field['field_name_type'] == 'custom') {
    $custom_name = $field['field_name_display'];
}
if($field['field_type'] == 'textline' || $field['field_type'] == 'address' || $field['field_type'] == 'name' || $field['field_type'] == 'number') {
    $field_class = 'standard textline';
    $add_items = 'yes';
    $field_html = '<div class="textline-html"></div>';
    $icon = '<i class="fas fa-horizontal-rule fa-lg text-primary"></i>';
} else if($field['field_type'] == 'radio') {
    $field_class = 'standard '.$field['field_type'];
    $add_items = 'yes';
    $field_html = '<div class="radio-html"></div>';
    $icon = '<i class="fas fa-circle fa-lg text-primary"></i>';
} else if($field['field_type'] == 'checkbox') {
    $field_class = 'standard '.$field['field_type'];
    $add_items = 'yes';
    $field_html = '<div class="checkbox-html"></div>';
    $icon = '<i class="fal fa-square-full fa-lg text-primary"></i>';
} else {
    $field_class = 'standard '.$field['field_type'];
    $add_items = 'no';
    $field_html = '';
    $icon = '';
}
?>
<div
    class="field-div {{ $field_class }}-div group_{{ $field['group_id'] }}"
    style="position: absolute;
    top: {{ $field['top_perc'] }}%;
    left: {{ $field['left_perc'] }}%;
    height: {{ $field['height_perc'] }}%;
    width: {{ $field['width_perc'] }}%;"
    id="field_{{ $field['field_id'] }}"
    data-field-id="{{ $field['field_id'] }}"
    data-group-id="{{ $field['group_id'] }}"
    data-type="{{ $field['field_type'] }}"
    data-page="{{ $field['page'] }}"
    data-x="{{ $field['left'] }}"
    data-y="{{ $field['top'] }}"
    data-h="{{ $field['height'] }}"
    data-w="{{ $field['width'] }}"
    data-xp="{{ $field['left_perc'] }}"
    data-yp="{{ $field['top_perc'] }}"
    data-hp="{{ $field['height_perc'] }}"
    data-wp="{{ $field['width_perc'] }}"
    data-commonname="{{ $common_name }}"
    data-customname="{{ $custom_name }}">
    <div class="field-status-div d-flex justify-content-left">
        <div class="field-status-name-div"></div>
        <div class="field-status-group-div float-right"></div>
    </div>
    <div class="field-options-holder focused shadow container">
        <div class="row m-0 p-0">
            <div class="col-2 p-0">
                <div class="field-handle"><i class="fal fa-arrows fa-lg text-primary"></i></div>
            </div>
            <div class="col-8 p-0">
                <div class="d-flex justify-content-center">
                    <div class="mini-slide-container w-100">
                        <a href="javascript: void(0);" class="mini-slider-icon d-block h-100 text-center"><i class="fal fa-arrows-v text-primary"></i></a>
                        <ul class="mini-slider list-group list-group-flush border border-primary p-0">
                            <li class="list-group-item text-center p-0"><a href="javascript:void(0);" class="mini-slider-option w-100 h-100 d-block p-2" data-direction="up"><i class="fal fa-arrow-up text-primary"></i></a></li>
                            <li class="list-group-item text-center p-0"><a href="javascript:void(0);" class="mini-slider-option w-100 h-100 d-block p-2" data-direction="down"><i class="fal fa-arrow-down text-primary"></i></a></li>
                        </ul>
                    </div>
                    @if($add_items == 'yes')
                    <div class="add-item-container mr-3">
                        <div class="field-add-item mr-3 h-100">
                            {!! $icon !!}
                            <i class="fal fa-plus fa-xs ml-1 text-primary add-item-plus"></i>
                        </div>
                        <div class="add-item-div shadow-lg field-popup">
                            <div class="add-item-content">
                                Add Item To Group?
                                <div class="d-flex justify-content-around">
                                    <a href="javascript: void(0);" class="btn btn-success btn-sm add-item shadow" data-group-id="{{ $field['group_id'] }}">Confirm</a>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm field-close-add-item">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="properties-container mr-3">
                        <div class="field-properties" data-group-id="{{ $field['group_id'] }}" data-field-type="{{ $field['field_type'] }}">
                            <i class="fal fa-info-circle fa-lg text-primary"></i>
                        </div>
                        <div class="modal fade edit-properties-div" id="edit_properties_modal_{{$field['field_id']}}" tabindex="-1" role="dialog" aria-labelledby="edit_properties_modal_{{$field['field_id']}}_title"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h4 class="modal-title" id="edit_properties_modal_{{$field['field_id']}}_title">Field Properties</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true" class="text-white">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <h5 class="text-primary">Type - {{ strtoupper($field['field_type']) }}</h5>
                                        <div class="form-div">
                                            <div class="container">
                                                <div class="row p-2 border border-secondary mb-1">
                                                    <h5 class="text-secondary mb-0">Field Name</h5>
                                                    @if($field['field_type'] != 'checkbox' && $field['field_type'] != 'radio')
                                                    <div class="col-12">
                                                        <div class="my-1">
                                                            <select class="form-select field-data-name form-select-searchable" id="name_select_{{$field['field_id']}}" data-field-type="common" data-default-value="{{$common_name}}" data-label="Select Common Name (Shared)">
                                                                <option value="">&nbsp;</option>
                                                                @foreach ($common_fields as $field_type => $field_names)
                                                                    @if($field['field_type'] == $field_type)
                                                                        @foreach($field_names as $field_name)
                                                                        <option value="{{ $field_name[0] }}" @if($field_name[0]==$common_name) selected @endif>{{ $field_name[0] }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                @endforeach
                                                            </select>

                                                        </div>
                                                    </div>
                                                    <div class="text-primary text-center w-100">OR</div>
                                                    @endif
                                                    <div class="col-12">
                                                        <div class="my-1">
                                                            <input type="text" class="form-input field-data-name" id="name_input_{{$field['field_id']}}" data-field-type="custom" value="{{ $custom_name }}" data-default-value="{{ $custom_name }}" data-label="Custom Name">

                                                        </div>
                                                    </div>
                                                </div>
                                                @if($field['field_type'] == 'number')
                                                <div class="row p-2 border border-secondary mb-1">
                                                    <div class="col-12">
                                                        <h5 class="text-secondary mb-0">Number Type</h5>
                                                        <div class="my-1">
                                                            <select class="form-select field-data-number-type" id="number_select_{{$field['field_id']}}" data-field-type="number-type" data-default-value="{{ $field['number_type'] }}" data-label="Number Type">
                                                                <option value="">&nbsp;</option>
                                                                <option value="numeric" @if($field['number_type']=='numeric' ) selected @endif>Numeric - 3,000</option>
                                                                <option value="written" @if($field['number_type']=='written' ) selected @endif>Written - Three Thousand</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                @elseif($field['field_type'] == 'textline')
                                                <div class="row p-2 border border-secondary mb-1">
                                                    <div class="col-12">
                                                        <h5 class="text-secondary mb-0">Text Type <small>(Optional - Use to format the value)</small></h5>
                                                        <div class="my-1">
                                                            <select class="form-select field-data-textline-type" id="textline_select_{{$field['field_id']}}" data-field-type="textline-type" data-default-value="{{ $field['textline_type'] }}" data-label="Text Type">
                                                                <option value="">&nbsp;</option>
                                                                <option value="number numbers-only" @if($field['textline_type']=='number numbers-only' ) selected @endif>Number</option>
                                                                <option value="phone numbers-only" @if($field['textline_type']=='phone numbers-only' ) selected @endif>Phone Number</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                @elseif($field['field_type'] == 'address')
                                                <div class="row p-2 border border-secondary mb-1">
                                                    <div class="col-12">
                                                        <h5 class="text-secondary mb-0">Address Type</h5>
                                                        <div class="my-1">
                                                            <select class="form-select field-data-address-type" id="address_select_{{$field['field_id']}}" data-field-type="address-type" data-default-value="{{ $field['address_type'] }}" data-label="Address Type">
                                                                <option value="">&nbsp;</option>
                                                                <option value="full" @if($field['address_type']=='full' ) selected @endif>Full Address</option>
                                                                <option value="street" @if($field['address_type']=='street' ) selected @endif>Street Address</option>
                                                                <option value="city" @if($field['address_type']=='city' ) selected @endif>City</option>
                                                                <option value="state" @if($field['address_type']=='state' ) selected @endif>State</option>
                                                                <option value="zip" @if($field['address_type']=='zip' ) selected @endif>Zip Code</option>
                                                                <option value="county" @if($field['address_type']=='county' ) selected @endif>County</option>
                                                            </select>

                                                        </div>
                                                    </div>
                                                </div>
                                                @elseif($field['field_type'] == 'radio')
                                                <div class="row p-2 border border-secondary mb-1">
                                                    <div class="col-12">
                                                        <h5 class="text-secondary mb-0">Radio Input Value</h5>
                                                        <div class="my-1">
                                                            <input type="text" class="form-input field-data-radio-value" id="field_value_input_{{$field['field_id']}}" value="{{ $field['radio_value'] }}" data-default-value="{{ $field['radio_value'] }}" data-label="Field Value">
                                                        </div>
                                                    </div>
                                                </div>
                                                @elseif($field['field_type'] == 'checkbox')
                                                <div class="row p-2 border border-secondary mb-1">
                                                    <div class="col-12">
                                                        <h5 class="text-secondary mb-0">Checkbox Value</h5>
                                                        <div class="my-1">
                                                            <input type="text" class="form-input field-data-checkbox-value" id="field_value_input_{{$field['field_id']}}" value="{{ $field['checkbox_value'] }}" data-default-value="{{ $field['checkbox_value'] }}" data-label="Field Value">
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="row p-2 border border-secondary mb-1">
                                                    <div class="col-12">
                                                        <h5 class="text-secondary mb-0">Helper Text</h5>
                                                        <div class="my-1">
                                                            <input type="text" class="form-input field-data-helper-text" id="helper_text_input_{{$field['field_id']}}" value="{{ $field['helper_text'] }}" data-default-value="{{ $field['helper_text'] }}" data-label="Helper Text">
                                                        </div>
                                                    </div>
                                                </div>
                                                @if($field['field_type'] == 'address' || $field['field_type'] == 'name')
                                                <div class="row p-2 border border-secondary mb-1">
                                                    <div class="col-12">
                                                        <h5 class="text-secondary mb-0">Inputs</h5>
                                                        <div class="container field-data-inputs-container">
                                                            @foreach($field_inputs as $field_input)
                                                            @if($field_input['field_id'] == $field['field_id'])
                                                            <div class="row p-2 border border-secondary mb-1 field-data-inputs-div">
                                                                <a href="javascript: void(0)" class="delete-input"><i class="fas fa-times-square text-danger fa-lg"></i></a>
                                                                <div class="col-12 mt-3">
                                                                    <div class="my-1">
                                                                        <input type="text" class="form-input field-data-input" id="input_name_{{ $field['field_id'] }}_{{ $field_input['input_id'] }}" value="{{ $field_input['input_name'] }}" data-default-value="{{ $field_input['input_name'] }}" data-id="{{ $field_input['input_id'] }}" data-label="Input Name">
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="my-1">
                                                                        <input type="text" class="form-input field-data-input-helper-text" id="input_helper_text_{{ $field['field_id'] }}_{{ $field_input['input_id'] }}" value="{{ $field_input['input_helper_text'] }}" data-default-value="{{ $field_input['input_helper_text'] }}" data-id="{{ $field_input['input_id'] }}" data-label="Input Helper Text">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endif
                                                            @endforeach
                                                        </div>
                                                        <a href="javascript: void(0);" class="text-green add-input" data-field-id="{{ $field['field_id'] }}"><i class="fa fa-plus"></i> Add Input</a>
                                                    </div>
                                                </div>
                                                @endif
                                            </div> <!-- end container -->
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button href="javascript: void(0);" class="btn btn-success btn-sm shadow field-save-properties" data-group-id="{{ $field['group_id'] }}" data-type="{{ $field['field_type'] }}">Save</button>
                                        <button href="javascript:void(0);" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end div -->
                </div> <!-- end d-flex -->
            </div> <!-- end col-8 -->
            <div class="col-2 p-0">
                <div class="remove-field"><i class="fal fa-times-circle fa-lg text-danger"></i></div>
            </div>
        </div> <!-- end row m-0 p-0 -->
    </div> <!-- end field-options-holder -->
    @if($field['field_type'] != 'checkbox' && $field['field_type'] != 'radio')
    <div class="ui-resizable-handle ui-resizable-e focused"></div>
    <div class="ui-resizable-handle ui-resizable-w focused"></div>
    @endif
    {!! $field_html !!}
</div> <!-- end field-div -->
