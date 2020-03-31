if (document.URL.match(/listing_details/)) {

    $(document).ready(function () {

        load_tabs('');

        $('#open_members_tab').one('click', function () {
            load_tabs('members');
        });
        $('#open_checklist_tab').one('click', function () {
            load_tabs('checklist');
        });
        $('#open_documents_tab').one('click', function () {
            load_tabs('documents');
        });
        $('#open_contracts_tab').one('click', function () {
            load_tabs('contracts');
        });
        $('#open_commission_tab').one('click', function () {
            load_tabs('commission');
        });

        load_details_header($('#Listing_ID').val());

    });

    window.load_details_header = function(Listing_ID) {
        axios.get('/agents/doc_management/transactions/listings/listing_details_header', {
            params: {
                Listing_ID: Listing_ID
            },
            headers: axios_headers_html
        })
        .then(function (response) {
            $('#details_header').html(response.data);
            setTimeout(function() {
                $('#Status').change(update_status);
            }, 500);
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.load_tabs = function(tab) {
        let Listing_ID = $('#Listing_ID').val();
        if (tab == '') {
            tab = 'details';
        }
        axios.get('/agents/doc_management/transactions/listings/get_' + tab, {
            params: {
                Listing_ID: Listing_ID
            },
            headers: axios_headers_html
        })
        .then(function (response) {
            $('#' + tab + '_tab').html(response.data);

            if (tab == 'details') {
                // update counties when state is changed
                $('#StateOrProvince').change(update_county_select);
                // date pickers
                $('.datepicker').pickadate({
                    format: 'yyyy-mm-dd',
                    formatSubmit: 'yyyy-mm-dd',
                });
                // format list price
                format_money();
                $('#ListPrice').keyup(function () {
                    format_money();
                    $('#list_price_display').text($(this).val());
                });

                // open checklist tab shortcut from helper
                $(document).on('click', '#open_checklist_button', function() {
                    $('#open_checklist_tab').trigger('click');
                });

                $('#search_mls_button').off('click').on('click', search_mls);

                $('.save-details-button').off('click').on('click', save_details);
            } else if(tab == 'members') {
                $(document).on('click', '.import-contact-button', function() {
                    show_import_modal($(this).data('ele'));
                });
                $('#contacts_table').DataTable({
                    "aaSorting": [],
                        columnDefs: [{
                        orderable: false,
                        targets: 0
                    }]
                });
                $('#add_member_button').off('click').on('click', show_add_member);
                //$('.save-member-div').off('click').on('click', '.save-member-button', save_add_member);
                $('.delete-member-button').off('click').on('click', confirm_delete_member);
            }

            // init tooltips and form elements
            global_tooltip();
            /* if(tab != 'details') {
                setTimeout(function() {
                    scrollToAnchor('scroll_to');
                }, 500);
            } */
            setTimeout(form_elements, 500);

        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.update_status = function() {
        let Status = $('#Status').val();
        let Listing_ID = $('#Listing_ID').val();
        let formData = new FormData();
        formData.append('Status', Status);
        formData.append('Listing_ID', Listing_ID);
        axios.post('/agents/doc_management/transactions/listings/update_status', formData, axios_options)
        .then(function (response) {
            toastr['success']('Status Successfully Updated');
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.format_money = function() {

        $('#ListPrice').val('$' + global_format_number($('#ListPrice').val().replace('/\$/', '')));

    }



}
