@extends('layouts.main')
@section('title', 'Required Details')

@section('content')

<script>
    let states = JSON.parse({!!json_encode($states_json)!!});
</script>
<div class="container page-required-details">
    <div class="row">
        <div class="col-12">

            <div class="h3 text-orange mt-3 mt-sm-4 text-center w-100">{{ $property_details -> FullStreetAddress }} {{ $property_details -> City.', '.$property_details -> StateOrProvince.', '.$property_details -> PostalCode }}</div>

            <div class="steps-container mx-auto">
                <form id="steps_form" autocomplete="off">
                    <input autocomplete="false" name="hidden" type="text" style="display:none;">
                    <div class="h4 text-primary mt-3 text-center">Please Enter the following details</div>
                    <ul class="stepper linear mt-2 pt-1">
                        <li class="step active">
                            <div class="h4 step-title waves-effect waves-light text-gray">Seller(s)</div>
                            <div class="step-new-content mt-3">
                                <div class="row">
                                    <div class="col-12 seller-container">

                                        <div class="seller-div mb-3 border-bottom">
                                            <div class="h5 text-orange seller-header">Seller 1</div>
                                            <a href="javascript: void(0)" class="btn btn-sm btn-primary ml-0 import-from-contacts-button" data-seller-id="1"><i class="fad fa-user-friends mr-2"></i> Import from Contacts</a>
                                            <div class="row">
                                                <div class="col-12 col-md-6 col-lg-3">
                                                    <input type="text" class="custom-form-element form-input required" name="seller_first_name[]" data-label="First Name">
                                                </div>
                                                <div class="col-12 col-md-6 col-lg-3">
                                                    <input type="text" class="custom-form-element form-input required" name="seller_last_name[]" data-label="Last Name">
                                                </div>
                                                <div class="col-12 col-md-6 col-lg-3">
                                                    <input type="text" class="custom-form-element form-input phone required" name="seller_phone[]" data-label="Phone">
                                                </div>
                                                <div class="col-12 col-md-6 col-lg-3">
                                                    <input type="text" class="custom-form-element form-input" name="seller_email[]" data-label="Email">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-md-6 col-lg-5">
                                                    <input type="text" class="custom-form-element form-input seller-street required" name="seller_street[]" data-label="Home Address">
                                                </div>
                                                <div class="col-12 col-md-6 col-lg-3">
                                                    <input type="text" class="custom-form-element form-input seller-city required" name="seller_city[]" data-label="City">
                                                </div>
                                                <div class="col-12 col-md-6 col-lg-2">
                                                    <select class="custom-form-element form-select seller-state required" name="seller_state[]" data-label="State">
                                                        <option value=""></option>
                                                        @foreach($states as $state)
                                                        <option value="{{ $state -> state }}">{{ $state -> state }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-12 col-md-6 col-lg-2">
                                                    <input type="text" class="custom-form-element form-input seller-zip required" name="seller_zip[]" data-label="Zip Code">
                                                </div>
                                                <input type="hidden" name="seller_crm_contact_id[]">
                                            </div>
                                        </div> {{-- end seller-div --}}

                                    </div>

                                    <div class="col-12">
                                        <a href="javascript: void(0);" class="btn btn-sm btn-success float-right" id="add_seller_button"><i class="fa fa-plus mr-2"></i> Add Seller</a>
                                    </div>

                                </div>
                                <div class="step-actions">
                                    <button class="waves-effect waves-light btn btn-success next-step">CONTINUE <i class="fad fa-chevron-double-right ml-2"></i></button>
                                </div>
                            </div>
                        </li>
                        <li class="step">
                            <div class="h4 step-title waves-effect waves-light text-gray">Dates</div>
                            <div class="step-new-content mt-3">
                                <div class="row">
                                    <div class="col-12 col-md-6 col-lg-3">
                                        <input type="text" class="custom-form-element form-input datepicker required" name="MLSListDate" id="MLSListDate" data-label="List Date" value="{{ $property_details -> MLSListDate }}">
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3">
                                        <input type="text" class="custom-form-element form-input datepicker required" name="ExpirationDate" id="ExpirationDate" data-label="Expiration Date" value="{{ $property_details -> ExpirationDate }}">
                                    </div>
                                </div>
                                <div class="step-actions">
                                    <button class="waves-effect waves-light btn btn-secondary previous-step"><i class="fad fa-chevron-double-left mr-2"></i> BACK</button>
                                    <button class="waves-effect waves-light btn btn-success next-step">CONTINUE <i class="fad fa-chevron-double-right ml-2"></i></button>
                                </div>
                            </div>
                        </li>
                        <li class="step">
                            <div class="h4 step-title waves-effect waves-light text-gray">Status</div>
                            <div class="step-new-content mt-3">
                                <div class="row">
                                    <div class="col-12 col-md-6 col-lg-3">
                                        <select class="custom-form-element form-select form-select-no-search form-select-no-cancel required" name="Status" id="Status" data-label="Status">
                                            <option value=""></option>
                                            @foreach($statuses as $status)
                                            <option value="{{ $status -> resource_id }}">{{ $status -> resource_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="step-actions">
                                    <button class="waves-effect waves-light btn btn-secondary previous-step"><i class="fad fa-chevron-double-left mr-2"></i> BACK</button>
                                    <button class="waves-effect waves-light btn btn-success next-step">CONTINUE <i class="fad fa-chevron-double-right ml-2"></i></button>
                                </div>
                            </div>
                        </li>

                        <li class="step">
                            <div class="h4 step-title waves-effect waves-light text-gray">Complete!</div>
                            <div class="step-new-content mt-3">
                                <div class="step-actions">
                                    <div class="d-flex justify-content-center w-100 h-100">
                                        <button class="waves-effect waves-light btn btn-secondary previous-step"><i class="fad fa-chevron-double-left mr-2"></i> BACK</button>
                                        <button id="steps_submit" class="waves-effect waves-light btn btn-lg btn-success">CONTINUE <i class="fad fa-chevron-double-right ml-2"></i></button>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <input type="hidden" name="Listing_ID" value="{{ $property_details -> Listing_ID }}">
                    <input type="hidden" name="Agent_ID" value="{{ $property_details -> Agent_ID }}">

                </form>
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

</div>
@endsection
