import Sortable from 'sortablejs';

if (document.URL.match(/transaction_details/)) {


    $(document).ready(function () {

        $(document).on('click', '.add-folder-button', show_add_folder);

        $(document).on('click', '#upload_documents_button', show_upload_documents);

        $(document).on('click', '#add_checklist_template_button', show_add_checklist_template);

        $(document).on('click', '#save_add_checklist_template_button', function () {
            $('#save_add_checklist_template_button').html('<i class="fas fa-spinner fa-pulse mr-2"></i> Adding Documents...').prop('disabled', true);
            save_add_template_documents('checklist');
        });

        $(document).on('click', '#add_individual_template_button', show_add_individual_template);

        $(document).on('click', '#save_add_individual_template_button', function () {
            if($('.individual-template-form:checked').length > 0) {
                $('#save_add_individual_template_button').html('<i class="fas fa-spinner fa-pulse mr-2"></i> Adding Documents...').prop('disabled', true);
                save_add_template_documents('individual');
            } else {
                $('#modal_danger').modal().find('.modal-body').html('You must select at least one form to add');
            }
        });

        $(document).on('click', '.check-all', check_all);

        $(document).on('change', '.check-document', show_bulk_options);

        $(document).on('click', '#delete_documents_button', show_delete_documents);

        $(document).on('click', '#move_documents_button', show_move_documents);

        $(document).on('click', '.folder-collapse', toggle_caret);

        $(document).on('click', '.doc-delete-button', show_delete_one_document);

        $(document).on('click', '.delete-folder-button', function () {
            confirm_delete_folder($(this).data('folder-id'));
        });



        $(document).on('click', '.doc-rename-button', show_rename_document);

        $(document).on('click', '.doc-split-button', show_split_document);

        $(document).on('click', '.doc-duplicate-button', duplicate_document);

        $(document).on('click', '.doc-print-button', function () {
            print_pdf($(this).data('link'));
        });

        $(document).on('click', '.docs-print-button', function() {
            print_download_documents('print');
        });

        $(document).on('click', '.docs-download-button', function() {
            print_download_documents('download', $(this).data('type'));
        });

        $(document).on('click', '.docs-print-button', function() {
            print_download_documents('print', $(this).data('type'));
        });

        $(document).on('click', '.doc-email-button', email_get_documents);
        $(document).on('click', '.docs-email-button', email_get_documents);
        $(document).on('click', '#send_email_button', email_documents);

        $(document).on('click', '.delete-address-button', function() {
            $(this).closest('.row').remove();
        });


        function print_download_documents(task, type) {
            let document_ids = [];
            $('.check-document:checked').each(function () {
                document_ids.push($(this).data('document-id'));
            });

            let formData = new FormData();
            let Listing_ID = $('#Listing_ID').val();
            let Contract_ID = $('#Contract_ID').val();
            let Referral_ID = $('#Referral_ID').val();
            let transaction_type = $('#transaction_type').val();

            formData.append('document_ids', document_ids);
            formData.append('Listing_ID', Listing_ID);
            formData.append('Contract_ID', Contract_ID);
            formData.append('Referral_ID', Referral_ID);
            formData.append('transaction_type', transaction_type);
            formData.append('type', type);

            axios.post('/agents/doc_management/transactions/merge_documents', formData, axios_options)
                .then(function (response) {
                    let file_location = response.data.file_location;
                    let filename = response.data.filename;
                    if(task == 'print') {
                        print_pdf(file_location);
                    } else if(task == 'download') {
                        let a = document.createElement('A');
                        a.href = file_location;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        }

        function email_documents() {

            $('#send_email_button').html('<i class="fas fa-spinner fa-pulse mr-2"></i> Sending Email');
            let from = $('#email_from').val();
            let to_addresses = [];
            $('.to-addresses').each(function() {
                if($(this).find('.email-to-address').val() != '') {
                    to_addresses.push({
                        type: $(this).find('.email-address-type').val(),
                        address: $(this).find('.email-to-address').val()
                    });
                }
            });
            let subject = $('#email_subject').val();
            let message = $('#email_message').val();
            let attachments = [];
            $('#email_attachments').find('.attachment-row').each(function() {
                attachments.push({
                    filename: $(this).data('file-name'),
                    file_location: $(this).data('file-location')
                });
            });

            let formData = new FormData();
            formData.append('type', 'documents');
            formData.append('from', from);
            formData.append('to_addresses', JSON.stringify(to_addresses));
            formData.append('subject', subject);
            formData.append('message', message);
            formData.append('attachments', JSON.stringify(attachments));
            axios.post('/agents/doc_management/transactions/send_email', formData, axios_options)
            .then(function (response) {
                if(response.data.fail) {
                    $('#modal_danger').modal().find('.modal-body').html('The attachments you are sending are too large. They must total less than 20MB and they are currently '+response.data.attachment_size+'MB');
                }
                $('#send_email_button').html('<i class="fad fa-share mr-2"></i> Send Email');
                $('#send_email_modal').modal('hide');
                toastr['success']('Documents Successfully Emailed');
            })
            .catch(function (error) {
                console.log(error);
            });
        }

        function email_get_documents() {

            let docs_type = $(this).data('docs-type') ? $(this).data('docs-type') : '';
            let document_ids = [];
            if(docs_type) {
                $('.check-document:checked').each(function () {
                    document_ids.push($(this).data('document-id'));
                });
            } else {
                document_ids.push($(this).data('document-id'));
            }

            let formData = new FormData();
            let Listing_ID = $('#Listing_ID').val();
            let Contract_ID = $('#Contract_ID').val();
            let Referral_ID = $('#Referral_ID').val();
            let transaction_type = $('#transaction_type').val();

            formData.append('document_ids', document_ids);
            formData.append('Listing_ID', Listing_ID);
            formData.append('Contract_ID', Contract_ID);
            formData.append('Referral_ID', Referral_ID);
            formData.append('transaction_type', transaction_type);
            formData.append('docs_type', docs_type);
            formData.append('type', 'filled');

            axios.post('/agents/doc_management/transactions/email_get_documents', formData, axios_options)
                .then(function (response) {

                    let file_locations = response.data.file_locations;
                    let filenames = response.data.filenames;

                    file_locations.forEach(function (file_location, index) {
                        let file_name = filenames[index];
                        let attachment = ' \
                        <div class="d-flex justify-content-start attachment-row" data-file-name="' + file_name + '" data-file-location="' + file_location + '"> \
                            <div><a href="javascript: void(0)" class="delete-attachment-button"><i class="fal fa-times text-danger mr-2"></i></a></div> \
                            <div>' + file_name + '</div> \
                        </div>';
                        $('#email_attachments').append(attachment);
                    });

                    $('.delete-attachment-button').on('click', function() {
                        $(this).closest('.attachment-row').remove();
                    });

                    $('#send_email_modal').modal();
                    $('#send_email_modal').on('hide.bs.modal', function () {
                        $('#email_attachments').html('');
                        $('.new-address').remove();
                    });

                    $('.add-address-button').off('click').on('click', function() {
                        let new_address_row = ' \
                        <div class="row to-addresses new-address"> \
                            <div class="col-2"> \
                                <select class="custom-form-element form-select form-select-no-cancel form-select-no-search email-address-type"> \
                                    <option value="to">To:</option> \
                                    <option value="cc" selected>Cc:</option> \
                                    <option value="bcc">Bcc:</option> \
                                </select> \
                            </div> \
                            <div class="col-9"> \
                                <input type="text" class="custom-form-element form-input email-to-address"> \
                            </div> \
                            <div class="col-1"> \
                                <div class="h-100 d-flex justify-content-end align-items-center"> \
                                    <button class="btn btn-sm btn-danger delete-address-button"><i class="fal fa-times"></i></button> \
                                </div> \
                            </div> \
                        </div> \
                        ';
                        $(new_address_row).insertBefore($(this).closest('.row'));
                        form_elements();

                    });


                })
                .catch(function (error) {
                    console.log(error);
                });
        }

        function duplicate_document() {
            let document_id = $(this).data('document-id');
            let file_type = $(this).data('file-type');
            let Listing_ID = $('#Listing_ID').val();
            let Contract_ID = $('#Contract_ID').val();
            let Referral_ID = $('#Referral_ID').val();
            let transaction_type = $('#transaction_type').val();

            let formData = new FormData();
            formData.append('document_id', document_id);
            formData.append('file_type', file_type);
            formData.append('Listing_ID', Listing_ID);
            formData.append('Contract_ID', Contract_ID);
            formData.append('Referral_ID', Referral_ID);
            formData.append('transaction_type', transaction_type);
            axios.post('/agents/doc_management/transactions/duplicate_document', formData, axios_options)
                .then(function (response) {
                    load_tabs('documents');
                    toastr['success']('Document successfully duplicated');
                })
                .catch(function (error) {
                    console.log(error);
                });
        }

        function show_split_document() {
            $('#folder_id').val($(this).data('folder'));
            let document_id = $(this).data('document-id');
            let checklist_id = $(this).data('checklist-id');
            let file_type = $(this).data('file-type');
            let file_name = $(this).data('file-name');
            let transaction_type = $('#transaction_type').val();

            $('#split_document_modal').modal();
            axios.get('/agents/doc_management/transactions/get_split_document_html', {
                params: {
                    document_id: document_id,
                    checklist_id: checklist_id,
                    file_type: file_type,
                    file_name: file_name,
                    transaction_type: transaction_type
                },
                headers: {
                    'Accept-Version': 1,
                    'Accept': 'text/html',
                    'Content-Type': 'text/html'
                }
            })
                .then(function (response) {
                    $('#split_document_container').html(response.data);
                    setTimeout(function () {

                        $('.image-slider').draggable({ axis: "x" });
                        $('.selected-images-slider').draggable({ axis: "x" });

                        $(document).on('click', '.image-zoom-button', function () {
                            $('#image_zoom_modal').modal();
                            $('#image_zoom_div').html('<img src="' + $(this).data('image-src') + '" class="image-preview">');
                        });

                        $(document).on('click', '.add-to-selected-button', function () {
                            let image_holder = $(this).closest('.image-holder');
                            image_holder.appendTo('.selected-images-slider').find('.image-order').text(image_holder.index() + 1);
                            $('.add-docs-to-checklist-item-button, #save_document_name_button').prop('disabled', false);
                        });
                        $(document).on('click', '.remove-from-selected-button', function () {

                            let image_holder = $(this).closest('.image-holder');
                            image_holder.prependTo('.image-slider');

                            let image_divs = $('.image-slider').find('.image-holder');
                            image_divs.sort(function (a, b) {
                                return $(a).data('index') - $(b).data('index')
                            });
                            $('.image-slider').html(image_divs);

                            if ($('.selected-images-slider').find('.image-holder').length == 0) {
                                $('.add-docs-to-checklist-item-button, #save_document_name_button').prop('disabled', true);
                            } else {
                                $('.selected-images-slider').find('.image-holder').each(function () {
                                    let index = $(this).index() + 1;
                                    $(this).find('.image-order').text(index);
                                });
                            }

                        });

                        form_elements();
                        $('[data-toggle="popover"]').popover();

                        $('#save_document_name_button').on('click', function() {
                            $(this).html('<i class="fas fa-spinner fa-pulse mr-2"></i> Saving...');
                            save_document_name($(this));
                        });
                        $('.add-docs-to-checklist-item-button').on('click', function() {
                            $(this).html('<i class="fas fa-spinner fa-pulse mr-2"></i> Adding...');
                            save_document_name($(this));
                        });

                    }, 500);
                })
                .catch(function (error) {
                    console.log(error);
                });

        }

        function save_document_name(ele) {

            let Listing_ID = $('#Listing_ID').val();
            let Contract_ID = $('#Contract_ID').val();
            let Referral_ID = $('#Referral_ID').val();
            let transaction_type = $('#transaction_type').val();
            let Agent_ID = $('#Agent_ID').val();
            let folder_id = $('#folder_id').val();
            let file_id = ele.data('file-id') ?? null;

            let checklist_item_id = ele.data('checklist-item-id') ?? null;
            let checklist_id = ele.data('checklist-id') ?? null;
            let validate = 'yes';
            let document_name = ele.data('upload-id');
            if (!checklist_item_id) {
                let form = $('#document_name_form');
                validate = validate_form(form);
                document_name = $('#document_name').val();
            }
            if(document_name == '0') {
                document_name = ele.closest('.list-group-item').find('.document-name').text()
            }

            if (validate == 'yes') {

                let image_ids = [];
                let file_type = '';
                $('.selected-images-container').find('.image-holder').each(function () {
                    file_type = $(this).data('file-type');
                    image_ids.push($(this).data('document-image-id'));
                });

                let formData = new FormData();
                formData.append('Listing_ID', Listing_ID);
                formData.append('Contract_ID', Contract_ID);
                formData.append('Referral_ID', Referral_ID);
                formData.append('transaction_type', transaction_type);
                formData.append('Agent_ID', Agent_ID);
                formData.append('folder_id', folder_id);
                formData.append('document_name', document_name);
                formData.append('image_ids', image_ids);
                formData.append('file_type', file_type);
                formData.append('file_id', file_id);
                formData.append('checklist_item_id', checklist_item_id);
                formData.append('checklist_id', checklist_id);
                axios.post('/agents/doc_management/transactions/save_split_document', formData, axios_options)
                    .then(function (response) {
                        toastr['success']('New Document Successfully Created');
                        $('.add-docs-to-checklist-item-button').html('<i class="fa fa-plus mr-1 mr-sm-2"></i> Add').prop('disabled', true);
                        $('#save_document_name_button').html('<i class="fa fa-save mr-2"></i> Save Document').prop('disabled', true);
                        $('.selected-images-slider').html('');
                        $('#split_document_modal').on('hide.bs.modal', function () {
                            load_tabs('documents');
                            load_checklist_on_tab_click();
                        });
                        // change status and count of checklist items
                        ele.parent().next().html('<span class="badge checklist-item-badge bg-blue-light text-primary p-1" title="We have received your document for this item. It is in the review process"><span class="d-none d-sm-inline-block"><i class="fal fa-stopwatch fa-lg mr-1"></i> </span>Pending</span>');
                        let count = ele.closest('.list-group-item').find('.docs-count-badge').text();
                        count = parseInt(count);
                        ele.closest('.list-group-item').find('.docs-count-badge').text(count + 1);

                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }
        }

        function show_rename_document() {
            let document_id = $(this).data('document-id');
            let document_name = $(this).data('document-name');
            $('#rename_document_modal').modal();
            $('#new_document_name').val(document_name);
            $('#save_rename_document_button').on('click', function () {
                save_rename_document(document_id);
            });
        }

        function save_rename_document(document_id) {
            let new_name = $('#new_document_name').val();

            let formData = new FormData();
            formData.append('document_id', document_id);
            formData.append('new_name', new_name);
            axios.post('/agents/doc_management/transactions/save_rename_document', formData, axios_options)
                .then(function (response) {
                    toastr['success']('Document Successfully Renamed')
                    load_tabs('documents', false);
                    load_checklist_on_tab_click();
                })
                .catch(function (error) {
                    console.log(error);
                });
        }

        window.show_add_to_checklist = function() {

            let button = $(this);

            if($('#questions_confirmed').val() == 'yes') {

                add_to_checklist(button);

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
                            add_to_checklist(button);
                            $('#questions_confirmed').val('yes');

                        })
                        .catch(function (error) {
                            console.log(error);
                        });

                    }

                });

            }

        }

        function add_to_checklist(button) {
            let checklist_id = button.data('checklist-id');
            let document_ids;
            let assigned = 'no';
            // single button add
            if (button.data('document-id')) {

                document_ids = [button.data('document-id')];
                if (button.hasClass('assigned')) {
                    assigned = 'yes';
                }

            } else {
                // selecting multiple forms
                document_ids = [];
                if ($('.check-document:checked').not('.assigned').length == 0) {
                    $('#modal_danger').modal().find('.modal-body').html('All documents you selected have already been assigned');
                    return false;
                } else {
                    $('.check-document:checked').not('.assigned').each(function () {
                        document_ids.push($(this).data('document-id'));
                    });
                    // if any of the docs are already assigned the user will be notified and they will be excluded
                    if ($('.assigned:checked').length > 0) {
                        assigned = 'yes';
                    }
                }

            }

            if (assigned == 'yes') {
                // notify user
                $('#modal_info').modal().find('.modal-body').html('<div class="d-flex justify-content-start align-items-center"><div class="mr-4"><i class="fal fa-exclamation-triangle fa-2x text-danger"></i></div><div class="text-gray text-center">Some documents you selected have already be assigned and will not be included</div></div>');
                let button = $('#modal_info').find('.modal-footer').find('.btn');
                button.text('Continue');

                button.off('click').on('click', function() {
                    add_to_checklist_html(checklist_id, document_ids);
                });

            } else {

                add_to_checklist_html(checklist_id, document_ids);

            }
        }

        function add_to_checklist_html(checklist_id, document_ids) {

            let transaction_type = $('#transaction_type').val();
            axios.get('/agents/doc_management/transactions/add_document_to_checklist_item_html', {
                params: {
                    document_ids: document_ids,
                    checklist_id: checklist_id,
                    transaction_type: transaction_type
                },
                headers: {
                    'Accept-Version': 1,
                    'Accept': 'text/html',
                    'Content-Type': 'text/html'
                }
            })
                .then(function (response) {
                    $('#add_items_to_checklist_div').html(response.data);

                    disable_arrows();

                    $('.doc-list-arrow').on('click', function() {
                        let new_active_item = '';
                        if($(this).data('dir') == 'up') {
                            new_active_item = $('.add-to-checklist-document-div.active').prev('.add-to-checklist-document-div');
                        } else {
                            new_active_item = $('.add-to-checklist-document-div.active').next('.add-to-checklist-document-div');
                        }
                        arrows(new_active_item);
                    });

                    $('.add-to-checklist-document-div').on('click', function() {
                        arrows($(this));
                    });

                    $('#add_to_checklist_documents_wrapper').off('wheel').on('wheel', function(e){
                        if(e.originalEvent.deltaY > 0) {
                            $('.add-to-checklist-document-div.active').next('.add-to-checklist-document-div').trigger('click');
                        } else {
                            $('.add-to-checklist-document-div.active').prev('.add-to-checklist-document-div').trigger('click');
                        }
                        return false;
                    });

                    $('.assign-button').off('click').on('click', assign_document);

                    $('#save_add_to_checklist_button').off('click').on('click', function () {
                        $(this).hide();
                        save_add_to_checklist($(this).data('checklist-id'));
                    });

                    let matches = [];
                    $('.add-to-checklist-document-div').each(function() {
                        let doc_name = $(this).data('file-name');
                        let match_document_id = $(this).data('document-id');
                        if($('.assign-button[data-file-name="' + doc_name + '"]').length == 1) {
                            matches.push(match_document_id);
                        }
                    });


                    if(matches.length > 0) {
                        $('#confirm_matches_modal').modal().find('#match_count').text(matches.length);
                        $('#confirm_matches_button').off('click').on('click', confirm_matches);
                        $('#cancel_matches_button').on('click', function() {
                            $('#add_to_checklist_modal').modal();
                            disable_closing_docs();
                        });
                    } else {
                        $('#add_to_checklist_modal').modal();
                        disable_closing_docs();
                    }

                })
                .catch(function (error) {
                    console.log(error);
                });

        }


        function disable_closing_docs() {

            $('.assign-button.closing_doc, .assign-button.release').addClass('disabled').prop('disabled', true);
            if(!$('.add-to-checklist-document-div.active').hasClass('assigned')) {
                if($('.assign-button.contract').not('.rejected').length > 0) {
                    if($('.assign-button.contract').closest('.list-group-item').find('.docs').length > 0) {
                        $('.assign-button.closing_doc, .assign-button.release').removeClass('disabled').prop('disabled', false);
                    }
                }
            }
        }

        function confirm_matches() {
            let release_submitted = false;
            $('.add-to-checklist-document-div').each(function() {
                let doc = $(this);
                let doc_name = doc.data('file-name');
                if($('.assign-button[data-file-name="' + doc_name + '"]').length == 1) {
                    doc.trigger('click');
                    let id = 'rand_'+Math.floor(Math.random() * 10000000);
                    $('.assign-button[data-file-name="' + doc_name + '"]').prop('id', id).trigger('click');
                    setTimeout(function() {
                        document.getElementById(id).scrollIntoView();
                    }, 500);
                    if($('#'+id).hasClass('release') || $(id).hasClass('contract')) {
                        release_submitted = true;
                    }
                }
            });
            $('#confirm_matches_modal').modal('hide');
            $('#add_to_checklist_modal').modal();
            $('.add-to-checklist-document-div').not('.assigned').first().trigger('click');

            if(release_submitted == true) {
                // if contract item in checklist and not rejected
                if($('.assign-button.contract').not('.rejected').length > 0) {
                    // if contract not submitted
                    if($('.assign-button.contract').closest('.list-group-item').find('.docs').length == 0) {
                        $('.assign-button.closing_doc, .assign-button.release').closest('.list-group-item').find('.delete-doc').trigger('click');
                        $('#modal_danger').modal().find('.modal-body').html('You must submit a contract before you can submit any closing documents or a release');
                    }
                }
            }

        }

        function arrows(new_active_item) {

            let active_item = $('.add-to-checklist-document-div.active');
            active_item.removeClass('active z-depth-2 p-4').addClass('p-2');

            let wrapper = $('#add_to_checklist_documents_wrapper');

            new_active_item.addClass('active z-depth-2 p-4');
            if(new_active_item.hasClass('assigned')) {
                $('.assign-button').prop('disabled', true);
            } else {
                $('.assign-button').not('.disabled').prop('disabled', false);
            }
            wrapper.scrollTop(wrapper.scrollTop() + new_active_item.position().top - (wrapper.height()/2) + (new_active_item.height()/2) - 41);

            disable_arrows();

        }


        function disable_arrows() {

            let active_item = $('.add-to-checklist-document-div.active');
            $('.doc-list-arrow').prop('disabled', true);
            if(active_item.prev('.add-to-checklist-document-div').length > 0) {
                $('.doc-list-arrow[data-dir="up"]').prop('disabled', false);
            }
            if(active_item.next('.add-to-checklist-document-div').length > 0) {
                $('.doc-list-arrow[data-dir="down"]').prop('disabled', false);
            }

        }


        function assign_document() {

            let checklist_id = $(this).data('checklist-id');
            let checklist_item_id = $(this).data('checklist-item-id');
            let active_div = $('.add-to-checklist-document-div.active');
            let document_id = active_div.data('document-id');
            let file_name = active_div.data('file-name');
            let docs_div = $(this).closest('.list-group-item').find('.submitted-docs');

            docs_div.closest('.submitted-docs-div').show();

            docs_div.prepend('<div class="added-document d-flex justify-content-start align-items-center docs" data-document-id="'+document_id+'" data-checklist-id="'+checklist_id+'" data-checklist-item-id="'+checklist_item_id+'"><div><a href="javascript: void(0)" class="delete-doc" data-document-id="'+document_id+'"><i class="fad fa-times-circle text-danger mr-2"></i></a></div><div class="text-orange">'+file_name.substring(0, 70)+'</div></div>');

            active_div.addClass('assigned');
            if(active_div.nextAll('.add-to-checklist-document-div').not('.assigned').length > 0) {
                active_div.nextAll('.add-to-checklist-document-div').not('.assigned').first().trigger('click');
            } else {
                active_div.trigger('click');
            }

            disable_closing_docs();

            $('.delete-doc').on('click', function() {
                let remove_document_id = $(this).data('document-id');
                let container = $(this).closest('.submitted-docs-div');

                $(this).closest('.added-document').remove();
                $('.add-to-checklist-document-div[data-document-id="' + remove_document_id + '"]').removeClass('assigned');
                if(container.find('.added-document').length == 0) {
                    container.hide();
                }

                if(document_id == remove_document_id) {
                    $('.assign-button').prop('disabled', false);
                }
                disable_closing_docs();

            });

        }

        function save_add_to_checklist(checklist_id) {


            let checklist_items = [];
            $('.added-document').each(function () {

                let checklist_id = $(this).data('checklist-id');
                let checklist_item_id = $(this).data('checklist-item-id');
                let document_id = $(this).data('document-id');

                checklist_items.push({
                    checklist_id: checklist_id,
                    checklist_item_id: checklist_item_id,
                    document_id: document_id
                });

            });

            let formData = new FormData();

            let Agent_ID = $('#Agent_ID').val();
            let Listing_ID = $('#Listing_ID').val();
            let Contract_ID = $('#Contract_ID').val();
            let Referral_ID = $('#Referral_ID').val();
            let transaction_type = $('#transaction_type').val();

            checklist_items = JSON.stringify(checklist_items);

            formData.append('Agent_ID', Agent_ID);
            formData.append('Listing_ID', Listing_ID);
            formData.append('Contract_ID', Contract_ID);
            formData.append('Referral_ID', Referral_ID);
            formData.append('transaction_type', transaction_type);
            formData.append('checklist_items', checklist_items);
            axios.post('/agents/doc_management/transactions/save_assign_documents_to_checklist', formData, axios_options)
                .then(function (response) {
                    toastr['success']('Documents Successfully Added')
                    load_tabs('documents', false);
                    load_checklist_on_tab_click();
                    if(response.data) {
                        if(response.data.release_submitted == 'yes') {
                            $('#cancel_contract_button').trigger('click');
                            load_details_header();
                        }
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });

        }

        function show_add_checklist_template() {
            $('#add_checklist_template_modal').modal();
        }

        function tag_search() {
            let selected_tags = $('#form_categories_search').find('option:checked');
            if (selected_tags.length > 0) {

                let form_group = $('.select-form-group').val();

                if (form_group == 'all') {

                    $('.form-group-div').each(function () {
                        let form_group = $(this);
                        $(this).find('.form-name').hide();
                        selected_tags.each(function () {
                            let tag = $(this).val();
                            form_group.find('.form-name').each(function () {
                                if ($(this).data('tags').match(new RegExp(tag, 'i'))) {
                                    // show name
                                    $(this).show();
                                }
                            });
                        });
                    });

                } else {

                    $('[data-form-group-id="' + form_group + '"]').find('.form-name').hide();
                    selected_tags.each(function () {
                        let tag = $(this).val();
                        $('[data-form-group-id="' + form_group + '"]').find('.form-name').each(function () {
                            if ($(this).data('tags').match(new RegExp(tag, 'i'))) {
                                // show name
                                $(this).show();
                            }
                        });
                    });

                }
            } else {
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
            }
        }

        /* function form_search() {
            let v = $('.form-search').val();
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
                    let regex = new RegExp(v, 'i');
                    if ($(this).data('text').match(regex)) {
                        // show name
                        $(this).show();
                        // show header
                        $(this).closest('.form-group-div').find('.list-group-header').show();
                    }
                });
            }
        } */

        function save_add_template_documents(type) {

            $('[data-dismiss="modal"]').trigger('click');

            let Agent_ID = $('#Agent_ID').val();
            let Listing_ID = $('#Listing_ID').val();
            let Contract_ID = $('#Contract_ID').val();
            let Referral_ID = $('#Referral_ID').val();
            let transaction_type = $('#transaction_type').val();

            let folder = '';
            let forms = '';
            let form = '';
            if (type == 'checklist') {
                folder = $('#checklist_templates_folder').val();
                forms = $('.checklist-template-form:checked');
                form = $('#add_checklist_template_form');
            } else {
                folder = $('#individual_templates_folder').val();
                forms = $('.individual-template-form:checked');
                form = $('#add_individual_template_form');
            }

            let formData = new FormData();
            formData.append('Agent_ID', Agent_ID);
            formData.append('Listing_ID', Listing_ID);
            formData.append('Contract_ID', Contract_ID);
            formData.append('Referral_ID', Referral_ID);
            formData.append('transaction_type', transaction_type);
            formData.append('folder', folder);

            let files = [];
            let c = 0;
            forms.each(function () {

                let file_data = {};
                file_data['file_id'] = $(this).data('file-id');
                file_data['file_name'] = $(this).data('file-name');
                file_data['file_size'] = $(this).data('file-size');
                file_data['file_name_display'] = $(this).data('file-name-display');
                file_data['pages_total'] = $(this).data('pages-total');
                file_data['file_location'] = $(this).data('file-location');
                file_data['file_location'] = $(this).data('file-location');
                file_data['order'] = c;
                files.push(file_data);

                c += 1;

            });

            let files_json = JSON.stringify(files);
            formData.append('files', files_json);

            let validate = validate_form(form);
            if (validate == 'yes') {

                let loading_html = ' \
                <div class="h5-responsive text-white mb-3">Importing Documents...</div> \
                <div class="w-100 text-left document-loading-container"> \
                    <div id="loading_div"></div> \
                </div> \
                ';

                global_loading_on('', loading_html);

                files.forEach(function(file, index) {

                    let interval = file['file_size'] * 25000;

                    let file_html = ' \
                    <div class="text-white w-100 p-1"> \
                        <span>'+file['file_name']+'</span> \
                        <div class="progress"> \
                            <div id="progress_'+index+'" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div> \
                        </div> \
                    </div> \
                    ';
                    setTimeout(function () {
                        $('#loading_div').append(file_html);
                        let progress_bar = $('#progress_'+index);
                        let width = 0;
                        let progress_interval = setInterval(progress, interval / 100);
                        function progress() {
                            if (width >= 100) {
                                clearInterval(progress_interval);
                            } else {
                                width++;
                                progress_bar.css({ width: width + '%' }).attr('aria-valuenow', width);
                            }
                        }
                        document.getElementById('progress_'+index).scrollIntoView();
                    }, index * 1000);

                });

                axios.post('/agents/doc_management/transactions/save_add_template_documents', formData, axios_options)
                    .then(function (response) {
                        toastr['success']('Documents Successfully Added')
                        load_tabs('documents');
                        load_checklist_on_tab_click();
                        let sortables = $('.document-div[data-folder-id="' + folder + '"]');
                        reorder_documents(sortables);
                        global_loading_off();
                        $('.progress-bar').css({ width: '0%' });

                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }
        }

        function show_add_individual_template() {
            $('#add_individual_template_modal').modal();
            // search forms
            $('.form-search').on('keyup', function() {
                form_search($(this))
            });
            // search by tag
            $('#form_categories_search').change(tag_search);
            // select and show form groups
            $('.select-form-group').on('change', function () {
                select_form_group();
            });
        }

        window.select_form_group = function () {
            // clear search input
            $('.form-search').val('').trigger('change');
            $('#form_categories_search').val('').trigger('change');
            select_refresh();
            // if all show everything or just the selected group
            if ($('.select-form-group').val() == 'all') {
                $('.form-group-div, .list-group-header, .form-name').show();
            } else {
                $('.list-group-header, .form-name').show();
                $('.form-group-div').hide();
                $('[data-form-group-id="' + $('.select-form-group').val() + '"]').show();
            }
        }

        function show_upload_documents() {
            $('#upload_documents_modal').modal();
            upload_documents();
            $('#save_upload_documents_button').on('click', function () {
                $(this).html('<span class="spinner-border spinner-border-sm mr-2"></span> Uploading Documents');
                $("#file_upload").dmUploader('start');
            });
            $(document).on('click', '.cancel-upload', function () {
                $("#file_upload").dmUploader('cancel', $(this).data('id'));
                $(this).closest('li').remove();
                $('#save_upload_documents_button').html('<i class="fad fa-check mr-2"></i> Upload Documents');
            });
        }

        function show_add_folder() {
            $('#add_folder_modal').modal();
            setTimeout(function () {
                $('#new_folder_name').focus();
            }, 500);
            $('#save_add_folder_button').click(add_folder);
        }

        function add_folder() {
            let form = $('#add_folder_form');
            let validate = validate_form(form);
            if (validate == 'yes') {
                let folder = $('#new_folder_name').val();
                let Listing_ID = $('#Listing_ID').val();
                let Contract_ID = $('#Contract_ID').val();
                let Referral_ID = $('#Referral_ID').val();
                let Agent_ID = $('#Agent_ID').val();
                let transaction_type = $('#transaction_type').val();
                let formData = new FormData();
                formData.append('folder', folder);
                formData.append('Listing_ID', Listing_ID);
                formData.append('Contract_ID', Contract_ID);
                formData.append('Referral_ID', Referral_ID);
                formData.append('Agent_ID', Agent_ID);
                formData.append('transaction_type', transaction_type);
                axios.post('/agents/doc_management/transactions/add_folder', formData, axios_options)
                    .then(function (response) {
                        load_tabs('documents', false);
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }
        }

        function confirm_delete_folder(folder_id) {
            $('#confirm_delete_folder_modal').modal();
            $('#confirm_delete_folder_button').on('click', function () {
                delete_folder(folder_id)
            });
        }

        function delete_folder(folder_id) {
            let Listing_ID = $('#Listing_ID').val();
            let Contract_ID = $('#Contract_ID').val();
            let Referral_ID = $('#Referral_ID').val();
            let transaction_type = $('#transaction_type').val();
            let formData = new FormData();
            formData.append('folder_id', folder_id);
            formData.append('Listing_ID', Listing_ID);
            formData.append('Contract_ID', Contract_ID);
            formData.append('Referral_ID', Referral_ID);
            formData.append('transaction_type', transaction_type);
            axios.post('/agents/doc_management/transactions/delete_folder', formData, axios_options)
                .then(function (response) {
                    load_tabs('documents', false);
                    load_checklist_on_tab_click();
                })
                .catch(function (error) {
                    console.log(error);
                });
        }

        function show_move_documents() {
            $('#move_documents_modal').modal();
            $('#save_move_documents_button').click(move_documents);
        }

        function move_documents() {
            $('.documents-container').fadeOut('1000');
            let document_ids = [];
            $('.check-document:checked').each(function () {
                document_ids.push($(this).data('document-id'));
            });

            let formData = new FormData();
            let Listing_ID = $('#Listing_ID').val();
            let Contract_ID = $('#Contract_ID').val();
            let Referral_ID = $('#Referral_ID').val();
            let transaction_type = $('#transaction_type').val();
            let folder_id = $('#move_documents_folder').val();
            formData.append('document_ids', document_ids);
            formData.append('Listing_ID', Listing_ID);
            formData.append('Contract_ID', Contract_ID);
            formData.append('Referral_ID', Referral_ID);
            formData.append('transaction_type', transaction_type);
            formData.append('folder_id', folder_id);
            axios.post('/agents/doc_management/transactions/move_documents_to_folder', formData, axios_options)
                .then(function (response) {
                    load_tabs('documents');
                    load_checklist_on_tab_click();
                })
                .catch(function (error) {
                    console.log(error);
                });
        }

        function show_delete_one_document() {
            let document_id = $(this).data('document-id');
            let document_name = $(this).data('document-name');
            $('#confirm_delete_document_modal').modal();
            $('#delete_document_name').text(document_name);
            $('#confirm_delete_document_button').off('click').on('click', function () {
                delete_one_document(document_id);
            });
        }

        function delete_one_document(document_id) {

            $('.documents-container').fadeOut('1000');
            let document_ids = [document_id];
            let formData = new FormData();
            let Listing_ID = $('#Listing_ID').val();
            let Contract_ID = $('#Contract_ID').val();
            let Referral_ID = $('#Referral_ID').val();
            let transaction_type = $('#transaction_type').val();
            formData.append('document_ids', document_ids);
            formData.append('Listing_ID', Listing_ID);
            formData.append('Contract_ID', Contract_ID);
            formData.append('Referral_ID', Referral_ID);
            formData.append('transaction_type', transaction_type);
            axios.post('/agents/doc_management/transactions/move_documents_to_trash', formData, axios_options)
                .then(function (response) {
                    //button.closest('.document-div').appendTo($('.folder-div').last().find('[id^=documents_folder_]'));
                    load_tabs('documents', false);
                    load_checklist_on_tab_click();
                    toastr['success']('Document Moved To Trash');
                })
                .catch(function (error) {
                    console.log(error);
                });
        }

        function show_delete_documents() {
            $('#confirm_delete_documents_modal').modal();
            $('#confirm_delete_documents_button').click(delete_documents);
        }

        function delete_documents() {
            $('.documents-container').fadeOut('1000');
            let document_ids = [];
            $('.check-document:checked').each(function () {
                document_ids.push($(this).data('document-id'));
            });

            let formData = new FormData();

            let Listing_ID = $('#Listing_ID').val();
            let Contract_ID = $('#Contract_ID').val();
            let Referral_ID = $('#Referral_ID').val();
            let transaction_type = $('#transaction_type').val();

            formData.append('document_ids', document_ids);
            formData.append('Listing_ID', Listing_ID);
            formData.append('Contract_ID', Contract_ID);
            formData.append('Referral_ID', Referral_ID);
            formData.append('transaction_type', transaction_type);
            axios.post('/agents/doc_management/transactions/move_documents_to_trash', formData, axios_options)
                .then(function (response) {
                    load_tabs('documents', false);
                    load_checklist_on_tab_click();
                    toastr['success']('Documents Moved To Trash');
                })
                .catch(function (error) {
                    console.log(error);
                });
        }

        function show_bulk_options() {
            if ($('#add_documents_div').hasClass('show')) {
                $('#add_documents_div').collapse('hide');
            }
            if ($('.check-document:checked').length > 0) {
                $('#bulk_options_div').collapse('show');
                return false;
            } else {
                $('#bulk_options_div').collapse('hide');
                $(this).closest('.folder-div').find('.check-all').prop('checked', false);
            }
            $('.check-document').closest('.document-div').removeClass('bg-blue-light');
            $('.check-document:checked').closest('.document-div').addClass('bg-blue-light');

        }

        function check_all() {
            if ($('#add_documents_div').hasClass('show')) {
                $('#add_documents_div').collapse('hide');
            }
            if (!$(this).closest('.folder-header').next('.collapse').hasClass('show')) {
                $(this).closest('.folder-header').next('.collapse').collapse('show');
            }
            if ($(this).is(':checked')) {
                $(this).closest('.folder-div').find('.document-div').find('input').prop('checked', true).trigger('change');
            } else {
                $(this).closest('.folder-div').find('.document-div').find('input').prop('checked', false).trigger('change');
            }
        }

        function toggle_caret() {
            let i = $(this).find('i');
            if (i.hasClass('fa-angle-right')) {
                i.removeClass('fa-angle-right').addClass('fa-angle-down');
            } else if (i.hasClass('fa-angle-down')) {
                i.removeClass('fa-angle-down').addClass('fa-angle-right');
            }
        }

        function print_pdf(url) {

            var wnd = window.open(url);
            setTimeout(function() {
                wnd.print();
                wnd.close();
            }, 1000);

        }



    });

}
