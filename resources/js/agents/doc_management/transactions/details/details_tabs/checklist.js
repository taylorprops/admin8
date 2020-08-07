// const { active } = require("sortablejs");

if (document.URL.match(/transaction_details/)) {

    $(document).ready(function() {



    });



    /* window.show_remove_checklist_item = function(ele, checklist_item_id) {

        $('#confirm_remove_checklist_item_modal').modal();
        $('#confirm_remove_checklist_item_button').off('click').on('click', function() {
            remove_checklist_item(ele, checklist_item_id);
        });
    } */

    /* window.remove_checklist_item = function(ele, checklist_item_id) {

        let formData = new FormData();
        formData.append('checklist_item_id', checklist_item_id);
        axios.post('/agents/doc_management/transactions/remove_checklist_item', formData, axios_options)
        .then(function (response) {
            load_tabs('checklist');
            load_documents_on_tab_click();
            $('#confirm_remove_checklist_item_modal').modal('hide');
        })
        .catch(function (error) {
            console.log(error);
        });
    } */

    /* window.mark_required = function (ele, checklist_item_id, required) {

        let formData = new FormData();
        formData.append('checklist_item_id', checklist_item_id);
        formData.append('required', required);
        axios.post('/agents/doc_management/transactions/mark_required', formData, axios_options)
        .then(function (response) {

            let status_badge = ele.closest('.checklist-item-div').find('.status-badge');
            $('.mark-required').removeClass('d-block').removeClass('d-none');

            if(status_badge.text().match(/Applicable/)) {
                status_badge.removeClass('bg-default-light').addClass('bg-orange').html('<i class="fal fa-exclamation-circle fa-lg mr-2"></i> Required').attr('title', '');
                $('.mark-required.no').addClass('d-block').next('a').addClass('d-none');
            } else if(status_badge.text().match(/Required/)) {
                status_badge.removeClass('bg-orange').addClass('bg-default-light').html('<i class="fal fa-minus-circle fa-lg mr-2"></i> If Applicable').attr('title', '');
                $('.mark-required.yes').addClass('d-block').prev('a').addClass('d-none');
            }

        })
        .catch(function (error) {
            console.log(error);
        });
    } */

    /* window.show_checklist_item_review_status = function(ele, action) {
        // show reject model with list of reasons
        $('#reject_document_modal').modal();
        setTimeout(function() {
            $('#rejected_reason').focus();
        }, 500);
        $('#rejected_reason').keyup(function() {
            if($(this).val().length > 0) {
                $('.rejected-reason').hide();
                let search = new RegExp($(this).val(), 'i')
                $('.rejected-reason').each(function() {
                    if($(this).text().match(search)) {
                        $(this).show();
                    }
                });
            } else {
                $('.rejected-reason').show();
            }
        });

        $('.rejected-reason').off('click').on('click', function() {
            $('#rejected_reason').val($(this).data('reason'));
            $('.rejected-selected').addClass('d-none');
            $(this).find('.rejected-selected').removeClass('d-none');
        });

        $('#save_reject_document_button').off('click').on('click', function() {

            let form = $('#rejected_reason_form');
            let validate = validate_form(form);
            if(validate == 'yes') {
                global_loading_on('', '<div class="h3-responsive text-white">Updating Checklist Item and Adding To Comments</div>');
                let note = $('#rejected_reason').val();
                checklist_item_review_status(ele, action, note);
                $('#reject_document_modal').modal('hide');
                $('.rejected-selected').addClass('d-none');
            }
        });
    } */

    /* window.checklist_item_review_status = function(ele, action, note) {

        let checklist_item_id = ele.data('checklist-item-id');
        let review_options = ele.closest('.review-options');
        let checklist_items_div = ele.closest('.checklist-item-div');
        let required = ele.data('required');
        let delete_docs_button = ele.closest('.checklist-item-div').find('.delete-doc-button');

        review_options.find('.item-not-reviewed, .item-rejected, .item-accepted').removeClass('d-flex').addClass('d-none');
        checklist_items_div.find('.status-badge').removeClass('bg-danger bg-success bg-orange bg-blue-light bg-default-light text-white text-primary');
        let classes = '';
        let html = '';

        review_options.removeClass('bg-green-light bg-red-light').addClass('bg-light');

        if(action == 'accepted') {
            review_options.removeClass('bg-light').addClass('bg-green-light').find('.item-accepted').removeClass('d-none').addClass('d-flex');
            classes = 'bg-success text-white';
            html = '<i class="fal fa-check-circle fa-lg mr-2"></i> Complete';
            delete_docs_button.prop('disabled', true);
        } else if(action == 'rejected') {
            review_options.removeClass('bg-light').addClass('bg-red-light').find('.item-rejected').removeClass('d-none').addClass('d-flex');
            delete_docs_button.prop('disabled', false);

            if(required) {
                classes = 'bg-danger text-white';
                html = '<i class="fal fa-exclamation-circle fa-lg mr-2"></i> Required';
            } else {
                classes = 'bg-default-light text-white';
                html = '<i class="fal fa-minus-circle fa-lg mr-2"></i> If Applicable';
            }
        } else if(action == 'not_reviewed') {
            review_options.find('.item-not-reviewed').removeClass('d-none').addClass('d-flex');
            classes = 'bg-blue-light text-primary';
            html = '<i class="fal fa-minus-circle fa-lg mr-2"></i> Pending';
            delete_docs_button.prop('disabled', false);
        }

        checklist_items_div.find('.status-badge').addClass(classes).html(html).attr('title', '');

        set_checklist_item_review_status(checklist_item_id, action, note);

    } */

    /* window.set_checklist_item_review_status = function(checklist_item_id, action, note) {

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
            $('.collapse').collapse('hide');
            if(action == 'rejected') {
                load_tabs('checklist');
            }
            load_documents_on_tab_click();
            global_loading_off();
        })
        .catch(function (error) {
            console.log(error);
        });
    } */

    window.confirm_change_checklist = function() {

        let checklist_id = $(this).data('checklist-id');

        $('#confirm_change_checklist_modal').modal();
        $('#confirm_change_checklist_button').off('click').on('click', function() {

            $('#confirm_change_checklist_modal').modal('hide');
            change_checklist(checklist_id);

        });

    }

    window.change_checklist = function(checklist_id) {

        let Listing_ID = $('#Listing_ID').val();
        let Contract_ID = $('#Contract_ID').val();
        let transaction_type = $('#transaction_type').val();
        let Agent_ID = $('#Agent_ID').val();

        $('#change_checklist_modal').modal();

        $('#save_change_checklist_button').off('click').on('click', function() {

            let form = $('#change_checklist_form');

            let validate = validate_form(form);
            if(validate == 'yes') {

                $('#save_change_checklist_button').html('<i class="fas fa-spinner fa-pulse mr-2"></i> Saving Checklist');

                let formData = new FormData(form[0]);
                formData.append('checklist_id', checklist_id);
                formData.append('Listing_ID', Listing_ID);
                formData.append('Contract_ID', Contract_ID);
                formData.append('transaction_type', transaction_type);
                formData.append('Agent_ID', Agent_ID);

                axios.post('/agents/doc_management/transactions/change_checklist', formData, axios_options)
                .then(function (response) {

                    $('#save_change_checklist_button').html('<i class="fad fa-check mr-2"></i> Save');
                    load_tabs('checklist');
                    load_documents_on_tab_click();
                    $('#change_checklist_modal').modal('hide');
                    toastr['success']('Checklist Successfully Changed');

                })
                .catch(function (error) {
                    console.log(error);
                });

            }

        });

    }


    window.show_delete_doc = function() {
        let button = $(this);
        let document_id = button.data('document-id');

        $('#confirm_delete_checklist_item_doc_modal').modal();
        $('#delete_checklist_item_doc_button').off('click').on('click', function () {
            delete_doc(button, document_id);
        });
    }

    window.delete_doc = function(button, document_id) {
        let formData = new FormData();
        formData.append('document_id', document_id);
        axios.post('/agents/doc_management/transactions/remove_document_from_checklist_item', formData, axios_options)
        .then(function (response) {
            $('#confirm_delete_checklist_item_doc_modal').modal('hide');
            toastr['success']('Document Removed From Checklist');
            load_documents_on_tab_click();
            let doc_count = button.closest('.checklist-item-div').find('.doc-count');
            doc_count.text(parseInt(doc_count.text()) - 1);
            button.closest('.document-row').fadeOut().remove();
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.toggle_view_docs_button = function() {
        $('.documents-collapse.show').not($(this).data('target')).collapse('hide');
    }

    window.toggle_view_notes_button = function() {
        $('.notes-collapse.show').not($(this).data('target')).collapse('hide');
    }

    window.show_add_document = function() {

        let button = $(this);

        $('#add_document_checklist_id').val(button.data('checklist-id'));
        $('#add_document_checklist_item_id').val(button.data('checklist-item-id'));

        // confirm earnest and title fields are complete
        if($('#questions_confirmed').val() == 'yes') {

            add_document(button);

        } else {

            $('#required_fields_modal').modal();

            $('#save_required_fields_button').off('click').on('click', function() {

                let form = $('#required_fields_form');
                let validate = validate_form(form);

                if(validate == 'yes') {
                    let Contract_ID = $('#Contract_ID').val();
                    let formData = new FormData(form[0]);
                    formData.append('Contract_ID', Contract_ID);
                    axios.post('/agents/doc_management/transactions/save_required_fields', formData, axios_options)
                    .then(function (response) {

                        $('#required_fields_modal').modal('hide');
                        add_document(button);
                        $('#questions_confirmed').val('yes');

                    })
                    .catch(function (error) {
                        console.log(error);
                    });

                }

            });

        }

    }

    window.add_document = function(button) {
        let active_collapse = button.data('target');
        $('.select-document-button').off('click').on('click', function( ){
            save_add_document($(this).data('document-id'), active_collapse);
        });
        $('#add_document_modal').modal();
    }

    window.save_add_document = function(document_id, active_collapse) {

        let checklist_id = $('#add_document_checklist_id').val();
        let checklist_item_id = $('#add_document_checklist_item_id').val();
        let Listing_ID = $('#Listing_ID').val();
        let Contract_ID = $('#Contract_ID').val();
        let Referral_ID = $('#Referral_ID').val();
        let transaction_type = $('#transaction_type').val();
        let Agent_ID = $('#Agent_ID').val();
        let formData = new FormData();
        formData.append('document_id', document_id);
        formData.append('checklist_id', checklist_id);
        formData.append('checklist_item_id', checklist_item_id);
        formData.append('Listing_ID', Listing_ID);
        formData.append('Contract_ID', Contract_ID);
        formData.append('Referral_ID', Referral_ID);
        formData.append('transaction_type', transaction_type);
        formData.append('Agent_ID', Agent_ID);
        axios.post('/agents/doc_management/transactions/add_document_to_checklist_item', formData, axios_options)
        .then(function (response) {
            $('#add_document_modal').modal('hide');
            toastr['success']('Document Added To Checklist');
            load_tabs('checklist');
            load_documents_on_tab_click();
            /* setTimeout(function() {
                $('#'+active_collapse).collapse('show');
            }, 500); */
        })
        .catch(function (error) {
            console.log(error);
        });

    }

}
