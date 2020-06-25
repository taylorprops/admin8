
<div class="card">
    <div class="card-body">

        <div class="row">
            <div class="col-12 col-md-6">
                <select class="custom-form-element form-select form-select-no-search form-select-no-cancel member-type-id required" data-label="Member Type">
                    <option value=""></option>
                    @foreach($contact_types as $contact_type)
                    <option value="{{ $contact_type -> resource_id }}">{{ $contact_type -> resource_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6 d-flex align-items-center">
                <a href="javascript: void(0)" class="btn btn-sm btn-primary import-contact-button" data-ele="#add_member_div"><i class="fad fa-cloud-download-alt mr-2"></i> Import Contact</a>
                <a href="javascript: void(0)" class="btn btn-sm btn-danger cancel-add-member-button"><i class="fad fa-trash mr-2"></i> Cancel</a>
            </div>
            <div class="col-12 bank-trust-div">
                <input type="checkbox" class="custom-form-element form-checkbox bank-trust" data-member="buyer" data-label="Buyer is a Trust, Company or other Entity">
            </div>
            <div class="col-12 member-entity-name-div">
                <input type="text" class="custom-form-element form-input member-entity-name" data-label="Trust, Company or other Entity Name">
            </div>
            <div class="col-12 col-md-6">
                <input type="text" class="custom-form-element form-input member-first-name" data-label="First Name">
            </div>
            <div class="col-12 col-md-6">
                <input type="text" class="custom-form-element form-input member-last-name" data-label="Last Name">
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6">
                <input type="text" class="custom-form-element form-input member-company" data-label="Company">
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6">
                <input type="text" class="custom-form-element form-input phone member-phone" data-label="Phone">
            </div>
            <div class="col-12 col-md-6">
                <input type="text" class="custom-form-element form-input member-email" data-label="Email">
            </div>
            <div class="col-12 col-md-6">
                <input type="text" class="custom-form-element form-input member-street" data-label="Home Address">
            </div>
            <div class="col-12 col-md-6">
                <input type="text" class="custom-form-element form-input member-city" data-label="City">
            </div>
            <div class="col-12 col-md-6">
                <select class="custom-form-element form-select form-select-no-cancel member-state" data-label="State">
                    <option value=""></option>
                    @foreach($states as $state)
                    <option value="{{ $state -> state }}">{{ $state -> state }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6">
                <input type="text" class="custom-form-element form-input member-zip" data-label="Zip Code">
            </div>
            <input type="hidden" class="member-crm-contact-id">
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <a href="javascript: void(0)" class="btn btn-lg btn-success save-member-button"><i class="fad fa-save mr-2"></i> Save Details</a>
            </div>
        </div>

    </div>
</div>

