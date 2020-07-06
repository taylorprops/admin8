if (document.URL.match(/transaction_required_details/)) {
// TODO: need to add company, bank, LLC  as seller
    $(document).ready(function () {

        //$('.stepper').mdbStepper();

        form_elements();

        $('.add-member-button').click(add_member);

        $('.member-delete').click(delete_member);

        $('#MLSListDate').focus(function() {
            $('.custom-picker-header').remove();
            $('.picker__box').prepend('<h3-responsive class="py-3 bg-primary text-yellow-light my-0 border-bottom custom-picker-header">List Date</h3>');
        });
        $('#ExpirationDate').focus(function() {
            $('.custom-picker-header').remove();
            $('.picker__box').prepend('<h3-responsive class="py-3 bg-primary text-yellow-light my-0 border-bottom custom-picker-header">Expiration Date</h3>');
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

        $('#continue').click(function(e) {
            e.preventDefault();
            save_transaction_required_details();
        });

        $('#contacts_table').DataTable({
            "aaSorting": [],
                columnDefs: [{
                orderable: false,
                targets: 0
            }]
        });

        $('.import-from-contacts-button').off('click').on('click', function() {
            show_import_modal($(this).data('member'), $(this).data('member-id'));
        });

        $('[name$=_entity_name]').removeClass('required');
        $('.bank-trust').change(show_bank_trust);

    });

    function show_bank_trust() {
        let member = $(this).data('member');
        let field = $(this).closest('.form-ele').next('div').find('.bank-trust-row');
        if($(this).is(':checked')) {
            $(this).closest('.form-ele').next('div').find('[name^='+member+'_]').not('[name^='+member+'_crm]').removeClass('required').parent().find('.required-div').hide();
            field.removeClass('hidden').find('input').addClass('required').parent().find('.required-div').show();
        } else {
            $(this).closest('.form-ele').next('div').find('[name^='+member+'_]').not('[name^='+member+'_crm]').addClass('required').parent().find('.required-div').show();
            field.addClass('hidden').find('input').removeClass('required').parent().find('.required-div').hide();
        }
    }

    function show_import_modal(member, member_id) {
        member_id = member_id - 1;

        $('#import_contact_modal').modal();
        $('#contacts_table').off('click').on('click', '.add-contact-button', function() {
            let member_div = $('.'+member+'-div').eq(member_id);
            member_div.find('[name^='+member+'_first_name]').val($(this).data('contact-first'));
            member_div.find('[name^='+member+'_last_name]').val($(this).data('contact-last'));
            member_div.find('[name^='+member+'_phone]').val($(this).data('contact-phone'));
            member_div.find('[name^='+member+'_email]').val($(this).data('contact-email'));
            member_div.find('[name^='+member+'_street]').val($(this).data('contact-street'));
            member_div.find('[name^='+member+'_city]').val($(this).data('contact-city'));
            member_div.find('[name^='+member+'_state]').val($(this).data('contact-state'));
            member_div.find('[name^='+member+'_zip]').val($(this).data('contact-zip'));
            member_div.find('[name^='+member+'_crm_contact_id]').val($(this).data('contact-id'));

            $('input').trigger('change');
            setTimeout(select_refresh, 500);
            $('#import_contact_modal').modal('hide');
        });
    }

    function save_transaction_required_details() {

        if($('#MLSListDate').val() > $('#ExpirationDate').val()) {
            $('#modal_danger').modal().find('.modal-body').html('List Date must be before Expiration Date');
            return false;
        }

        let form = $('#details_form');
        let validate = validate_form(form);
        if(validate == 'yes') {
            let formData = new FormData(form[0]);
            axios.post('/agents/doc_management/transactions/save_transaction_required_details', formData, axios_options)
            .then(function (response) {
                global_loading_off();
                window.location = '/agents/doc_management/transactions/transaction_details/' + response.data.id + '/' + response.data.type;
            })
            .catch(function (error) {
                //global_loading_off();
                console.log(error);
            });
        } else {
            //global_loading_off();
        }

    }

    function add_member() {


        let member = $(this).data('member'); // seller or buyer
        let type = $(this).data('type'); // listing or contract

        let required = 'required';

        let member_id = $('.'+member+'-div').length + 1;
        let member_div = '';

        if(type == 'listing' || (type == 'contract' && member == 'buyer')) {
            member_div += ' \
            <div class="'+member+'-div mb-3 z-depth-1"> \
                <div class="h5 responsive text-orange '+member+'-header"></div> \
                <div class="d-flex justify-content-between"> \
                    <a href="javascript: void(0)" class="btn btn-sm btn-primary ml-0 import-from-contacts-button" data-member="'+member+'" data-member-id="' + member_id + '"><i class="fad fa-user-friends mr-2"></i> Import from Contacts</a> \
                    <div><a href="javascript: void(0)" class="member-delete text-danger" data-member="'+member+'"><i class="fal fa-times fa-2x"></i></a></div> \
                </div> \
            ';
        } else {
            member_div += ' \
            <div class="'+member+'-div mb-3 z-depth-1"> \
                <div class="d-flex justify-content-between"> \
                    <div class="h5 responsive text-orange '+member+'-header"></div> \
                    <div><a href="javascript: void(0)" class="member-delete text-danger" data-member="'+member+'"><i class="fal fa-times fa-2x"></i></a></div> \
                </div> \
            ';
        }
        member_div += ' \
                <div class="row"> \
                    <div class="col-12 col-md-6 col-lg-3"> \
                        <input type="text" class="custom-form-element form-input required" name="'+member+'_first_name[]" data-label="First Name"> \
                    </div> \
                    <div class="col-12 col-md-6 col-lg-3"> \
                        <input type="text" class="custom-form-element form-input required" name="'+member+'_last_name[]" data-label="Last Name"> \
                    </div> \
        ';

        if(type == 'listing' || (type == 'contract' && member == 'buyer')) {
            member_div += ' \
                    <div class="col-12 col-md-6 col-lg-3"> \
                        <input type="text" class="custom-form-element form-input phone '+required+'" name="'+member+'_phone[]" data-label="Phone"> \
                    </div> \
                    <div class="col-12 col-md-6 col-lg-3"> \
                        <input type="text" class="custom-form-element form-input" name="'+member+'_email[]" data-label="Email"> \
                    </div> \
                </div> \
                <div class="row"> \
                    <div class="col-12 col-md-6 col-lg-5"> \
                        <input type="text" class="custom-form-element form-input '+member+'-street '+required+' street-autocomplete" name="'+member+'_street[]" data-label="Home Address"> \
                        <div class="address-autocomplete-container"><div class="address-autocomplete-div z-depth-1"></div></div> \
                    </div> \
                    <div class="col-12 col-md-6 col-lg-3"> \
                        <input type="text" class="custom-form-element form-input '+member+'-city '+required+'" name="'+member+'_city[]" data-label="City"> \
                    </div> \
                    <div class="col-12 col-md-6 col-lg-2"> \
                        <select class="custom-form-element form-select form-select-no-cancel '+member+'-state '+required+'" name="'+member+'_state[]" data-label="State"> \
                            <option value=""></option> \
            ';

            states.forEach(function(state) {
                member_div += '<option value="' + state.state + '">' + state.state + '</option>';
            });

            member_div += ' \
                        </select> \
                    </div> \
                    <div class="col-12 col-md-6 col-lg-2"> \
                        <input type="text" class="custom-form-element form-input '+member+'-zip '+required+'" name="'+member+'_zip[]" data-label="Zip Code"> \
                    </div> \
                    <input type="hidden" name="'+member+'_crm_contact_id[]"> \
                </div> \
            </div> \
            ';
        } else {
            member_div += '</div>';
        }

        $('.'+member+'-container').append(member_div);
        let count = $('.'+member+'-div').length;
        $('.'+member+'-div').fadeIn('slow').last().find('.'+member+'-header').text((member == 'seller' ? 'Seller' : 'Buyer')+ ' ' + count);
        form_elements();
        if(count == 2) {
            $('.add-member-button[data-member="'+member+'"]').hide();
        } else {
            $('.add-member-button[data-member="'+member+'"]').show();
        }

        $('.member-delete').click(delete_member);

        $('.street-autocomplete').focus(function() {
            let step = $(this).closest('.'+member+'-div');
            if($('.'+member+'-div').eq(0).find('.'+member+'-street').val() != '') {
                let street = $('.'+member+'-div').eq(0).find('.'+member+'-street').val();
                let city = $('.'+member+'-div').eq(0).find('.'+member+'-city').val();
                let state = $('.'+member+'-div').eq(0).find('.'+member+'-state').val();
                let zip = $('.'+member+'-div').eq(0).find('.'+member+'-zip').val();

                let container = $(this).closest('.row');
                container.find('.address-autocomplete-div').show().html('<a href="javascript:void(0)" class="text-primary"> <i class="fa fa-plus mr-2"></i> Copy from '+(member == 'seller' ? 'Seller' : 'Buyer')+' 1 address </a>');

                $(document).on('mousedown', function (e) {
                    if (!$(e.target).is('.address-autocomplete-div *')) {
                        $('.address-autocomplete-div').hide();
                    } else {
                        container.find('.'+member+'-street').val(street).trigger('change');
                        container.find('.'+member+'-city').val(city).trigger('change');
                        container.find('.'+member+'-state').val(state).trigger('change');
                        container.find('.'+member+'-zip').val(zip).trigger('change');
                        select_refresh();
                        $('.address-autocomplete-div').hide();
                    }

                });


            }
        });

        $('.import-from-contacts-button').off('click').on('click', function() {
            show_import_modal($(this).data('member'), $(this).data('member-id'));
        });
    }

    function delete_member() {
        // TODO
        let member = $(this).data('member');
        $(this).closest('.'+member+'-div').fadeOut().remove();
        form_elements();

        let count = $('.'+member+'-div').length;
        $('.'+member+'-div').each(function() {
            let index = $(this).index() + 1;
            $(this).find('.'+member+'-header').text((member == 'seller' ? 'Seller' : 'Buyer') + ' ' + index);
        });
        if(count == 3) {
            $('.add-member-button[data-member="'+member+'"]').hide();
        } else {
            $('.add-member-button[data-member="'+member+'"]').removeClass('hidden').show();
        }
    }

}
