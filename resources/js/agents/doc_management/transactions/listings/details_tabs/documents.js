if (document.URL.match(/listing_details/)) {

    $(document).ready(function () {

        $(document).on('click', '.add-folder-button', show_add_folder);

        $(document).on('click', '#upload_documents_button', show_upload_documents);

        $(document).on('click', '#add_checklist_template_button', show_add_checklist_template);

        $(document).on('click', '#save_add_checklist_template_button', function () {
            save_add_template_documents('checklist');
        });

        $(document).on('click', '#add_individual_template_button', show_add_individual_template);

        $(document).on('click', '#save_add_individual_template_button', function () {
            save_add_template_documents('individual');
        });

        $(document).on('click', '.check-all', check_all);

        $(document).on('change', '.check-document', show_bulk_options);

        $(document).on('click', '#delete_documents_button', show_delete_documents);

        $(document).on('click', '#move_documents_button', show_move_documents);

        $(document).on('click', '.folder-collapse', toggle_caret);

        $(document).on('click', '.doc-delete-button', delete_one_document);

        $(document).on('click', '.delete-folder-button', function () {
            confirm_delete_folder($(this).data('folder-id'));
        });



    });



    function show_add_checklist_template() {
        $('#add_checklist_template_modal').modal();
    }

    function tag_search() {
        let selected_tags = $('#form_tag_search').find('option:checked');
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

    function form_search() {
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
                if ($(this).data('text').match(new RegExp(v, 'i'))) {
                    // show name
                    $(this).show();
                    // show header
                    $(this).closest('.form-group-div').find('.list-group-header').show();
                }
            });
        }
    }

    function save_add_template_documents(type) {
        let Agent_ID = $('#Agent_ID').val();
        let Listing_ID = $('#Listing_ID').val();
        let folder = forms = form = '';
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
        formData.append('folder', folder);

        let files = [];
        forms.each(function () {
            let file_data = {};
            file_data['file_id'] = $(this).data('file-id');
            file_data['file_name'] = $(this).data('file-name');
            file_data['file_name_display'] = $(this).data('file-name-display');
            file_data['pages_total'] = $(this).data('pages-total');
            file_data['file_location'] = $(this).data('file-location');
            files.push(file_data);
        });

        files = JSON.stringify(files);
        formData.append('files', files);

        let validate = validate_form(form);
        if (validate == 'yes') {
            axios.post('/agents/doc_management/transactions/listings/save_add_template_documents', formData, axios_options)
                .then(function (response) {
                    toastr['success']('Documents Successfully Added')
                    load_tabs('documents');
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    }

    function show_add_individual_template() {
        $('#add_individual_template_modal').modal();
        // search forms
        $('#form_search').keyup(form_search);
        // search by tag
        $('#form_tag_search').change(tag_search);
        // select and show form groups
        $('.select-form-group').change(function () {
            select_form_group();
        });
    }

    window.select_form_group = function () {
        // clear search input
        $('#form_search').val('').trigger('change');
        $('#form_tag_search').val('').trigger('change');
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
        $('#save_upload_documents_button').click(function () {
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
            let Agent_ID = $('#Agent_ID').val();
            let formData = new FormData();
            formData.append('folder', folder);
            formData.append('Listing_ID', Listing_ID);
            formData.append('Agent_ID', Agent_ID);
            axios.post('/agents/doc_management/transactions/listings/add_folder', formData, axios_options)
                .then(function (response) {
                    load_tabs('documents');
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    }

    function confirm_delete_folder(folder_id) {
        $('#confirm_delete_folder_modal').modal();
        $('#confirm_delete_folder_button').click(function () {
            delete_folder(folder_id)
        });
    }

    function delete_folder(folder_id) {
        let Listing_ID = $('#Listing_ID').val();
        let formData = new FormData();
        formData.append('folder_id', folder_id);
        formData.append('Listing_ID', Listing_ID);
        axios.post('/agents/doc_management/transactions/listings/delete_folder', formData, axios_options)
            .then(function (response) {
                load_tabs('documents');
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
        let folder_id = $('#move_documents_folder').val();
        formData.append('document_ids', document_ids);
        formData.append('Listing_ID', Listing_ID);
        formData.append('folder_id', folder_id);
        axios.post('/agents/doc_management/transactions/listings/move_documents_to_folder', formData, axios_options)
            .then(function (response) {
                load_tabs('documents');
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    function delete_one_document() {
        $('.documents-container').fadeOut('1000');
        let document_ids = [$(this).data('document-id')];
        let formData = new FormData();
        let Listing_ID = $('#Listing_ID').val();
        formData.append('document_ids', document_ids);
        formData.append('Listing_ID', Listing_ID);
        axios.post('/agents/doc_management/transactions/listings/move_documents_to_trash', formData, axios_options)
            .then(function (response) {
                //button.closest('.document-div').appendTo($('.folder-div').last().find('[id^=documents_folder_]'));
                load_tabs('documents');
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
        formData.append('document_ids', document_ids);
        formData.append('Listing_ID', Listing_ID);
        axios.post('/agents/doc_management/transactions/listings/move_documents_to_trash', formData, axios_options)
            .then(function (response) {
                load_tabs('documents');
                toastr['success']('Documents Moved To Trash');
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    function show_bulk_options() {
        $('#add_documents_div').collapse('hide');
        if ($('.check-document:checked').length > 0) {
            $('#bulk_options_div').collapse('show');
        } else {
            $('#bulk_options_div').collapse('hide');
            $(this).closest('.folder-div').find('.check-all').prop('checked', false);
        }
        $('.check-document').closest('.document-div').removeClass('bg-blue-light');
        $('.check-document:checked').closest('.document-div').addClass('bg-blue-light');

    }

    function check_all() {
        $('#add_documents_div').collapse('hide');
        $(this).closest('.folder-header').next('.collapse').collapse('show');
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

}
