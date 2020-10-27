<div class="edit-properties-div p-2" id="edit_properties_div_{{ $field_id }}">

    @php
    $cols = $field_type == 'name' || $field_type == 'date' || $field_type == 'radio' ? 'col-12' : 'col-12 col-sm-6';
    @endphp

    <div class="h5-responsive text-orange mb-2 ml-3">{{ strtoupper($field_type) }}</div>

    <div class="form-div">

        <div class="container p-0">

            <div class="row no-gutters">

                <div class="{{ $cols }}">

                    <div class="p-2 mx-2 border rounded">

                        <h6 class="text-primary">Field Name</h6>

                        @if($field_type != 'checkbox' && $field_type != 'radio')

                            <select class="custom-form-element form-select field-data-name required" id="name_select_{{ $field_id }}" data-field-type="common" data-default-value="{{$common_name}}" data-label="Select Common Name (Shared)">
                                <option value="">&nbsp;</option>
                                @foreach ($common_fields as $common_field_type => $field_names)
                                    @if($field_type == $common_field_type)
                                        @foreach($field_names as $field_name)
                                            <option value="{{ $field_name[0] }}" @if($field_name[0]==$common_name) selected @endif>{{ $field_name[0] }}</option>
                                        @endforeach
                                    @endif
                                @endforeach
                            </select>

                            <div class="text-primary w-100 text-center my-0">OR</div>

                        @endif

                        <input type="text" class="custom-form-element form-input field-data-name required" id="name_input_{{ $field_id }}" data-field-type="custom" value="{{ $custom_name }}" data-default-value="{{ $custom_name }}" data-label="{{ $label }}">

                        <div class="custom-name-wrapper">
                            <div class="custom-name-results">
                                <div class="list-group dropdown-results-div"></div>
                            </div>
                        </div>

                    </div>

                </div>

                @if($field_type == 'number' || $field_type == 'textline' || $field_type == 'address')

                    <div class="{{ $cols }}">

                        <div class="p-2 mx-2 border rounded">

                            <h6 class="text-primary">Options</h6>

                            @if($field_type == 'number')

                                <select class="custom-form-element form-select field-data-number-type required" id="number_select_{{ $field_id }}" data-field-type="number-type" data-default-value="{{ $field_number_type }}" data-label="Number Type">
                                    <option value="">&nbsp;</option>
                                    <option value="numeric" @if($field_number_type=='numeric' ) selected @endif>Numeric - 3,000</option>
                                    <option value="written" @if($field_number_type=='written' ) selected @endif>Written - Three Thousand</option>
                                </select>

                            @elseif($field_type == 'textline')

                                <select class="custom-form-element form-select field-data-textline-type" id="textline_select_{{ $field_id }}" data-field-type="textline-type" data-default-value="{{ $field_textline_type }}" data-label="Format Type - Optional">
                                    <option value="">&nbsp;</option>
                                    <option value="number numbers-only" @if($field_textline_type=='number numbers-only' ) selected @endif>Number</option>
                                    <option value="phone numbers-only" @if($field_textline_type=='phone numbers-only' ) selected @endif>Phone Number</option>
                                </select>

                            @elseif($field_type == 'address')

                                <select class="custom-form-element form-select field-data-address-type required" id="address_select_{{ $field_id }}" data-field-type="address-type" data-default-value="{{ $field_address_type }}" data-label="Address Type">
                                    <option value="">&nbsp;</option>
                                    <option value="full" @if($field_address_type=='full' ) selected @endif>Full Address</option>
                                    <option value="street" @if($field_address_type=='street' ) selected @endif>Street Address</option>
                                    <option value="city" @if($field_address_type=='city' ) selected @endif>City</option>
                                    <option value="state" @if($field_address_type=='state' ) selected @endif>State</option>
                                    <option value="zip" @if($field_address_type=='zip' ) selected @endif>Zip Code</option>
                                    <option value="county" @if($field_address_type=='county' ) selected @endif>County</option>
                                </select>

                            @endif

                        </div>

                    </div>

                @endif

            </div>
            <hr>
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-around my-2">

                        <button class="btn btn-danger" data-toggle="collapse" href="#properties_container_{{ $field_id }}" role="button" aria-expanded="false" aria-controls="properties_container_{{ $field_id }}"><i class="fad fa-ban mr-2"></i> Cancel</button>

                        @if($published == 'no')
                            <a href="javascript: void(0);" class="btn btn-success field-save-properties" data-field-id="{{ $field_id }}" data-group-id="{{ $group_id }}" data-type="{{ $field_type }}"><i class="fad fa-save mr-2"></i> Save</a>
                        @endif

                    </div>
                </div>
            </div>

            <div class="field-inputs-div">

                @foreach($field_inputs as $field_input)

                    @if($field_input -> field_id == $field_id)

                        <input type="hidden" class="field-data-input" id="input_name_{{ $field_id }}_{{ $field_input -> input_id }}" value="{{ $field_input -> input_name }}" data-default-value="{{ $field_input -> input_name }}" data-id="{{ $field_input -> input_id }}">

                    @endif

                @endforeach

            </div>

        </div>

    </div>

</div>
