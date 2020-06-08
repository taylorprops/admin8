if (document.URL.match(/listing_details/)) {

    $(document).ready(function() {
        $(document).on('click', '.save-member-button', save_add_member);
    });

    window.save_add_member = function() {
        let form = $(this).closest('.member-div');
        let validate = validate_form(form);

        if(validate == 'yes') {

            let formData = new FormData();

            formData.append('id', form.find('.member-id').val());
            formData.append('Listing_ID', $('#Listing_ID').val());
            formData.append('Agent_ID', $('#Agent_ID').val());
            formData.append('member_type_id', form.find('.member-type-id').val());
            formData.append('first_name', form.find('.member-first-name').val());
            formData.append('last_name', form.find('.member-last-name').val());
            formData.append('company', form.find('.member-company').val());
            formData.append('cell_phone', form.find('.member-phone').val());
            formData.append('email', form.find('.member-email').val());
            formData.append('address_street', form.find('.member-street').val());
            formData.append('address_city', form.find('.member-city').val());
            formData.append('address_state', form.find('.member-state').val());
            formData.append('address_zip', form.find('.member-zip').val());
            formData.append('CRMContact_ID', form.find('.member-crm-contact-id').val());

            axios.post('/agents/doc_management/transactions/listings/save_member', formData, axios_options)
            .then(function (response) {
                reload_member_tab();
                toastr['success']('Member Successfully Added');
                setTimeout(function() {
                    scrollToAnchor('scroll_to');
                    load_tabs('documents');
                    load_tabs('checklist');
                }, 500);
            })
            .catch(function (error) {
                console.log(error);
            });
        }

    }

    window.show_add_member = function() {
        axios.get('/agents/doc_management/transactions/listings/add_member_html', {
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {
            // show add button in list group
            $('#add_member_group').show().trigger('click');
            // fill add member div with form
            $('#add_member_div').html(response.data);
            global_tooltip();
            load_details_header($('#Listing_ID').val());
            setTimeout(function() {
                form_elements();
                scrollToAnchor('scroll_to');
            }, 500);
            $('.cancel-add-member-button').off('click').on('click', function() {
                $('#add_member_group').hide();
                $('.list-group-item-member').eq(0).trigger('click');
            });
            $('.list-group-item-member').click(function() {
                $('#add_member_group').hide();
            });

        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.reload_member_tab = function() {
        let active_tab = $('.list-group-item-member.active').eq();
        load_tabs('members');
        $('.list-group-item-member.active').eq(active_tab).trigger('click');
    }

    window.confirm_delete_member = function() {
        let id = $(this).data('member-id');
        $('#confirm_delete_member_modal').modal();
        $('#delete_member_button').off('click').on('click', function() {
            delete_member(id);
            $('#confirm_delete_member_modal').modal('hide');
        });
    }

    window.delete_member = function(id) {
        let formData = new FormData();
        formData.append('id', id);
        formData.append('Listing_ID', $('#Listing_ID').val());
        axios.post('/agents/doc_management/transactions/listings/delete_member', formData, axios_options)
        .then(function (response) {
            reload_member_tab();
            load_details_header($('#Listing_ID').val());
            toastr['success']('Member Successfully Deleted');
            setTimeout(function() {
                scrollToAnchor('scroll_to');
            }, 500);
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.show_import_modal = function(member_div) {
        member_div = $(member_div);
        $('#import_contact_modal').modal();

        $('#contacts_table').off('click').on('click', '.add-contact-button', function() {
            if($(this).data('contact-type-id')) {
                member_div.find('.member-type-id').val($(this).data('contact-type-id'));
            }
            member_div.find('.member-first-name').val($(this).data('contact-first'));
            member_div.find('.member-last-name').val($(this).data('contact-last'));
            member_div.find('.member-company').val($(this).data('contact-company'));
            member_div.find('.member-phone').val($(this).data('contact-phone'));
            member_div.find('.member-email').val($(this).data('contact-email'));
            member_div.find('.member-street').val($(this).data('contact-street'));
            member_div.find('.member-city').val($(this).data('contact-city'));
            member_div.find('.member-state').val($(this).data('contact-state'));
            member_div.find('.member-zip').val($(this).data('contact-zip'));
            member_div.find('.member-crm-contact-id').val($(this).data('contact-id'));

            $('input').trigger('change');
            setTimeout(select_refresh, 500);
            $('#import_contact_modal').modal('hide');

            setTimeout(function() {
                scrollToAnchor('scroll_to');
            }, 500);
        });
    }

}
