<div class="container p-1 p-sm-4">
    <div class="row">
        <div class="col-12 col-md-10 mx-auto">

            <div class="row members-list-group">
                <div class="col-12 col-lg-4">
                    <a href="javascript: void(0)" id="add_member_button" class="btn btn-success"><i class="fa fa-plus mr-2"></i> Add Member</a>
                    <div class="list-group my-3 border-top
                    " id="members_tab" role="tablist">
                        <a class="list-group-item list-group-item-action hidden font-weight-bold" id="add_member_group" data-toggle="list" href="#add_member_div" role="tab">New Contact</a>
                        @foreach($members as $member)
                            <a class="list-group-item list-group-item-action list-group-item-member @if($loop -> first) active @endif" id="member_{{ $member -> id }}_item" data-toggle="list" href="#member_{{ $member -> id }}_div" role="tab" data-member-type="{{ $member -> member_type }}">
                                <div class="row">
                                    <div class="col-5">
                                        <span class="font-weight-bold">{{ $resource_items -> GetResourceName($member -> member_type_id) }}</span>
                                    </div>
                                    <div class="col-7">
                                        @if($member -> entity_name) {{ $member -> entity_name }} @elseif($member -> first_name) {{ $member -> first_name . ' ' . $member -> last_name }} @else {{ $member -> company }} @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="col-12 col-lg-8">
                    <div class="tab-content get-members-tabs pt-2" id="members_tab_div">

                        @foreach($members as $member)
                            @php
                            $disabled = $member -> disabled == true ? 'disabled' : '';
                            @endphp
                        <div class="tab-pane animated fadeIn slow mt-0 {{ $disabled }} @if($loop -> first) show active @endif member-div" id="member_{{ $member -> id }}_div" role="tabpanel">
                            <div class="h3-responsive text-orange mb-2">@if($member -> entity_name) {{ $member -> entity_name }} @elseif($member -> first_name) {{ $member -> first_name . ' ' . $member -> last_name }} @else {{ $member -> company }} @endif</div>
                            <div class="card" id="member_div_{{ $member -> id }}">
                                <div class="card-body">

                                    <div class="row">

                                        <div class="col-12 col-md-6">
                                            <select class="custom-form-element form-select form-select-no-search form-select-no-cancel member-type-id" {{ $disabled }} data-label="Member Role">
                                                <option value=""></option>
                                                @foreach($contact_types as $contact_type)
                                                <option value="{{ $contact_type -> resource_id }}" @if($contact_type -> resource_id == $member -> member_type_id) selected @endif>{{ $contact_type -> resource_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-12 col-md-6 d-flex align-items-center flex-wrap">
                                            @if($disabled == '')
                                            <a href="javascript: void(0)" class="btn btn-sm btn-primary import-contact-button" data-ele="{{ '#member_div_'.$member -> id }}"><i class="fad fa-cloud-download-alt mr-2"></i> Import Contact</a>
                                            <a href="javascript: void(0)" class="btn btn-sm btn-danger delete-member-button" data-member-id="{{ $member -> id }}"><i class="fad fa-trash mr-2"></i> Delete Member</a>
                                            @endif
                                        </div>

                                        <div class="col-12 bank-trust-div">
                                            <input type="checkbox" class="custom-form-element form-checkbox bank-trust" {{ $disabled }} data-member="buyer" data-label="Buyer is a Trust, Company or other Entity" @if($member -> entity_name != '') checked @endif>
                                        </div>

                                        <div class="col-12 member-entity-name-div">
                                            <input type="text" class="custom-form-element form-input member-entity-name" {{ $disabled }} data-label="Trust, Company or other Entity Name" value="{{ $member -> entity_name }}">
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <input type="text" class="custom-form-element form-input member-first-name" {{ $disabled }} value="{{ $member -> first_name }}" data-label="First Name">
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <input type="text" class="custom-form-element form-input member-last-name" {{ $disabled }} value="{{ $member -> last_name }}" data-label="Last Name">
                                        </div>

                                    </div>
                                    <div class="row">

                                        <div class="col-12 col-md-6 company-div">
                                            <input type="text" class="custom-form-element form-input member-company" {{ $disabled }} value="{{ $member -> company }}" data-label="Company">
                                        </div>

                                        <div class="col-12 col-md-6 bright-mls-id-div">
                                            <input type="text" class="custom-form-element form-input member-bright-mls-id" {{ $disabled }} value="{{ $member -> bright_mls_id }}" data-label="Bright MLS ID">
                                        </div>

                                    </div>
                                    <div class="row">

                                        <div class="col-12 col-md-6">
                                            <input type="text" class="custom-form-element form-input phone member-phone" {{ $disabled }} value="{{ $member -> cell_phone }}" data-label="Phone">
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <input type="text" class="custom-form-element form-input member-email" {{ $disabled }} value="{{ $member -> email }}" data-label="Email">
                                        </div>

                                        <div class="col-12 col-md-6 home-address-div">
                                            <input type="text" class="custom-form-element form-input member-home-street" {{ $disabled }} value="{{ $member -> address_home_street }}" data-label="Street Address">
                                        </div>

                                        <div class="col-12 col-md-6 home-address-div">
                                            <input type="text" class="custom-form-element form-input member-home-city" {{ $disabled }} value="{{ $member -> address_home_city }}" data-label="City">
                                        </div>

                                        <div class="col-12 col-md-6 home-address-div">
                                            <select class="custom-form-element form-select form-select-no-cancel member-home-state" {{ $disabled }} data-label="State">
                                                <option value=""></option>
                                                @foreach($states as $state)
                                                <option value="{{ $state -> state }}" @if($member -> address_home_state == $state -> state) selected @endif>{{ $state -> state }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-12 col-md-6 home-address-div">
                                            <input type="text" class="custom-form-element form-input member-home-zip" {{ $disabled }} value="{{ $member -> address_home_zip }}" data-label="Zip Code">
                                        </div>


                                        <div class="col-12 col-md-6 office-address-div">
                                            <input type="text" class="custom-form-element form-input member-office-street" {{ $disabled }} value="{{ $member -> address_office_street }}" data-label="Office Street Address">
                                        </div>

                                        <div class="col-12 col-md-6 office-address-div">
                                            <input type="text" class="custom-form-element form-input member-office-city" {{ $disabled }} value="{{ $member -> address_office_city }}" data-label="Office City">
                                        </div>

                                        <div class="col-12 col-md-6 office-address-div">
                                            <select class="custom-form-element form-select form-select-no-cancel member-office-state" {{ $disabled }} data-label="Office State">
                                                <option value=""></option>
                                                @foreach($states as $state)
                                                <option value="{{ $state -> state }}" @if($member -> address_office_state == $state -> state) selected @endif>{{ $state -> state }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-12 col-md-6 office-address-div">
                                            <input type="text" class="custom-form-element form-input member-office-zip" {{ $disabled }} value="{{ $member -> address_office_zip }}" data-label="Office Zip Code">
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            @if($disabled == '')
                                            <a href="javascript: void(0)" class="btn btn-lg btn-success save-member-button"><i class="fad fa-save mr-2"></i> Save Details</a>
                                            @endif
                                        </div>
                                    </div>

                                    <input type="hidden" class="member-id" value="{{ $member -> id }}">
                                    <input type="hidden" class="member-crm-contact-id" value="{{ $member -> CRMContact_ID ?? 0 }}">

                                </div>
                            </div>
                        </div>

                        @endforeach

                        <div class="tab-pane animated fadeIn slow mt-0 member-div" id="add_member_div" role="tabpanel" aria-labelledby="add_member_group"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="confirm_delete_member_modal" tabindex="-1" role="dialog" aria-labelledby="delete_member_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary draggable-handle">
                <h4 class="modal-title" id="delete_member_title">Confirm</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="container text-center">Delete Member?</div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-success modal-confirm-button" id="delete_member_button"><i class="fad fa-check mr-2"></i> Confirm</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="import_contact_modal" tabindex="-1" role="dialog" aria-labelledby="import_contact_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary draggable-handle">
                <h4 class="modal-title" id="import_contact_modal_title">Select Contacts</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="table-responsive text-nowrap">
                        <table id="contacts_table" class="table table-hover table-bordered table-sm" width="100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Type</th>
                                    <th>Last</th>
                                    <th>First</th>
                                    <th>Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contacts as $contact)
                                <tr>
                                    <td>
                                        <a href="javascript: void(0)"
                                        class="btn btn-sm btn-primary add-contact-button"
                                        data-contact-id="{{ $contact -> id }}"
                                        data-contact-type-id="{{ $contact -> contact_type_id }}"
                                        data-contact-first="{{ $contact -> contact_first }}"
                                        data-contact-last="{{ $contact -> contact_last }}"
                                        data-contact-company="{{ $contact -> contact_company }}"
                                        data-contact-phone="{{ $contact -> contact_phone_cell }}"
                                        data-contact-email="{{ $contact -> contact_email }}"
                                        data-contact-street="{{ $contact -> contact_street }}"
                                        data-contact-city="{{ $contact -> contact_city }}"
                                        data-contact-state="{{ $contact -> contact_state }}"
                                        data-contact-zip="{{ $contact -> contact_zip }}"
                                        >Import</a>
                                    </td>
                                    <td>{{ $resource_items -> getResourceName($contact -> contact_type_id) }}</td>
                                    <td>{{ $contact -> contact_last }}</td>
                                    <td>{{ $contact -> contact_first }}</td>
                                    <td>{{ $contact -> contact_street.' '.$contact -> contact_city.', '.$contact -> contact_state.' '.$contact -> contact_zip }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-success" id="save_import_contact_button"><i class="fad fa-check mr-2"></i> Add Contact</a>
            </div>
        </div>
    </div>
</div>
