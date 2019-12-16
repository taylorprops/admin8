@php

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
@endphp

<div
    class="field-div {{ $field_class }}-div group_{{ $field['group_id'] }}"
    style="position: absolute;
    top: {{ $field['top'] }}px;
    left: {{ $field['left'] }}px;
    height: {{ $field['height'] }}px;
    width: {{ $field['width'] }}px;"
    id="field_{{ $field['field_id'] }}"
    data-fieldid="{{ $field['field_id'] }}"
    data-groupid="{{ $field['group_id'] }}"
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
                <div class="field-handle"><i class="fal fa-ellipsis-v-alt fa-lg text-primary"></i></div>
            </div>
            <div class="col-8 p-0">
                <div class="d-flex justify-content-center">
                    @if($add_items == 'yes')
                    <div class="add-item-container mr-2">
                        <div class="field-add-item mr-3 h-100">
                            {!! $icon !!}
                            <i class="fal fa-plus fa-xs ml-1 text-primary add-item-plus"></i>
                        </div>
                        <div class="add-item-div shadow-lg field-popup">
                            <div class="add-item-content">
                                Add Item To Group?
                                <div class="d-flex justify-content-around">
                                    <a href="javascript: void(0);" class="btn btn-success btn-sm add-item shadow" data-groupid="{{ $field['group_id'] }}">Confirm</a>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm field-close-add-item">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="properties-container">
                        <div class="field-properties" data-groupid="{{ $field['group_id'] }}">
                            <i class="fal fa-info-circle fa-lg text-primary"></i>
                        </div>

                        <div class="modal fade edit-properties-div" id="edit_properties_modal" tabindex="-1" role="dialog" aria-labelledby="edit_properties_modal_title"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h4 class="modal-title" id="edit_properties_modal_title">Field Properties</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true" class="text-white">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <h5 class="text-primary">Type - {{ strtoupper($field['field_type']) }}</h5>
                                        <div class="form-div">
                                            <div class="container">
                                                <div class="row p-3 border border-secondary mb-1">
                                                    <h5 class="text-secondary">Field Name</h5>
                                                    @if($field['field_type'] != 'checkbox' && $field['field_type'] != 'radio')
                                                    <div class="col-12">
                                                        <div class="md-form my-1">
                                                            <select class="field-data-name mdb-select colorful-select dropdown-primary" id="name_select_{{$field['field_id']}}" data-fieldtype="common" data-defaultvalue="{{ $common_name }}">
                                                                <option value="">&nbsp;</option>
                                                                @foreach ($common_fields as $field_type => $field_names)
                                                                @if($field['field_type'] == $field_type)
                                                                @foreach($field_names as $field_name)
                                                                <option
                                                                    value="{{ $field_name[0] }}"
                                                                    @if($field_name[0]==$common_name) selected @endif>
                                                                    {{ $field_name[0] }}
                                                                </option>
                                                                @endforeach
                                                                @endif
                                                                @endforeach
                                                            </select>
                                                            <label for="name_select_{{$field['field_id']}}" class="mdb-main-label">Select Common Name (Shared)</label>
                                                        </div>
                                                    </div>
                                                    <div class="text-primary text-center w-100">OR</div>
                                                    @endif
                                                    <div class="col-12">
                                                        <div class="md-form my-1">
                                                            <input type="text" class="form-control field-data-name" id="name_input_{{$field['field_id']}}" data-fieldtype="custom" value="{{ $custom_name }}" data-defaultvalue="{{ $custom_name }}">
                                                            <label for="name_input_{{$field['field_id']}}">Custom Name</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if($field['field_type'] == 'number')
                                                <div class="row p-3 border border-secondary mb-1">
                                                    <div class="col-12">
                                                        <h5 class="text-secondary">Number Type</h5>
                                                        <div class="md-form my-1">
                                                            <select class="field-data-number-type mdb-select colorful-select dropdown-primary" id="number_select_{{$field['field_id']}}" data-fieldtype="number-type" data-defaultvalue="{{ $field['number_type'] }}">
                                                                <option value="">&nbsp;</option>
                                                                <option value="numeric" @if($field['number_type']=='numeric' ) selected @endif>Numeric - 3,000</option>
                                                                <option value="written" @if($field['number_type']=='written' ) selected @endif>Written - Three Thousand</option>
                                                            </select>
                                                            <label for="number_select_{{$field['field_id']}}" class="mdb-main-label">Number Type</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                @elseif($field['field_type'] == 'address')
                                                <div class="row p-3 border border-secondary mb-1">
                                                    <div class="col-12">
                                                        <h5 class="text-secondary">Address Type</h5>
                                                        <div class="md-form my-1">
                                                            <select class="field-data-address-type mdb-select colorful-select dropdown-primary" id="address_select_{{$field['field_id']}}" data-fieldtype="address-type" data-defaultvalue="{{ $field['address_type'] }}">
                                                                <option value="">&nbsp;</option>
                                                                <option value="full" @if($field['address_type']=='full' ) selected @endif>Full Address</option>
                                                                <option value="street" @if($field['address_type']=='street' ) selected @endif>Street Address</option>
                                                                <option value="city" @if($field['address_type']=='city' ) selected @endif>City</option>
                                                                <option value="state" @if($field['address_type']=='state' ) selected @endif>State</option>
                                                                <option value="zip" @if($field['address_type']=='zip' ) selected @endif>Zip Code</option>
                                                                <option value="county" @if($field['address_type']=='county' ) selected @endif>County</option>
                                                            </select>
                                                            <label for="address_select_{{$field['field_id']}}" class="mdb-main-label">Address Type</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                @elseif($field['field_type'] == 'radio')
                                                <div class="row p-3 border border-secondary mb-1">
                                                    <div class="col-12">
                                                        <h5 class="text-secondary">Radio Input Value</h5>
                                                        <div class="md-form my-1">
                                                            <input type="text" class="form-control field-data-radio-value" id="field_value_input_{{$field['field_id']}}" value="{{ $field['radio_value'] }}" data-defaultvalue="{{ $field['radio_value'] }}">
                                                            <label for="field_value_input_{{$field['field_id']}}">Field Value</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="row p-3 border border-secondary mb-1">
                                                    <div class="col-12">
                                                        <h5 class="text-secondary">Helper Text</h5>
                                                        <div class="md-form my-1">
                                                            <input type="text" class="form-control field-data-helper-text" id="helper_text_input_{{$field['field_id']}}" value="{{ $field['helper_text'] }}" data-defaultvalue="{{ $field['helper_text'] }}">
                                                            <label for="helper_text_input_{{$field['field_id']}}">Helper Text</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if($field['field_type'] == 'address' || $field['field_type'] == 'name')
                                                <div class="row p-3 border border-secondary mb-1">
                                                    <div class="col-12">
                                                        <h5 class="text-secondary">Inputs</h5>
                                                        <div class="container field-data-inputs-container">
                                                            @php $c = 0; @endphp
                                                            @foreach($field_inputs as $field_input)
                                                            @if($field_input['field_id'] == $field['field_id'])
                                                            @php $c += 1; @endphp
                                                            <div class="row p-2 border border-secondary mb-1 field-data-inputs-div">
                                                                <a href="javascript: void(0)" class="delete-input"><i class="fas fa-times-square text-danger fa-lg"></i></a>
                                                                <div class="col-12 mt-3">
                                                                    <div class="md-form my-1">
                                                                        <input type="text" class="form-control field-data-input" id="input_name_{{ $field['field_id'] }}_{{ $c }}" value="{{ $field_input['input_name'] }}" data-defaultvalue="{{ $field_input['input_name'] }}">
                                                                        <label for="input_name_{{ $field['field_id'] }}_{{ $c }}">Input Name</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="md-form my-1">
                                                                        <input type="text" class="form-control field-data-input-helper-text" id="input_helper_text_{{ $field['field_id'] }}_{{ $c }}" value="{{ $field_input['input_helper_text'] }}" data-defaultvalue="{{ $field_input['input_helper_text'] }}">
                                                                        <label for="input_helper_text_{{ $field['field_id'] }}_{{ $c }}">Input Helper Text</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endif
                                                            @endforeach
                                                        </div>
                                                        <a href="javascript: void(0);" class="text-green add-input" data-fieldid="{{ $field['field_id'] }}"><i class="fa fa-plus"></i> Add Input</a>
                                                    </div>
                                                </div>
                                                @endif
                                            </div> <!-- end container -->
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button href="javascript: void(0);" class="btn btn-success btn-sm shadow field-save-properties" data-groupid="{{ $field['group_id'] }}" data-type="{{ $field['field_type'] }}">Save</button>
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
    <div class="ui-resizable-handle ui-resizable-ne focused"></div>
    <div class="ui-resizable-handle ui-resizable-sw focused"></div>
    <div class="ui-resizable-handle ui-resizable-nw focused"></div>
    <div class="ui-resizable-handle ui-resizable-se focused"></div>
    {!! $field_html !!}
</div> <!-- end field-div -->