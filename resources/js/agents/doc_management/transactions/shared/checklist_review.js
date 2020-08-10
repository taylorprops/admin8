if (document.URL.match(/transaction_details/) || document.URL.match(/document_review/)) {

    let page_type = '';

    if (document.URL.match(/transaction_details/)) {
        page_type = 'checklist';
    } else if (document.URL.match(/document_review/)) {
        page_type = 'review';
    }


    window.show_email_agent = function() {

        $('#email_agent_modal').modal();

        axios.get('/agents/doc_management/transactions/get_email_checklist_html', {
            params: {
                checklist_id: $('#transaction_checklist_id').val(),
                transaction_type: $('#transaction_type').val()
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {
            $('#email_agent_checklist_details').html(response.data);
            $('#send_email_agent_button').off('click').on('click', send_email_agent)
        })
        .catch(function (error) {
            console.log(error);
        });

    }

    window.send_email_agent = function() {

        $('#send_email_agent_button').html('<i class="fas fa-spinner fa-pulse mr-2"></i> Sending Email');

        let from = $('#email_agent_from').val();
        let to = $('#email_agent_to').val();
        let cc = $('#email_agent_cc').val();

        let to_addresses = [];
        to_addresses.push({
            type: 'to',
            address: to
        });
        if(cc != '') {
            to_addresses.push({
                type: 'cc',
                address: cc
            });
        }
        let subject = $('#email_agent_subject').val();
        let message = nl2br($('#email_agent_message').val());
        message += $('#email_agent_checklist_details').html();

        let formData = new FormData();
        formData.append('type', 'checklist');
        formData.append('from', from);
        formData.append('to_addresses', JSON.stringify(to_addresses));
        formData.append('subject', subject);
        formData.append('message', message);

        axios.post('/agents/doc_management/transactions/send_email', formData, axios_options)
        .then(function (response) {
            $('#send_email_agent_button').html('<i class="fad fa-share mr-2"></i> Send Email');
            $('#email_agent_modal').modal('hide');
            toastr['success']('Agent Successfully Emailed');
        })
        .catch(function (error) {
            console.log(error);
        });

    }

    window.save_add_notes = function () {

        let checklist_id = $(this).data('checklist-id');
        let checklist_item_id = $(this).data('checklist-item-id');
        let notes = $('.notes-input-' + checklist_item_id).val();

        if (notes == '') {
            $('#modal_danger').modal().find('.modal-body').html('You must enter comments before saving');
            return false;
        }


        let formData = new FormData();
        formData.append('notes', notes);
        formData.append('checklist_id', checklist_id);
        formData.append('checklist_item_id', checklist_item_id);
        formData.append('Listing_ID', $('#Listing_ID').val());
        formData.append('Contract_ID', $('#Contract_ID').val());
        formData.append('Referral_ID', $('#Referral_ID').val());
        formData.append('transaction_type', $('#transaction_type').val());
        formData.append('Agent_ID', $('#Agent_ID').val());
        axios.post('/agents/doc_management/transactions/add_notes_to_checklist_item', formData, axios_options)
            .then(function (response) {
                toastr['success']('Comments Successfully Added');
                get_notes(checklist_item_id);
                $('.notes-input-' + checklist_item_id).val('');

            })
            .catch(function (error) {
                console.log(error);
            });
    }

    window.get_notes = function (checklist_item_id) {
        let Agent_ID = $('#Agent_ID').val();
        axios.get('/doc_management/get_notes', {
            params: {
                checklist_item_id: checklist_item_id,
                Agent_ID: Agent_ID
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
            .then(function (response) {
                if (response.data != '') {
                    $('#notes_' + checklist_item_id).find('.notes-div').html(response.data);
                    $('.mark-read-button').off('click').on('click', mark_note_read);
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    window.mark_note_read = function () {

        let button = $(this);
        let note_id = button.data('note-id');
        //let notes_collapse = button.data('notes-collapse');
        let note_div = button.closest('.note-div');

        let formData = new FormData();
        formData.append('note_id', note_id);
        axios.post('/agents/doc_management/transactions/mark_note_read', formData, axios_options)
            .then(function (response) {
                button.parent().html('<span class="text-success small"><i class="fa fa-check"></i> Read</span>');
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    window.mark_required = function (ele, checklist_item_id, required) {

        let formData = new FormData();
        formData.append('checklist_item_id', checklist_item_id);
        formData.append('required', required);
        axios.post('/agents/doc_management/transactions/mark_required', formData, axios_options)
            .then(function (response) {

                let parent = '';
                let required_icon = '';
                let default_icon = '';

                if (page_type == 'checklist') {
                    required_icon = '<i class="fal fa-exclamation-circle fa-lg mr-2"></i> ';
                    default_icon = '<i class="fal fa-minus-circle fa-lg mr-2"></i> ';
                }
                parent = ele.closest('.checklist-item-div');

                let status_badge = parent.find('.status-badge');

                parent.find('.mark-required').removeClass('d-block').removeClass('d-none');
                if(required == 'yes') {
                    parent.find('.checklist-item-unused').removeClass('checklist-item-unused');
                } else {
                    parent.find('.checklist-item-name, .status-badge').addClass('checklist-item-unused');
                }

                if (status_badge.text().match(/Applicable/)) {
                    status_badge.removeClass('bg-default-light').addClass('bg-orange').html(required_icon + 'Required').attr('title', '');
                    parent.find('.mark-required.no').addClass('d-block').next('a').addClass('d-none');
                } else if (status_badge.text().match(/Required/)) {
                    status_badge.removeClass('bg-orange').addClass('bg-default-light').html(default_icon + 'If Applicable').attr('title', '');
                    parent.find('.mark-required.yes').addClass('d-block').prev('a').addClass('d-none');
                }

            })
            .catch(function (error) {
                console.log(error);
            });
    }

    window.show_remove_checklist_item = function (ele, checklist_item_id) {

        $('#confirm_remove_checklist_item_modal').modal();
        $('#confirm_remove_checklist_item_button').off('click').on('click', function () {
            remove_checklist_item(ele, checklist_item_id);
        });
    }

    window.remove_checklist_item = function (ele, checklist_item_id) {

        let formData = new FormData();
        formData.append('checklist_item_id', checklist_item_id);
        axios.post('/agents/doc_management/transactions/remove_checklist_item', formData, axios_options)
            .then(function (response) {
                if (page_type == 'checklist') {
                    load_tabs('checklist');
                    load_documents_on_tab_click();
                } else if (page_type == 'review') {
                    ele.closest('.list-group-item').fadeOut();
                }
                $('#confirm_remove_checklist_item_modal').modal('hide');
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    window.show_add_checklist_item = function () {

        let group_id = $(this).data('group-id');
        $('#add_checklist_item_group_id').val(group_id);

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

        if (page_type == 'review') {
            $('#add_checklist_item_modal').appendTo('body');
        }
        $('#add_checklist_item_modal').modal();
        form_elements();

        $('.form-name').off('click').on('click', function (e) {

            if (!$(e.target).hasClass('form-link')) {

                clear_selected_form();

                $(this).addClass('selected');
                $('#add_checklist_item_name').val('').trigger('change');
                $(this).addClass('bg-green-light selected').find('.checked-div').removeClass('d-none').next('.form-name-display').removeClass('text-primary').addClass('text-success');

            }

        });

        $('#add_checklist_item_name').keyup(function () {
            if ($(this).val() != '') {
                clear_selected_form();
            }
        });

        $('#save_add_checklist_item_button').click(function () {
            save_add_checklist_item(group_id);
        });

    }

    window.save_add_checklist_item = function (group_id) {

        let Agent_ID = $('#Agent_ID').val();
        let Listing_ID = $('#Listing_ID').val() || null;
        let Contract_ID = $('#Contract_ID').val() || null;
        let Referral_ID = $('#Referral_ID').val() || null;
        let checklist_id = $('#add_checklist_item_checklist_id').val();
        let checklist_form_id = $('.form-name.selected').data('form-id') || null;
        let add_checklist_item_name = $('#add_checklist_item_name').val();
        let add_checklist_item_group_id = $('#add_checklist_item_group_id').val();

        let form = $('#add_checklist_item_form');
        let validation = validate_form(form);

        if (validation == 'yes') {

            let formData = new FormData(form[0]);
            formData.append('Agent_ID', Agent_ID);
            formData.append('Listing_ID', Listing_ID);
            formData.append('Contract_ID', Contract_ID);
            formData.append('Referral_ID', Referral_ID);
            formData.append('checklist_id', checklist_id);
            formData.append('checklist_form_id', checklist_form_id);
            formData.append('add_checklist_item_name', add_checklist_item_name);
            formData.append('add_checklist_item_group_id', add_checklist_item_group_id);

            axios.post('/agents/doc_management/transactions/save_add_checklist_item', formData, axios_options)
                .then(function (response) {
                    toastr['success']('Checklist Item Successfully Added');
                    $('.modal-backdrop').remove();
                    if (page_type == 'checklist') {
                        load_tabs('checklist');
                    } else {
                        $('#add_checklist_item_modal').remove();
                        let id = $('#property_id').val();
                        let type = $('#property_type').val();
                        get_checklist(id, type);
                    }

                })
                .catch(function (error) {
                    console.log(error);
                });

        }
    }

    window.clear_selected_form = function () {

        $('.form-name').removeClass('bg-green-light selected');
        $('.form-name-display').removeClass('text-success').addClass('text-primary');
        $('.checked-div').addClass('d-none');
    }

    window.form_search = function () {
        let v = $('#form_search').val();
        if (v.length == 0) {
            // hide all containers with header and name inside
            $('.form-group-div').hide();
            // make sure all headers and names are visible if searched for
            $('.list-group-header, .form-name').show();
            // get value of selected form group to reset list
            let form_group = $('.select-form-group').val();
            if (form_group == 'all') {
                $('.form-group-div, .list-group-header, .form-name').show();
            } else {
                $('[data-form-group-id="' + form_group + '"]').show().find('.form-name').show();
            }
        } else {

            // show all containers with header and name inside
            $('.form-group-div').show();
            // hide all headers
            $('.list-group-header').hide();
            // hide all names
            $('.form-name').hide().each(function () {
                if ($(this).data('form-name').match(new RegExp(v, 'i'))) {
                    // show name
                    $(this).show();
                    // show header
                    $(this).closest('.form-group-div').find('.list-group-header').show();
                }
            });
        }
    }

    window.show_checklist_item_review_status = function (ele, action) {

        form_elements();
        // show reject model with list of reasons
        $('#reject_document_modal').modal();
        setTimeout(function () {
            $('#rejected_reason').focus();
        }, 500);
        $('#rejected_reason').keyup(function () {
            if ($(this).val().length > 0) {
                $('.rejected-reason').hide();
                let search = new RegExp($(this).val(), 'i')
                $('.rejected-reason').each(function () {
                    if ($(this).text().match(search)) {
                        $(this).show();
                    }
                });
            } else {
                $('.rejected-reason').show();
            }
        });

        $('.rejected-reason').off('click').on('click', function () {
            $('#rejected_reason').val($(this).data('reason'));
            $('.rejected-selected').addClass('d-none');
            $(this).find('.rejected-selected').removeClass('d-none');
        });

        $('#save_reject_document_button').off('click').on('click', function () {

            let form = $('#rejected_reason_form');
            let validate = validate_form(form);
            if (validate == 'yes') {
                if (page_type == 'checklist') {
                    global_loading_on('', '<div class="h3-responsive text-white">Updating Checklist Item and Adding To Comments</div>');
                }
                let note = $('#rejected_reason').val();
                checklist_item_review_status(ele, action, note);
                $('#reject_document_modal').modal('hide');
                $('.rejected-selected').addClass('d-none');

            }
        });
    }

    window.checklist_item_review_status = function (ele, action, note) {

        let checklist_item_id = ele.data('checklist-item-id');
        let review_options = null;
        let delete_docs_button = null;

        let parent_div = '';
        if (page_type == 'checklist') {
            parent_div = ele.closest('.checklist-item-div');
            delete_docs_button = ele.closest('.checklist-item-div').find('.delete-doc-button');
        } else {
            parent_div = $('.checklist-item-div.active');
        }


        review_options = ele.closest('.review-options');
        review_options.find('.item-not-reviewed, .item-rejected, .item-accepted').removeClass('d-flex').addClass('d-none');
        review_options.removeClass('bg-green-light bg-red-light').addClass('bg-light');

        let required = ele.data('required');

        parent_div.find('.status-badge').removeClass('bg-danger bg-default bg-success bg-orange bg-blue-light bg-default-light text-white text-primary');
        let classes = '';
        let html = '';

        if (action == 'accepted') {

            if (page_type == 'checklist') {
                html = '<i class="fal fa-check-circle fa-lg mr-2"></i> Complete';
                delete_docs_button.prop('disabled', true);
            } else {
                html = 'Complete';
                next_item(parent_div);
            }

            parent_div.removeClass('pending');
            review_options.removeClass('bg-light').addClass('bg-green-light').find('.item-accepted').removeClass('d-none').addClass('d-flex');

            classes = 'bg-success text-white';

        } else if (action == 'rejected') {

            if (page_type == 'review') {

                review_options.removeClass('bg-light').addClass('bg-red-light').find('.item-rejected').removeClass('d-none').addClass('d-flex');
                classes = 'bg-default text-white';
                html = 'Rejected';
                next_item(parent_div);

                setTimeout(function() {
                    get_notes(checklist_item_id);
                    $('#checklist_item_'+checklist_item_id).find('.fa-comment.fa-stack-1x').removeClass('text-blue-light').addClass('text-primary');
                }, 1000);

            }

            parent_div.removeClass('pending');

        } else if (action == 'not_reviewed') {

            if (page_type == 'checklist') {
                html = '<i class="fal fa-minus-circle fa-lg mr-2"></i> Pending';
                delete_docs_button.prop('disabled', false);
            } else {
                html = 'Pending';
            }
            // if agent or admin
            classes = 'bg-blue-light text-primary';
            if ($('.accept-checklist-item-button').length > 0) {
                classes = 'bg-danger text-white';
            }

            review_options.find('.item-not-reviewed').removeClass('d-none').addClass('d-flex');
            parent_div.addClass('pending');

        }

        update_pending_count();

        parent_div.find('.status-badge').addClass(classes).html(html).attr('title', '');

        set_checklist_item_review_status(checklist_item_id, action, note);

    }

    function update_pending_count() {
        let count = $('.pending').length;
        $('.property-item.active').find('.badge-pill').text(count);
    }

    // TODO: this for comments
    /* function update_notes_count() {
        let count = $('.pending').length;
        $('.property-item.active').find('.badge-pill').text(count);
    } */

    function next_item(parent_div) {
        let index = parent_div.index();
        cancel = false;
        $('.checklist-item-div.pending').each(function () {
            if (cancel == false) {
                if ($(this).index() > index) {
                    $(this).find('.checklist-item-name').trigger('click');
                    let id = $(this).prop('id');
                    document.getElementById(id).scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'nearest' });
                    cancel = true;
                }
            }
        });

    }

    window.set_checklist_item_review_status = function (checklist_item_id, action, note) {

        let Agent_ID = $('#Agent_ID').val();
        let Listing_ID = $('#Listing_ID').val();
        let Contract_ID = $('#Contract_ID').val();
        let Referral_ID = $('#Referral_ID').val();
        let transaction_type = $('#transaction_type').val();

        let formData = new FormData();
        formData.append('Agent_ID', Agent_ID);
        formData.append('Listing_ID', Listing_ID);
        formData.append('Contract_ID', Contract_ID);
        formData.append('Referral_ID', Referral_ID);
        formData.append('transaction_type', transaction_type);
        formData.append('checklist_item_id', checklist_item_id);
        formData.append('action', action);
        formData.append('note', note);
        axios.post('/agents/doc_management/transactions/set_checklist_item_review_status', formData, axios_options)
            .then(function (response) {

                if (page_type == 'checklist') {
                    $('.collapse').collapse('hide');
                    if (action == 'rejected') {
                        load_tabs('checklist');
                    }
                    load_documents_on_tab_click();
                    global_loading_off();
                } else {

                }

            })
            .catch(function (error) {
                console.log(error);
            });
    }

}
