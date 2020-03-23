<div class="container mt-0">
    <div class="row">
        <div class="col-12 col-md-9 mx-auto">
            <div class="row members-list-group">
                <div class="col-12 col-md-4">
                    <a href="javascript: void(0)" id="add_contact_button" class="btn btn-success"><i fa fa-plus mr-2"></i> Add Contact</a>
                    <div class="list-group my-3" id="members_tab" role="tablist">
                        @foreach($members as $member)
                        <a class="list-group-item list-group-item-action @if($loop -> first) active @endif" id="member_{{ $member -> id }}_item" data-toggle="list" href="#member_{{ $member -> id }}_div" role="tab">{{ $resource_items -> GetTagName($member -> member_type_id) }} - @if($member -> first_name) {{ $member -> first_name . ' ' . $member -> last_name }} @else {{ $member -> company }} @endif</a>
                        @endforeach
                    </div>
                </div>
                <div class="col-12 col-md-8">
                    <div class="tab-content get-members-tabs pt-2" id="members_tab_div">

                        @foreach($members as $member)

                        <div class="tab-pane fade mt-0 @if($loop -> first) show active @endif member-div" id="member_{{ $member -> id }}_div" role="tabpanel" aria-labelledby="member_{{ $member -> id }}_item">
                            <div class="h3-responsive text-orange mb-2">@if($member -> first_name) {{ $member -> first_name . ' ' . $member -> last_name }} @else {{ $member -> company }} @endif</div>
                            <div class="card" id="member_div_{{ $member -> id }}">
                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <select class="custom-form-element form-select form-select-no-search form-select-no-cancel contact-type required" data-label="Contact Type">
                                                <option value=""></option>
                                                @foreach($contact_types as $contact_type)
                                                <option value="{{ $contact_type -> resource_id }}" @if($contact_type -> resource_id == $member -> member_type_id) selected @endif>{{ $contact_type -> resource_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6 d-flex align-items-center">
                                            <a href="javascript: void(0)" class="btn btn-sm btn-primary import-contact-button" data-member-id="{{ $member -> id }}"><i class="fad fa-cloud-download-alt mr-2"></i> Import Contact</a>
                                            <a href="javascript: void(0)" class="btn btn-sm btn-danger delete-contact-button" data-member-id="{{ $member -> id }}"><i class="fad fa-trash mr-2"></i> Delete Member</a>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <input type="text" class="custom-form-element form-input member-first-name required" value="{{ $member -> first_name }}" data-label="First Name">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <input type="text" class="custom-form-element form-input member-last-name required" value="{{ $member -> last_name }}" data-label="Last Name">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <input type="text" class="custom-form-element form-input phone member-phone required" value="{{ $member -> cell_phone }}" data-label="Phone">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <input type="text" class="custom-form-element form-input member-email" value="{{ $member -> email }}" data-label="Email">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <input type="text" class="custom-form-element form-input member-street required" value="{{ $member -> address_street }}" data-label="Home Address">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <input type="text" class="custom-form-element form-input member-city required" value="{{ $member -> address_city }}" data-label="City">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <select class="custom-form-element form-select form-select-no-cancel member-state required" data-label="State">
                                                <option value=""></option>
                                                @foreach($states as $state)
                                                <option value="{{ $state -> state }}" @if($member -> address_state == $state -> state) selected @endif>{{ $state -> state }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <input type="text" class="custom-form-element form-input member-zip required" value="{{ $member -> address_zip }}" data-label="Zip Code">
                                        </div>
                                        <input type="hidden" class="member-crm-contact-id" value="{{ $member -> CRMContact_ID }}">
                                    </div>
                                    <div class="row">
                                        <div class="col-12 text-center">
                                            <a href="javascript: void(0)" class="btn btn-lg btn-success save-member-button"><i class="fad fa-save mr-2"></i> Save Details</a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="import_contact_modal" tabindex="-1" role="dialog" aria-labelledby="import_contact_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary draggable-handle">
                <h3 class="modal-title" id="import_contact_modal_title">Select Contacts</h3>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times fa-2x"></i>
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
                                        data-contact-first="{{ $contact -> contact_first }}"
                                        data-contact-last="{{ $contact -> contact_last }}"
                                        data-contact-phone="{{ $contact -> contact_phone_cell }}"
                                        data-contact-email="{{ $contact -> contact_email }}"
                                        data-contact-street="{{ $contact -> contact_street }}"
                                        data-contact-city="{{ $contact -> contact_city }}"
                                        data-contact-state="{{ $contact -> contact_state }}"
                                        data-contact-zip="{{ $contact -> contact_zip }}"
                                        >Import</a>
                                    </td>
                                    <td>{{ $contact -> contact_type }}</td>
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
