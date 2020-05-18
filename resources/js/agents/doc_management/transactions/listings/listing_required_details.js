if (document.URL.match(/listing_required_details/)) {

    $(document).ready(function () {

        $('.stepper').mdbStepper();

        form_elements();

        $('#add_seller_button').click(add_seller);

        $('#MLSListDate').focus(function() {
            $('.custom-picker-header').remove();
            $('.picker__box').prepend('<h3 class="py-3 bg-primary text-yellow-light my-0 border-bottom custom-picker-header">List Date</h3>');
        });
        $('#ExpirationDate').focus(function() {
            $('.custom-picker-header').remove();
            $('.picker__box').prepend('<h3 class="py-3 bg-primary text-yellow-light my-0 border-bottom custom-picker-header">Expiration Date</h3>');
        });

        // disable opening steps unless complete
        $('.step-title').click(function(e) {
            e.stopPropagation();
        });
        // validate section
        $('.next-step').off('click').on('click', function(e) {
            e.preventDefault();
            let step = $(this).closest('.step');
            let validate = validate_form(step);
            if(validate == 'no') {
                e.stopPropagation();
            }
        });

        $('#steps_submit').click(save_listing_required_details);

        $('#contacts_table').DataTable({
            "aaSorting": [],
                columnDefs: [{
                orderable: false,
                targets: 0
            }]
        });

        $('.import-from-contacts-button').off('click').on('click', function() {
            show_import_modal($(this).data('seller-id'));
        });

    });

    function show_import_modal(seller_id) {
        seller_id = seller_id - 1;
        $('#import_contact_modal').modal();
        $('#contacts_table').off('click').on('click', '.add-contact-button', function() {
            let seller_div = $('.seller-div').eq(seller_id);
            console.log(seller_div.length);
            seller_div.find('[name^=seller_first_name]').val($(this).data('contact-first'));
            seller_div.find('[name^=seller_last_name]').val($(this).data('contact-last'));
            seller_div.find('[name^=seller_phone]').val($(this).data('contact-phone'));
            seller_div.find('[name^=seller_email]').val($(this).data('contact-email'));
            seller_div.find('[name^=seller_street]').val($(this).data('contact-street'));
            seller_div.find('[name^=seller_city]').val($(this).data('contact-city'));
            seller_div.find('[name^=seller_state]').val($(this).data('contact-state'));
            seller_div.find('[name^=seller_zip]').val($(this).data('contact-zip'));
            seller_div.find('[name^=seller_crm_contact_id]').val($(this).data('contact-id'));

            $('input').trigger('change');
            setTimeout(select_refresh, 500);
            $('#import_contact_modal').modal('hide');
        });
    }

    function save_listing_required_details() {

        let form = $('#steps_form');
        let formData = new FormData(form[0]);
        axios.post('/agents/doc_management/transactions/save_listing_required_details', formData, axios_options)
        .then(function (response) {
            window.location = '/agents/doc_management/transactions/listings/listing_details/' + response.data;
        })
        .catch(function (error) {
            console.log(error);
        });

    }

    function add_seller() {
        let seller_id = $('.seller-div').length + 1;
        let seller_div = ' \
        <div class="seller-div border-bottom mb-3 hidden"> \
            <div class="d-flex justify-content-between"> \
                <div class="h5 responsive text-orange seller-header">Seller 1</div> \
                <div><a href="javascript: void(0)" class="seller-delete text-danger"><i class="fal fa-times fa-2x"></i></a></div> \
            </div> \
            <a href="javascript: void(0)" class="btn btn-sm btn-primary ml-0 import-from-contacts-button" data-seller-id="' + seller_id + '"><i class="fad fa-user-friends mr-2"></i> Import from Contacts</a> \
            <div class="row"> \
                <div class="col-12 col-md-6 col-lg-3"> \
                    <input type="text" class="custom-form-element form-input required" name="seller_first_name[]" data-label="First Name"> \
                </div> \
                <div class="col-12 col-md-6 col-lg-3"> \
                    <input type="text" class="custom-form-element form-input required" name="seller_last_name[]" data-label="Last Name"> \
                </div> \
                <div class="col-12 col-md-6 col-lg-3"> \
                    <input type="text" class="custom-form-element form-input phone required" name="seller_phone[]" data-label="Phone"> \
                </div> \
                <div class="col-12 col-md-6 col-lg-3"> \
                    <input type="text" class="custom-form-element form-input" name="seller_email[]" data-label="Email"> \
                </div> \
            </div> \
            <div class="row"> \
                <div class="col-12 col-md-6 col-lg-5"> \
                    <input type="text" class="custom-form-element form-input seller-street required street-autocomplete" name="seller_street[]" data-label="Home Address"> \
                    <div class="address-autocomplete-container"><div class="address-autocomplete-div z-depth-1"></div></div> \
                </div> \
                <div class="col-12 col-md-6 col-lg-3"> \
                    <input type="text" class="custom-form-element form-input seller-city required" name="seller_city[]" data-label="City"> \
                </div> \
                <div class="col-12 col-md-6 col-lg-2"> \
                    <select class="custom-form-element form-select form-select-no-cancel seller-state required" name="seller_state[]" data-label="State"> \
                        <option value=""></option> \
        ';

        states.forEach(function(state) {
            seller_div += '<option value="' + state.state + '">' + state.state + '</option>';
        });

        seller_div += ' \
                    </select> \
                </div> \
                <div class="col-12 col-md-6 col-lg-2"> \
                    <input type="text" class="custom-form-element form-input seller-zip required" name="seller_zip[]" data-label="Zip Code"> \
                </div> \
                <input type="hidden" name="seller_crm_contact_id[]"> \
            </div> \
        </div> \
        ';
        $('.seller-container').append(seller_div);
        let count = $('.seller-div').length;
        $('.seller-div').fadeIn('slow').last().find('.seller-header').text('Seller ' + count);
        form_elements();
        if(count == 2) {
            $('#add_seller_button').hide();
        } else {
            $('#add_seller_button').show();
        }

        $('.seller-delete').click(delete_seller);

        $('.street-autocomplete').focus(function() {
            let step = $(this).closest('.step');
            if($('.step').eq(0).find('.seller-street').val() != '') {
                let street = $('.step').eq(0).find('.seller-street').val();
                let city = $('.step').eq(0).find('.seller-city').val();
                let state = $('.step').eq(0).find('.seller-state').val();
                let zip = $('.step').eq(0).find('.seller-zip').val();

                let container = $(this).closest('.row');
                container.find('.address-autocomplete-div').show().html('<a href="javascript:void(0)" class="text-primary"> <i class="fa fa-plus mr-2"></i> Copy from Seller 1 address </a>');

                $(document).on('mousedown', function (e) {
                    if (!$(e.target).is('.address-autocomplete-div *')) {
                        $('.address-autocomplete-div').hide();
                    } else {
                        container.find('.seller-street').val(street).trigger('change');
                        container.find('.seller-city').val(city).trigger('change');
                        container.find('.seller-state').val(state).trigger('change');
                        container.find('.seller-zip').val(zip).trigger('change');
                        select_refresh();
                        $('.address-autocomplete-div').hide();
                    }

                });


            }
        });

        $('.import-from-contacts-button').off('click').on('click', function() {
            show_import_modal($(this).data('seller-id'));
        });
    }

    function delete_seller() {
        $(this).closest('.seller-div').fadeOut().remove();
        form_elements();

        let count = $('.seller-div').length;
        $('.seller-div').each(function() {
            let index = $(this).index() + 1;
            $(this).find('.seller-header').text('Seller ' + index);
        });
        if(count == 3) {
            $('#add_seller_button').hide();
        } else {
            $('#add_seller_button').show();
        }
    }

}
