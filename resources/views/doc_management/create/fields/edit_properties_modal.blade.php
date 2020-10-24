<div class="modal fade edit-properties-div draggable" id="edit_properties_modal_{{ $field_id }}" tabindex="-1" role="dialog" aria-labelledby="edit_properties_modal_{{ $field_id }}_title" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-content">
                <div class="modal-header draggable-handle">
                    <h4 class="modal-title" id="edit_properties_modal_{{ $field_id }}_title">Field Properties</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body pb-5">
                    <div class="h5-responsive text-orange my-2">{{ strtoupper($field_type) }}</div>
                    <div class="form-div">
                        <div class="container">
                            <div class="row">
                                {{-- <div class="col-12">
                                    <h5 class="text-primary">Field Name</h5>
                                </div> --}}
                                @if($field_type != 'checkbox' && $field_type != 'radio')
                                <div class="col-12">
                                    <select class="custom-form-element form-select field-data-name" id="name_select_{{ $field_id }}" data-field-type="common" data-default-value="{{$common_name}}" data-label="Select Common Name (Shared)">
                                        <option value="">&nbsp;</option>
                                        @foreach ($common_fields as $common_field_type => $field_names)
                                            @if($field_type == $common_field_type)
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
                                    <input type="text" class="custom-form-element form-input field-data-name" id="name_input_{{ $field_id }}" data-field-type="custom" value="{{ $custom_name }}" data-default-value="{{ $custom_name }}" data-label="{{ $label }}">
                                    <div class="collapse custom-name-results">
                                        <div class="list-group dropdown-results-div"></div>
                                    </div>
                                </div>
                                @if($field_type != 'checkbox' && $field_type != 'radio')
                                    <div class="col-12"><hr></div>
                                @endif

                                @if($field_type == 'number')
                                    <div class="col-12">
                                        <select class="custom-form-element form-select field-data-number-type" id="number_select_{{ $field_id }}" data-field-type="number-type" data-default-value="{{ $field_number_type }}" data-label="Number Type">
                                            <option value="">&nbsp;</option>
                                            <option value="numeric" @if($field_number_type=='numeric' ) selected @endif>Numeric - 3,000</option>
                                            <option value="written" @if($field_number_type=='written' ) selected @endif>Written - Three Thousand</option>
                                        </select>
                                    </div>
                                @elseif($field_type == 'textline')
                                    <div class="col-12">
                                        <select class="custom-form-element form-select field-data-textline-type" id="textline_select_{{ $field_id }}" data-field-type="textline-type" data-default-value="{{ $field_textline_type }}" data-label="Format Type - Optional">
                                            <option value="">&nbsp;</option>
                                            <option value="number numbers-only" @if($field_textline_type=='number numbers-only' ) selected @endif>Number</option>
                                            <option value="phone numbers-only" @if($field_textline_type=='phone numbers-only' ) selected @endif>Phone Number</option>
                                        </select>
                                    </div>
                                @elseif($field_type == 'address')
                                    <div class="col-12">
                                        <select class="custom-form-element form-select field-data-address-type" id="address_select_{{ $field_id }}" data-field-type="address-type" data-default-value="{{ $field_address_type }}" data-label="Address Type">
                                            <option value="">&nbsp;</option>
                                            <option value="full" @if($field_address_type=='full' ) selected @endif>Full Address</option>
                                            <option value="street" @if($field_address_type=='street' ) selected @endif>Street Address</option>
                                            <option value="city" @if($field_address_type=='city' ) selected @endif>City</option>
                                            <option value="state" @if($field_address_type=='state' ) selected @endif>State</option>
                                            <option value="zip" @if($field_address_type=='zip' ) selected @endif>Zip Code</option>
                                            <option value="county" @if($field_address_type=='county' ) selected @endif>County</option>
                                        </select>
                                    </div>
                                {{-- @elseif($field_type == 'radio')
                                    <div class="col-12">
                                        <input type="text" class="custom-form-element form-input field-data-radio-value" id="field_value_input_{{ $field_id }}" value="{{ $field -> radio_value }}" data-default-value="{{ $field -> radio_value }}" data-label="Field Value">
                                    </div>
                                @elseif($field_type == 'checkbox')
                                    <div class="col-12">
                                        <input type="text" class="custom-form-element form-input field-data-checkbox-value" id="field_value_input_{{ $field_id }}" value="{{ $field -> checkbox_value }}" data-default-value="{{ $field -> checkbox_value }}" data-label="Field Value">
                                    </div> --}}
                                @endif

                                @if($field_type != 'checkbox' && $field_type != 'radio')
                                    <div class="col-12">
                                        <input type="text" class="custom-form-element form-input field-data-helper-text" id="helper_text_input_{{ $field_id }}" value="{{ $field_helper_text }}" data-default-value="{{ $field_helper_text }}" data-label="Helper Text - Optional">
                                    </div>
                                @endif
                                @if($field_type == 'address' || $field_type == 'name')
                                    <div class="col-12">
                                        <div class="text-gray font-weight-bold mt-3">Inputs</div>
                                        <div class="container field-data-inputs-container">
                                            @foreach($field_inputs as $field_input)
                                                @if($field_input -> field_id == $field_id)
                                                    <div class="row field-data-inputs-div">
                                                        <div class="col-12">
                                                            <div class="border border-gray p-2 mb-3 shadow">
                                                                <div class="clearfix">
                                                                    <a href="javascript: void(0)" class="delete-input float-right mr-2 mt-1"><i class="fas fa-times-square text-danger fa-lg"></i></a>
                                                                </div>
                                                                <div class="mt-1">
                                                                    <input type="text" class="custom-form-element form-input field-data-input" id="input_name_{{ $field_id }}_{{ $field_input -> input_id }}" value="{{ $field_input -> input_name }}" data-default-value="{{ $field_input -> input_name }}" data-id="{{ $field_input -> input_id }}" data-label="Input Name">
                                                                </div>
                                                                <div class="mt-3 mb-2">
                                                                    <input type="text" class="custom-form-element form-input field-data-input-helper-text" id="input_helper_text_{{ $field_id }}_{{ $field_input -> input_id }}" value="{{ $field_input -> input_helper_text }}" data-default-value="{{ $field_input -> input_helper_text }}" data-id="{{ $field_input -> input_id }}" data-label="Input Helper Text">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                        <div class="my-3">
                                            <a href="javascript: void(0);" class="text-green add-input" data-field-id="{{ $field_id }}"><i class="fa fa-plus"></i> Add Input</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div> <!-- end container -->
                    </div>

                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <button href="javascript:void(0);" class="btn btn-danger" data-dismiss="modal"><i class="fad fa-ban mr-2"></i> Cancel</button>
                    @if($published == 'no')<button href="javascript: void(0);" class="btn btn-success field-save-properties" data-group-id="{{ $group_id }}" data-type="{{ $field_type }}"><i class="fad fa-save mr-2"></i> Save</button>@endif
                </div>
            </div>
        </div>
    </div>
</div>
