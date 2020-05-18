import Sortable from 'sortablejs';

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

        $(document).on('click', '.doc-delete-button', show_delete_one_document);

        $(document).on('click', '.delete-folder-button', function () {
            confirm_delete_folder($(this).data('folder-id'));
        });

        $(document).on('click', '.add-to-checklist-button', show_add_to_checklist);

        $(document).on('click', '.doc-rename-button', show_rename_document);

        $(document).on('click', '.doc-split-button', show_split_document);

        function show_split_document() {
            let document_id = $(this).data('document-id');
            let checklist_id = $(this).data('checklist-id');
            let file_type = $(this).data('file-type');
            $('#split_document_modal').modal();
            axios.get('/agents/doc_management/transactions/listings/get_split_document_html', {
                params: {
                    document_id: document_id,
                    checklist_id: checklist_id,
                    file_type: file_type
                },
                headers: {
                    'Accept-Version': 1,
                    'Accept': 'text/html',
                    'Content-Type': 'text/html'
                }
            })
                .then(function (response) {
                    $('#split_document_container').html(response.data);
                    setTimeout(function() {

                        $('.image-slider').draggable({ axis: "x" });
                        $('.selected-images-slider').draggable({ axis: "x" });

                        $(document).on('click', '.image-zoom-button', function() {
                            $('#image_zoom_modal').modal();
                            $('#image_zoom_div').html('<img src="'+$(this).data('image-src')+'" class="image-preview">');
                        });

                        $(document).on('click', '.add-to-selected-button', function() {
                            let image_holder = $(this).closest('.image-holder');
                            image_holder.appendTo('.selected-images-slider').find('.image-order').text(image_holder.index() + 1);
                            $('.add-docs-to-checklist-item-button, #save_document_name_button').prop('disabled', false);
                        });
                        $(document).on('click', '.remove-from-selected-button', function() {

                            let image_holder = $(this).closest('.image-holder');
                            image_holder.prependTo('.image-slider');

                            let image_divs = $('.image-slider').find('.image-holder');
                            image_divs.sort(function(a, b){
                                return $(a).data('index')-$(b).data('index')
                            });
                            $('.image-slider').html(image_divs);

                            if($('.selected-images-slider').find('.image-holder').length == 0) {
                                $('.add-docs-to-checklist-item-button, #save_document_name_button').prop('disabled', true);
                            } else {
                                $('.selected-images-slider').find('.image-holder').each(function() {
                                    let index = $(this).index() + 1;
                                    $(this).find('.image-order').text(index);
                                });
                            }

                        });

                        form_elements();
                        $('[data-toggle="popover"]').popover();

                        $('#save_document_name_button').click(save_document_name);
                        $('.add-docs-to-checklist-item-button').click(save_document_name);

                    }, 500);
                })
                .catch(function (error) {
                    console.log(error);
                });

        }

        function save_document_name() {

            let checklist_item_id = $(this).data('checklist-item-id') ?? null;
            let validate = 'yes';
            if(!checklist_item_id) {
                let form = $('#document_name_form');
                validate = validate_form(form);
            }
            if(validate == 'yes') {
                let document_name = $('#document_name').val() ?? null;
                let image_ids = [];
                let file_type = '';
                $('.selected-images-container').find('.image-holder').each(function () {
                    file_type = $(this).data('file-type');
                    image_ids.push($(this).data('document-image-id'));
                });

                let formData = new FormData();
                formData.append('document_name', document_name);
                formData.append('image_ids', image_ids);
                formData.append('file_type', file_type);
                formData.append('checklist_item_id', checklist_item_id);
                axios.post('/agents/doc_management/transactions/listings/save_split_document_to_documents', formData, axios_options)
                .then(function (response) {
                    console.log(response);
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
            $('#save_rename_document_button').click(function () {
                save_rename_document(document_id);
            });
        }

        function save_rename_document(document_id) {
            let new_name = $('#new_document_name').val();

            let formData = new FormData();
            formData.append('document_id', document_id);
            formData.append('new_name', new_name);
            axios.post('/agents/doc_management/transactions/listings/save_rename_document', formData, axios_options)
                .then(function (response) {
                    toastr['success']('Document Successfully Renamed')
                    load_tabs('documents');
                    load_tabs('checklist');
                })
                .catch(function (error) {
                    console.log(error);
                });
        }

        function show_add_to_checklist() {

            let checklist_id = $(this).data('checklist-id');
            let document_ids;
            let assigned = 'no';
            // single button add
            if ($(this).data('document-id')) {

                document_ids = [$(this).data('document-id')];
                if ($(this).hasClass('assigned')) {
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

                $('#modal_info').on('hidden.bs.modal', function () {
                    $('#add_to_checklist_modal').modal();
                    add_to_checklist_html(checklist_id, document_ids);
                });

            } else {

                $('#add_to_checklist_modal').modal();
                add_to_checklist_html(checklist_id, document_ids);

            }

        }

        function add_to_checklist_html(checklist_id, document_ids) {

            axios.get('/agents/doc_management/transactions/listings/add_document_to_checklist_item_html', {
                params: {
                    document_ids: document_ids,
                    checklist_id: checklist_id
                },
                headers: {
                    'Accept-Version': 1,
                    'Accept': 'text/html',
                    'Content-Type': 'text/html'
                }
            })
                .then(function (response) {
                    $('#add_items_to_checklist_div').html(response.data);

                    new Sortable(document.querySelector('#add_to_checklist_documents_div'), {
                        group: {
                            name: 'shared',
                        },
                        animation: 150,
                        sort: false,

                        onStart: function (evt) {
                            let source = evt.srcElement;
                            let el = evt.item;
                            // remove green background from match
                            $('.checklist-item-droparea').removeClass('doc-match');
                            // see if document matches any items and add green bg if so
                            if ($(window).width() > 576) {
                                if ($(source).prop('id') == 'add_to_checklist_documents_div') {
                                    let source_file_name = $(el).data('file-name');
                                    $('.add-to-checklist-item-div').each(function () {
                                        if ($(this).data('file-name') == source_file_name) {
                                            $(this).find('.checklist-item-droparea').addClass('doc-match');
                                            this.scrollIntoView({
                                                block: 'center',
                                            });
                                        }
                                    });
                                }
                            }
                        },
                        onEnd: function (evt) {

                            let source = evt.srcElement;
                            let target = evt.to;
                            let el = evt.item;

                            $('.checklist-item-droparea').removeClass('doc-match');

                            if ($(target).hasClass('checklist-item-droparea')) {
                                $(target).addClass('drop-activated');
                                $(target).find('.add-to-checklist-document-div').addClass('source-dropped');
                            }

                            show_drop_activated();

                        },
                    });

                    // loop through all item drop areas and add sortable
                    let drop_areas = document.getElementsByClassName('checklist-item-droparea');
                    drop_areas.forEach(function (el) {
                        new Sortable(el, {
                            group: {
                                name: 'shared',
                            },
                            animation: 150,
                            sort: false,
                            onStart: function (evt) {
                                let source = evt.srcElement;
                                let el = evt.item;
                                // remove source-dropped class
                                $(el).removeClass('source-dropped');
                                $(source).removeClass('drop-activated');
                            },
                            onEnd: function (evt) {
                                let source = evt.srcElement;
                                let target = evt.to;
                                // if not moving outside of items div readd source-dropped class
                                if ($(target).hasClass('checklist-item-droparea') && $(source).hasClass('checklist-item-droparea')) {
                                    $(target).find('.add-to-checklist-document-div').addClass('source-dropped');
                                    $(target).addClass('drop-activated');
                                }

                                setTimeout(function () {
                                    show_drop_activated();
                                }, 300);

                            },
                        });
                    });



                    $('#save_add_to_checklist_button').off('click').on('click', function () {
                        save_add_to_checklist($(this).data('checklist-id'));
                    });
                })
                .catch(function (error) {
                    console.log(error);
                });

        }

        function show_drop_activated() {
            $('.checklist-item-droparea').each(function () {

                if ($(this).find('.add-to-checklist-document-div').length == 0) {
                    $(this).removeClass('drop-activated');
                }
                $(this).find('.drop-div-title').prependTo($(this));

            });
        }

        function save_add_to_checklist(checklist_id) {

            let checklist_items = [];
            $('.add-to-checklist-item-div').each(function () {

                let checklist_item_id = $(this).data('checklist-item-id');
                let document_ids = [];
                $(this).find('.add-to-checklist-document-div').each(function () {
                    document_ids.push($(this).data('document-id'));
                });
                if (document_ids.length > 0) {
                    checklist_items.push({
                        checklist_item_id: checklist_item_id,
                        document_ids: document_ids
                    });
                }

            });

            let formData = new FormData();

            let Agent_ID = $('#Agent_ID').val();
            let Listing_ID = $('#Listing_ID').val();
            checklist_items = JSON.stringify(checklist_items);
            formData.append('checklist_id', checklist_id);
            formData.append('Agent_ID', Agent_ID);
            formData.append('Listing_ID', Listing_ID);
            formData.append('checklist_items', checklist_items);
            axios.post('/agents/doc_management/transactions/listings/save_assign_documents_to_checklist', formData, axios_options)
                .then(function (response) {
                    toastr['success']('Documents Successfully Added')
                    load_tabs('documents');
                    load_tabs('checklist');
                })
                .catch(function (error) {
                    console.log(error);
                });

        }

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
            console.log(files);

            files = JSON.stringify(files);
            formData.append('files', files);

            let validate = validate_form(form);
            if (validate == 'yes') {
                axios.post('/agents/doc_management/transactions/listings/save_add_template_documents', formData, axios_options)
                    .then(function (response) {
                        toastr['success']('Documents Successfully Added')
                        load_tabs('documents');
                        load_tabs('checklist');
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
                    load_tabs('checklist');
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
                    load_tabs('checklist');
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
            formData.append('document_ids', document_ids);
            formData.append('Listing_ID', Listing_ID);
            axios.post('/agents/doc_management/transactions/listings/move_documents_to_trash', formData, axios_options)
                .then(function (response) {
                    //button.closest('.document-div').appendTo($('.folder-div').last().find('[id^=documents_folder_]'));
                    load_tabs('documents');
                    load_tabs('checklist');
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
                    load_tabs('checklist');
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



    });

}
