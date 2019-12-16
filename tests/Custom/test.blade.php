<div class="modal fade" id="modal_{{ $field['field_id'] }}" tabindex="-1" role="dialog" aria-labelledby="modal_{{ $field['field_id'] }}_title"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_{{ $field['field_id'] }}_title">Field Properties</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">


                <div class="edit-properties-div card">
                    <div class="form-div">
                        <div class="bg-secondary-light text-orange card-header">Type - {{ strtoupper($field['field_type']) }}</div>
                        <div class="card-body p-3 pt-1">
                            <div class="container">
                                <div class="row">

                                    @if($field['field_type'] != 'checkbox' && $field['field_type'] != 'radio')

                                    <div class="col-12">
                                        Common Field<br>
                                        <select class="custom-select field-data-name" data-fieldtype="common" data-defaultvalue="{{ $common_name }}">
                                            <option value=""></option>
                                            @foreach ($common_fields as $field_type => $field_names)
                                            @if($field['field_type'] == $field_type)
                                            @foreach($field_names as $field_name)
                                            <option
                                                value="{{ $field_name[0] }}"
                                                @if($field_name[0]==$common_name) selected @endif>
                                                {{ $field_name[1] }}
                                            </option>
                                            @endforeach
                                            @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="text-primary text-center w-100 mt-2">OR</div>
                                    @endif
                                    <div class="col-12">
                                        Custom Name<br>
                                        <input type="text" class="form-control field-data-name" data-fieldtype="custom" value="{{ $custom_name }}" data-defaultvalue="{{ $custom_name }}">
                                    </div>
                                    <hr>
                                </div>

                                @if($field['field_type'] == 'number')
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        Number Type<br>
                                        <select class="custom-select field-data-number-type" data-fieldtype="number-type" data-defaultvalue="{{ $field['number_type'] }}">
                                            <option value=""></option>
                                            <option value="numeric" @if($field['number_type']=='numeric' ) selected @endif>Numeric - 3,000</option>
                                            <option value="written" @if($field['number_type']=='written' ) selected @endif>Written - Three Thousand</option>
                                        </select>
                                    </div>
                                </div>
                                <hr>
                                @elseif($field['field_type'] == 'address')
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        Address Type<br>
                                        <select class="custom-select field-data-address-type" data-fieldtype="address-type" data-defaultvalue="{{ $field['address_type'] }}">
                                            <option value=""></option>
                                            <option value="full" @if($field['address_type']=='full' ) selected @endif>Full Address</option>
                                            <option value="street" @if($field['address_type']=='street' ) selected @endif>Street Address</option>
                                            <option value="city" @if($field['address_type']=='city' ) selected @endif>City</option>
                                            <option value="state" @if($field['address_type']=='state' ) selected @endif>State</option>
                                            <option value="zip" @if($field['address_type']=='zip' ) selected @endif>Zip Code</option>
                                            <option value="county" @if($field['address_type']=='county' ) selected @endif>County</option>
                                        </select>
                                    </div>
                                </div>
                                <hr>
                                @elseif($field['field_type'] == 'radio')
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        Field Value<br>
                                        <input type="text" class="form-control field-data-radio-value" value="{{ $field['radio_value'] }}" data-defaultvalue="{{ $field['radio_value'] }}">
                                    </div>
                                </div>
                                <hr>
                                @endif

                            </div> <!-- end container -->
                        </div> <!-- end card-body -->

                    </div> <!-- end form-div -->
                </div> <!-- end edit-properties-div shadow field-popup card -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger shadow field-close-properties" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success shadow save_field_properties" data-groupid="{{ $field['group_id'] }}" data-type="{{ $field['field_type'] }}">Save changes</button>
            </div>
        </div>
    </div>
</div>