if (document.URL.match(/listing_details/)) {

    $(document).ready(function () {

        load_tabs('');

        $('#open_members_tab').one('click', function () {
            load_tabs('members');
        });
        $('#open_checklist_tab').one('click', function () {
            load_tabs('checklist');
        });
        $('#open_docs_tab').one('click', function () {
            load_tabs('docs');
        });
        $('#open_contracts_tab').one('click', function () {
            load_tabs('contracts');
        });
        $('#open_commission_tab').one('click', function () {
            load_tabs('commission');
        });

        load_details_header($('#Listing_ID').val());

    });

    function show_import_modal(member_id) {

        $('#import_contact_modal').modal();

        $('#contacts_table').on('click', '.add-contact-button', function() {

            let member_div = $('#member_div_'+member_id);
            member_div.find('.member-first-name').val($(this).data('contact-first'));
            member_div.find('.member-last-name').val($(this).data('contact-last'));
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
            toastr['success']('Contact Imported');
        });
    }

    function save_details() {
        let form = $('#listing_details_form');
        let formData = new FormData(form[0]);
        formData.append('Listing_ID', $('#Listing_ID').val());
        axios.post('/agents/doc_management/transactions/listings/save_details', formData, axios_options)
        .then(function (response) {
            if(response.data.status == 'ok') {
                load_tabs('details');
                load_details_header($('#Listing_ID').val());
                toastr['success']('Listing Details Saved!');
            }
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    function save_search_mls() {

        $('#confirm_import_modal').modal('hide');

        let ListingId = $('#ListingId').val();
        let Listing_ID = $('#Listing_ID').val();
        axios.get('/agents/doc_management/transactions/listings/save_mls_search', {
            params: {
                ListingId: ListingId,
                Listing_ID: Listing_ID
            }
        })
        .then(function (response) {
            if(response.data.status == 'ok') {

                $('#modal_success').modal().find('.modal-body').html('BrightMLS Data Successfully Imported');
                load_tabs('details');
                load_details_header(Listing_ID);

            }
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    function search_mls() {
        let ListingId = $('#ListingId').val();
        let Listing_ID = $('#Listing_ID').val();
        axios.get('/agents/doc_management/transactions/listings/mls_search', {
            params: {
                ListingId: ListingId,
                Listing_ID: Listing_ID
            }
        })
        .then(function (response) {

            if(response.data.status == 'ok') {

                let search_response = '';
                if(response.data.county_match == 'yes') {
                    search_response = 'We have located the property in BrightMLS. Details below.<hr>';
                } else {
                    search_response = 'We have located the property in BrightMLS, however the property is in a different County than the previous County entered.<br><br><i class="fad fa-exclamation-triangle mr-2"></i> The checklist will be replaced to include the forms required for the new location. Any relevant forms will be kept in the checklist but some may need to be added or replaced<hr>';
                }

                let listing_details = ' \
                    <div class="row"> \
                        <div class="col-12 col-md-5"> \
                            <img src="'+response.data.picture_url+'" class="confirm-import-image"> \
                        </div> \
                        <div class="col-12 col-md-7"> \
                            Listed By '+response.data.list_company+'<br> \
                            '+response.data.address+'<br> \
                            '+response.data.city+', '+response.data.state+' '+response.data.zip+'<br> \
                        </div> \
                    </div> \
                ';

                $('#confirm_import_modal').modal().find('.modal-body').html(search_response + listing_details);

                $('#confirm_import_button').click(save_search_mls);

            } else if(response.data.status == 'not found') {

                $('#modal_danger').modal().find('.modal-body').html('<div class="h4 text-danger w-100 text-center"><i class="fad fa-exclamation-triangle mr-2"></i> Listing not found in BrightMLS</div>');

            }
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    function load_details_header(Listing_ID) {
        axios.get('/agents/doc_management/transactions/listings/get_details_header', {
            params: {
                Listing_ID: Listing_ID
            },
            headers: axios_headers_html
        })
        .then(function (response) {
            $('#details_header').html(response.data);
        })
        .catch(function (error) {
            console.log(error);
        });
    }


    function load_tabs(tab) {
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

                $('#search_mls_button').click(search_mls);

                $('.save-details-button').click(save_details);
            } else if(tab == 'members') {
                $('.import-contact-button').off('click').on('click', function() {
                    show_import_modal($(this).data('member-id'));
                });
                $('#contacts_table').DataTable({
                    "aaSorting": [],
                        columnDefs: [{
                        orderable: false,
                        targets: 0
                    }]
                });
            }

            // init tooltips and form elements
            global_tooltip();
            setTimeout(form_elements, 500);

        })
        .catch(function (error) {
            console.log(error);
        });
    }

    function update_county_select() {
        let state = $('#StateOrProvince').val();
        axios.get('/agents/doc_management/transactions/update_county_select', {
            params: {
                state: state
            },
        })
            .then(function (response) {
                let counties = response.data;
                $('#County').html('').prop('disabled', false);
                $('#County').append('<option value=""></option>');
                $.each(counties, function (k, v) {
                    $('#County').append('<option value="' + v.county.toUpperCase() + '">' + v.county + '</option>');
                });
                setTimeout(function () {
                    select_refresh();
                    if (state == 'DC') {
                        $('#County').val('DISTRICT OF COLUMBIA');
                        select_refresh();
                    }
                }, 500);
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    function format_money() {

        $('#ListPrice').val('$' + global_format_number($('#ListPrice').val().replace('/\$/', '')));

    }

}
