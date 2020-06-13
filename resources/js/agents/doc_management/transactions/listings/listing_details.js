
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

    window.load_details_header = function (Listing_ID) {
        axios.get('/agents/doc_management/transactions/listings/listing_details_header', {
            params: {
                Listing_ID: Listing_ID
            },
            headers: axios_headers_html
        })
            .then(function (response) {
                $('#details_header').html(response.data);
                $('[data-toggle="popover"]').popover( { placement: 'bottom' });
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    window.load_tabs = function (tab) {
        let Listing_ID = $('#Listing_ID').val();
        let Agent_ID = $('#Agent_ID').val();
        if (tab == '') {
            tab = 'details';
        }
        axios.get('/agents/doc_management/transactions/listings/get_' + tab, {
            params: {
                Listing_ID: Listing_ID,
                Agent_ID: Agent_ID
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
                    $(document).on('click', '#open_checklist_button', function () {
                        $('#open_checklist_tab').trigger('click');
                    });

                    $('#search_mls_button').off('click').on('click', search_mls);

                    $('.save-details-button').off('click').on('click', save_details);

                } else if (tab == 'members') {

                    $(document).on('click', '.import-contact-button', function () {
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

                } else if (tab == 'documents') {

                    setTimeout(function () {
                        $('.check-all').next('label').css({ transform: 'scale(1.2)' });
                        select_form_group();
                        $('#add_documents_div').on('show.bs.collapse', function () {
                            $('#bulk_options_div').collapse('hide');
                            $('.check-document, .check-all').prop('checked', false).closest('.document-div').removeClass('bg-blue-light');
                        });
                        sortable_documents();

                        $('.dropdown-submenu .dropdown-item').on('click', function (e) {
                            e.stopPropagation();
                            e.preventDefault();
                            $(this).next('.dropdown-menu').toggle();
                        });

                        $('.form-in-use').each(function () {
                            $(this).find('.individual-template-form').prop('disabled', true);
                        });

                        reorder_documents('yes');

                    }, 1000);

                } else if (tab == 'checklist') {

                    /* setTimeout(function() {
                        $('.save-notes-button').off().on('click', function() {
                            save_add_notes($(this));
                        });
                    }, 200); */

                    $('.notes-collapse').on('show.bs.collapse', function () {
                        $('.documents-collapse.show').collapse('hide');
                        //$('.checklist-item-div').removeClass('bg-green-light');
                        $(this).closest('.checklist-item-div').addClass('bg-green-light');
                    });
                    $('.documents-collapse').on('show.bs.collapse', function () {
                        $('.notes-collapse.show').collapse('hide');
                        //$('.checklist-item-div').removeClass('bg-green-light');
                        $(this).closest('.checklist-item-div').addClass('bg-green-light');
                    });

                    $('.collapse').on('hide.bs.collapse', function () {
                        $(this).closest('.checklist-item-div').removeClass('bg-green-light');
                    });

                    $('.listing-option-trigger').off('change').on('change', listing_options);

                    listing_options();

                }

                // init tooltips and form elements
                global_tooltip();

                $('.draggable').draggable({
                    handle: '.draggable-handle'
                });

                setTimeout(form_elements, 500);

                $('.modal-backdrop').remove();

            })
            .catch(function (error) {
                console.log(error);
            });
    }

    function sortable_documents() {
        $('.sortable-documents').sortable({
            placeholder: 'bg-sortable',
            handle: '.document-handle',
            stop: function (event, ui) {
                reorder_documents('no');
            }

        });
        $('.sortable-documents').disableSelection();
    }

    window.reorder_documents = function(on_load) {

        $('.sortable-documents').each(function() {
            let els = $(this).find('.document-div');

            let documents = {
                document: []
            }
            let c = 0;
            let stop = els.length - 1;
            els.each(function () {
                let el, folder_id, document_id, document_index;
                el = $(this);
                folder_id = el.data('folder-id');
                document_id = el.data('document-id');
                document_index = el.index();
                documents.document.push(
                    {
                        'folder_id': folder_id,
                        'document_id': document_id,
                        'document_index': document_index
                    }
                );
            });

            let formData = new FormData();
            documents = JSON.stringify(documents);
            formData.append('data', documents);
            axios.post('/agents/doc_management/transactions/listings/reorder_documents', formData, axios_options)
                .then(function (response) {
                    if(c == stop && on_load == 'no') {
                        toastr['success']('Documents Reordered');
                    }
                })
                .catch(function (error) {

                });

        });

    }

    window.format_money = function () {
        $('#ListPrice').val('$' + global_format_number($('#ListPrice').val().replace('/\$/', '')));
    }

    window.listing_options = function() {

        let sale_type = $('[name=property_sub_type]').val();
        if(sale_type == 'Standard' || sale_type == 'Short Sale') {
            $('.hoa').show();
        } else {
            $('.hoa').hide();
            $('[name=hoa_condo]').val('none').trigger('change').attr('required', false);
        }

        let listing_type = $('[name=listing_type]').val();
        if(listing_type == 'rental') {
            $('.property-sub-type').hide().find('[name=property_sub_type]').val('Standard');
            $('.year-built, .hoa').hide();
            $('[name=year_built], [name=hoa_condo]').attr('required', false);
        } else {
            if(sale_type == 'Standard' || sale_type == 'Short Sale') {
                $('.property-sub-type, .year-built, .hoa').show();
                $('[name=year_built], [name=hoa_condo]').attr('required', true);
            } else {
                $('.property-sub-type, .year-built').show();
                $('[name=year_built]').attr('required', true);
            }
        }

    }

}
