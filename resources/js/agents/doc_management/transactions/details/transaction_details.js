
if (document.URL.match(/transaction_details/)) {

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

        load_details_header();


        let agent_search_request = null;

        function search_bright_agents() {

            let val = $(this).val();

            if (val.length > 3) {

                if (agent_search_request) {
                    agent_search_request.cancel();
                }
                agent_search_request = axios.CancelToken.source();

                axios.get('/agents/doc_management/transactions/search_bright_agents', {
                    cancelToken: agent_search_request.token,
                    params: {
                        val: val
                    },
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(function (response) {
                    let data = response.data;
                    $('.search-results').html('');
                    $.each(data, function (k, agents) {
                        if (agents.length > 0) {
                            $.each(agents, function (k, agent) {
                                let agent_div = ' \
                                <div class="search-result list-group-item" data-agent-first="'+ agent.MemberFirstName + '" data-agent-last="' + agent.MemberLastName + '" data-agent-phone="' + agent.MemberPreferredPhone + '" data-agent-email="' + agent.MemberEmail + '" data-agent-company="' + agent.OfficeName + '" data-agent-mls-id="' + agent.MemberMlsId + '" data-agent-street="' + agent.OfficeAddress1 + '" data-agent-city="' + agent.OfficeCity + '" data-agent-state="' + agent.OfficeStateOrProvince + '" data-agent-zip="' + agent.OfficePostalCode + '"> \
                                    <div class="row"> \
                                        <div class="col-6 col-md-3"> \
                                            <span class="font-weight-bold">'+ agent.MemberLastName + ', ' + agent.MemberFirstName + '</span><br><span class="small">' + agent.MemberType + ' (' + agent.MemberMlsId + ')<br>' + agent.MemberEmail + ' \
                                        </div> \
                                        <div class="col-6 col-md-3"> \
                                        <span class="font-weight-bold">'+ agent.OfficeName + '</span><br><span class="small">' + agent.OfficeMlsId + '</span>\
                                        </div> \
                                        <div class="col-12 col-md-6"> \
                                            '+ agent.OfficeAddress1 + '<br>' + agent.OfficeCity + ', ' + agent.OfficeStateOrProvince + ' ' + agent.OfficePostalCode + ' \
                                        </div> \
                                    </div> \
                                </div> \
                            ';
                                $('.search-results').show().append(agent_div);
                            });
                        } else {
                            $('.search-results').show().append('<div class="search-result list-group-item text-danger"><i class="fad fa-exclamation-triangle mr-2"></i> No Matching Results</div>');
                        }
                    });

                    $('.search-result').off('click').on('click', function () {
                        add_buyers_agent($(this));
                    });

                    $(document).mouseup(function (e) {
                        var container = $('.search-results');
                        if (!container.is(e.target) && container.has(e.target).length === 0) {
                            container.hide();
                        }
                    });
                })
                .catch(function (error) {
                    if (axios.isCancel(error)) {

                    } else {
                        //console.log(error);
                    }
                });


            } else {

                $('.search-results').hide().html('');

            }

        }

        $('#agent_search').on('keyup', search_bright_agents);
    });



    function show_accept_contract() {
        $('#accept_contract_modal').modal();
        $('#save_accept_contract_button').off('click').on('click', save_accept_contract);
        $('#agent_search_div').on('show.bs.collapse', function () {
            setTimeout(function () {
                $('#agent_search').focus().trigger('click');
            }, 500);
        });

        $('#accept_contract_using_heritage').change(function() {
            if($(this).val() == 'yes') {
                $('.not-using-heritage').hide();
                $('#accept_contract_title_company').val('').trigger('change');
            } else {
                $('.not-using-heritage').show();
            }
        });
    }

    function add_buyers_agent(ele) {

        let agent_first = ele.data('agent-first');
        let agent_last = ele.data('agent-last');
        let agent_email = ele.data('agent-email');
        let agent_phone = ele.data('agent-phone');
        let agent_mls_id = ele.data('agent-mls-id');
        let agent_company = ele.data('agent-company');
        let office_street = ele.data('agent-street');
        let office_city = ele.data('agent-city');
        let office_state = ele.data('agent-state');
        let office_zip = ele.data('agent-zip');

        $('#accept_contract_buyer_agent_first').val(agent_first).trigger('change');
        $('#accept_contract_buyer_agent_last').val(agent_last).trigger('change');
        $('#accept_contract_buyer_agent_email').val(agent_email).trigger('change');
        $('#accept_contract_buyer_agent_phone').val(agent_phone).trigger('change');
        $('#accept_contract_buyer_agent_mls_id').val(agent_mls_id).trigger('change');
        $('#accept_contract_buyer_agent_company').val(agent_company).trigger('change');
        $('#accept_contract_buyer_agent_street').val(office_street);
        $('#accept_contract_buyer_agent_city').val(office_city);
        $('#accept_contract_buyer_agent_state').val(office_state);
        $('#accept_contract_buyer_agent_zip').val(office_zip);

        $('.search-results').fadeOut('slow');
        $('#agent_search_div').collapse('hide');
    }

    function save_accept_contract() {

        $('#accept_contract_contract_price, #accept_contract_earnest_amount').each(function() {
            if ($(this).val() == '$0') {
                $(this).val('');
            }
        });

        let form = $('#accept_contract_form');
        let validate = validate_form(form);

        if (validate == 'yes') {

            let agent_first = $('#accept_contract_buyer_agent_first').val();
            let agent_last = $('#accept_contract_buyer_agent_last').val();
            let agent_email = $('#accept_contract_buyer_agent_email').val();
            let agent_phone = $('#accept_contract_buyer_agent_phone').val();
            let agent_mls_id = $('#accept_contract_buyer_agent_mls_id').val();
            let agent_company = $('#accept_contract_buyer_agent_company').val();
            let agent_street = $('#accept_contract_buyer_agent_street').val();
            let agent_city = $('#accept_contract_buyer_agent_city').val();
            let agent_state = $('#accept_contract_buyer_agent_state').val();
            let agent_zip = $('#accept_contract_buyer_agent_zip').val();

            let buyer_one_first = $('#accept_contract_buyer_one_first').val();
            let buyer_one_last = $('#accept_contract_buyer_one_last').val();
            let buyer_two_first = $('#accept_contract_buyer_two_first').val();
            let buyer_two_last = $('#accept_contract_buyer_two_last').val();
            let contract_date = $('#accept_contract_contract_date').val();
            let close_date = $('#accept_contract_close_date').val();
            let contract_price = $('#accept_contract_contract_price').val();
            let using_heritage = $('#accept_contract_using_heritage').val();
            let title_company = $('#accept_contract_title_company').val();
            let earnest_amount = $('#accept_contract_earnest_amount').val();
            let earnest_held_by = $('#accept_contract_earnest_held_by').val();
            let Listing_ID = $('#Listing_ID').val();

            let formData = new FormData();
            formData.append('agent_first', agent_first);
            formData.append('agent_last', agent_last);
            formData.append('agent_email', agent_email);
            formData.append('agent_phone', agent_phone);
            formData.append('agent_mls_id', agent_mls_id);
            formData.append('agent_company', agent_company);
            formData.append('agent_street', agent_street);
            formData.append('agent_city', agent_city);
            formData.append('agent_state', agent_state);
            formData.append('agent_zip', agent_zip);
            formData.append('buyer_one_first', buyer_one_first);
            formData.append('buyer_one_last', buyer_one_last);
            formData.append('buyer_two_first', buyer_two_first);
            formData.append('buyer_two_last', buyer_two_last);
            formData.append('contract_date', contract_date);
            formData.append('close_date', close_date);
            formData.append('contract_price', contract_price);
            formData.append('using_heritage', using_heritage);
            formData.append('title_company', title_company);
            formData.append('earnest_amount', earnest_amount);
            formData.append('earnest_held_by', earnest_held_by);
            formData.append('Listing_ID', Listing_ID);
            axios.post('/agents/doc_management/transactions/accept_contract', formData, axios_options)
                .then(function (response) {
                    $('#accept_contract_modal').modal('hide');
                    load_tabs('contracts');
                    let Contract_ID = response.data.Contract_ID;
                    $('#modal_info').modal().find('.modal-body').html('<div class="w-100 text-center">Your Contract was successfully added. You will find it in the "Contracts" tab<br><br><a class="btn btn-primary" href="/agents/doc_management/transactions/transaction_details/' + Contract_ID + '/contract">View Contract</a></div>');
                    $('.header-contract-active').hide();
                })
                .catch(function (error) {
                    console.log(error);
                });

        }
    }

    window.load_details_header = function () {
        let Listing_ID = $('#Listing_ID').val();
        let Contract_ID = $('#Contract_ID').val();
        let Referral_ID = $('#Referral_ID').val();
        let transaction_type = $('#transaction_type').val();
        axios.get('/agents/doc_management/transactions/transaction_details_header', {
            params: {
                Listing_ID: Listing_ID,
                Contract_ID: Contract_ID,
                Referral_ID: Referral_ID,
                transaction_type: transaction_type
            },
            headers: axios_headers_html
        })
            .then(function (response) {
                $('#details_header').html(response.data);
                $('[data-toggle="popover"]').popover({ placement: 'bottom' });
                $('#accept_contract_button').click(show_accept_contract);
                /* $('#disabled_accept_contract_button').click(function () {
                    $('#modal_danger').modal().find('.modal-body').html('You cannot accept a contract until the current one is released');
                });
                $('#disabled_withdraw_listing_button').click(function () {
                    $('#modal_danger').modal().find('.modal-body').html('You cannot withdraw the listing while it is under contract. Once the contract is released you can withdraw the listing');
                }); */
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    window.load_tabs = function (tab, reorder = true) {
        let Listing_ID = $('#Listing_ID').val();
        let Contract_ID = $('#Contract_ID').val();
        let Referral_ID = $('#Referral_ID').val();
        let Agent_ID = $('#Agent_ID').val();
        let transaction_type = $('#transaction_type').val();

        if (tab == '') {
            tab = 'details';
        }
        axios.get('/agents/doc_management/transactions/get_' + tab, {
            params: {
                Listing_ID: Listing_ID,
                Contract_ID: Contract_ID,
                Referral_ID: Referral_ID,
                Agent_ID: Agent_ID,
                transaction_type: transaction_type
            },
            headers: axios_headers_html
        })
            .then(function (response) {

                $('#' + tab + '_tab').html(response.data);

                if (tab == 'details') {

                    // update counties when state is changed
                    $('#StateOrProvince').change(update_county_select);

                    // open checklist tab shortcut from helper
                    $(document).on('click', '#open_checklist_button', function () {
                        $('#open_checklist_tab').trigger('click');
                    });

                    $('#search_mls_button').off('click').on('click', search_mls);

                    $('.save-details-button').off('click').on('click', save_details);

                    if($('#UsingHeritage').val() == 'no') {
                        $('.not-using-heritage').show();
                    }

                    $('#UsingHeritage').change(function() {
                        if($(this).val() == 'yes') {
                            $('.not-using-heritage').hide();
                            $('#TitleCompany').val('').trigger('change');
                        } else {
                            $('.not-using-heritage').show();
                        }
                    });

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

                    setTimeout(function () {
                        show_hide_fields();
                        $('.member-type-id').change(show_hide_fields);
                        $('.bank-trust').click(show_bank_trust);
                    }, 1000);

                    $('a[data-toggle="list"]').on('shown.bs.tab', function (e) {
                        show_hide_fields();
                    });

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


                        $('.add-to-checklist-button').off('click').on('click', show_add_to_checklist);

                    }, 200);

                    if(reorder) {
                        setTimeout(function() {
                            reorder_documents('yes');
                        }, 1000);
                    }

                } else if (tab == 'checklist') {

                    setTimeout(function() {
                        $('.save-notes-button').off().on('click', save_add_notes);

                        $('.add-document-button').off('click').on('click', show_add_document);

                        $('.view-docs-button').off('click').on('click', toggle_view_docs_button);

                        $('.view-notes-button').off('click').on('click', toggle_view_notes_button);

                        $('.delete-doc-button').off('click').on('click', show_delete_doc);

                        $('.mark-read-button').off('click').on('click', mark_note_read);

                        $('#change_checklist_button').off('click').on('click', confirm_change_checklist);

                        $('.accept-checklist-item-button').off('click').on('click', function() {
                            checklist_item_review_status($(this), 'accepted', null);
                        });
                        $('.reject-checklist-item-button').off('click').on('click', function() {
                            show_checklist_item_review_status($(this), 'rejected');
                        });

                        $('.undo-accepted, .undo-rejected').off('click').on('click', function() {
                            checklist_item_review_status($(this), 'not_reviewed', null);
                        });

                        $('.mark-required').off('click').on('click', function() {
                            mark_required($(this), $(this).data('checklist-item-id'), $(this).data('required'));
                        });

                        $('.remove-checklist-item').off('click').on('click', function() {
                            show_remove_checklist_item($(this), $(this).data('checklist-item-id'));
                        });

                        $('.add-checklist-item-button').off('click').on('click', show_add_checklist_item);

                        $('.email-agent-button').off('click').on('click', show_email_agent);

                        $('.notes-div').each(function() {
                            get_notes($(this).data('checklist-item-id'));
                        });

                    }, 500);


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

                    $('.transaction-option-trigger').off('change').on('change', listing_options);

                    listing_options();

                    // hide all form-group-div and show the first (MAR)
                    $('.form-group-div').hide();
                    $('.form-group-div').eq(0).show();
                    // search forms
                    $('#form_search').keyup(form_search);
                    // select and show form groups
                    $('.select-form-group').change(function () {
                        // clear search input
                        $('#form_search').val('').trigger('change');

                        // if all show everything or just the selected group
                        if ($(this).val() == 'all') {
                            $('.form-group-div, .list-group-header, .form-name').show();
                        } else {
                            $('.list-group-header, .form-name').show();
                            $('.form-group-div').hide();
                            $('[data-form-group-id="' + $(this).val() + '"]').show();
                        }
                    });


                } else if (tab == 'contracts') {

                    $('.contract-div').mouseenter(function () {
                        $(this).addClass('z-depth-3').removeClass('z-depth-1');
                    });
                    $('.contract-div').mouseleave(function () {
                        $(this).removeClass('z-depth-3').addClass('z-depth-1');
                    });

                }


                if($('#required_fields_using_heritage').val() == 'no') {
                    $('.not-using-heritage').show();
                } else {
                    $('#required_fields_title_company').prop('required', false).removeClass('required');
                }

                $('#required_fields_using_heritage').change(function() {
                    if($(this).val() == 'yes') {
                        $('.not-using-heritage').hide();
                        $('#required_fields_title_company').val('').trigger('change');
                        $('#required_fields_title_company').prop('required', false).removeClass('required');
                    } else {
                        $('.not-using-heritage').show();
                        $('#required_fields_title_company').prop('required', true).addClass('required');
                    }
                });

                $('.money, .money-decimal').each(function() {
                    if($(this).val() == '') {
                        //$(this).val('0');
                    }
                });
                if($('.money').length > 0) {
                    format_money($('.money'));
                    $('.money').keyup(function () {
                        format_money($(this));
                    });
                }
                if($('.money-decimal').length > 0) {
                    $('.money-decimal').each(function() {
                        if($(this).val() != '') {
                            format_money_with_decimals($(this));
                        }
                        $('.money-decimal').change(function () {
                            if($(this).val() != '') {
                                format_money_with_decimals($(this));
                            }
                        });
                    });
                }


                // init tooltips and form elements
                global_tooltip();

                $('.draggable').draggable({
                    handle: '.draggable-handle'
                });

                setTimeout(function() {
                    form_elements();
                    global_loading_off();
                }, 500);

                $('.modal-backdrop').remove();

            })
            .catch(function (error) {
                console.log(error);
            });
    }

    window.load_documents_on_tab_click = function() {
        $('#open_documents_tab').off().on('show.bs.tab', function (e) {
            load_tabs('documents', false);
        });
    }

    window.load_checklist_on_tab_click = function() {
        $('#open_checklist_tab').off().on('show.bs.tab', function (e) {
            load_tabs('checklist');
        });
    }


    function sortable_documents() {
        $('.sortable-documents').sortable({
            connectWith: '.sortable-documents',
            placeholder: 'bg-sortable',
            handle: '.document-handle',
            stop: function (event, ui) {
                reorder_documents('no');
            }

        });
        $('.sortable-documents').disableSelection();
    }

    window.reorder_documents = function (on_load) {

        let c = 0;
        let stop = $('.sortable-documents').length - 1;

        $('.sortable-documents').each(function () {
            let els = $(this).find('.document-div');
            let folder_id = $(this).data('folder-id');

            let documents = {
                document: []
            }

            els.each(function () {
                let el, document_id, document_index;
                el = $(this);
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
            axios.post('/agents/doc_management/transactions/reorder_documents', formData, axios_options)
                .then(function (response) {
                    if (c == stop && on_load == 'no') {
                        toastr['success']('Documents Reordered');
                        docs_count();
                    }
                    c += 1;
                })
                .catch(function (error) {

                });

        });

    }

    function docs_count() {
        $('.folder-div').each(function () {
            let docs_count = $(this).find('.sortable-documents').find('.document-div').length;
            $(this).find('.docs-count').text(docs_count);
        });
    }


    window.listing_options = function () {

        let sale_type = $('[name=property_sub_type]').val();
        if (sale_type == 'Standard' || sale_type == 'Short Sale') {
            $('.hoa').show();
        } else {
            $('.hoa').hide();
            $('[name=hoa_condo]').val('none').trigger('change').attr('required', false);
        }

        let listing_type = $('[name=listing_type]').val();
        if (listing_type == 'rental') {
            $('.property-sub-type').hide().find('[name=property_sub_type]').val('Standard');
            $('.year-built, .hoa').hide();
            $('[name=year_built], [name=hoa_condo]').attr('required', false);
        } else {
            if (sale_type == 'Standard' || sale_type == 'Short Sale') {
                $('.property-sub-type, .year-built, .hoa').show();
                $('[name=year_built], [name=hoa_condo]').attr('required', true);
            } else {
                $('.property-sub-type, .year-built').show();
                $('[name=year_built]').attr('required', true);
            }
        }

    }

}
