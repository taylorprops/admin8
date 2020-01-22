if (document.URL.match(/checklists/)) {

    $(document).ready(function () {

        let data_count = $('.checklist-data').length;
        $('.checklist-data').each(function (index) {
            let location_id = $(this).data('location-id');
            get_checklists(location_id, 'listing');
            // load form elements and options after all divs are loaded
            if (index === data_count - 1) {

                setTimeout(function() {
                    init();
                }, 500);
            }
        });

    });

    // functions to run on load and after adding elements
    function init() {
        form_elements();
        // show add/edit modal to edit checklist details
        $('.add-checklist-button, .edit-checklist-button').off('click').on('click', show_add_edit_checklist);
        // delete checklist
        $('.delete-checklist-button').off('click').on('click', confirm_delete_checklist);
        // add items to checklist
        $('.add-items-button').off('click').on('click', checklist_items);

        // toggle listing and contract checklists
        $('.checklist-type-option').unbind('change').bind('change', function () {
            checklist_type($(this));
        });

        sortable_checklists();


    }

    function checklist_type(ele = null) {
        $('.checklist-items-container').hide();
        if (ele) {
            if (ele.val() == 'listing') {
                $('.checklist-items-listing').show();
            } else {
                $('.checklist-items-contract').show();
            }
            $('.checklist-type-option').val(ele.val());
            select_refresh();

            let type = ele.val().charAt(0).toUpperCase() + ele.val().slice(1);
            $('.checklist-type').text(type);
        } else {

        }
    }

    function sortable_checklists() {
        // checklists are sortable
        $('.sortable-checklist').sortable({
            placeholder: 'bg-orange-sortable',
            handle: '.list-item-handle',
            stop: function (event, ui) {
                // get details and update new order
                let els = $(ui.item).parent('.sortable').children('.checklist-items-container');
                let checklists = {
                    checklist: []
                }

                els.each(function () {
                    let el, checklist_id, checklist_index;
                    el = $(this);
                    checklist_id = el.data('checklist-id');
                    checklist_index = el.index();
                    checklists.checklist.push(
                        {
                            'checklist_id': checklist_id,
                            'checklist_index': checklist_index
                        }
                    );
                });
                let formData = new FormData();
                checklists = JSON.stringify(checklists);
                formData.append('data', checklists);
                axios.post('/doc_management/reorder_checklists', formData, axios_options)
                    .then(function (response) {
                        toastr['success']('Checklist Reordered');
                    })
                    .catch(function (error) {

                    });
            }

        });
        $('.sortable-checklist').disableSelection();
    }

    function sortable_checklist_items() {
        // checklist items are sortable and saved after sort
        $('.sortable-checklist-items').sortable({
            handle: '.checklist-item-handle',
            placeholder: 'bg-orange-sortable-big',
            receive: function (event, ui) {
                forms_status();
            },
            revert: true
        });
        $('.sortable-checklist-items').disableSelection();
    }


    // search forms in add items modal
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


    // add/edit checklist items
    function checklist_items() {

        let checklist_id = $(this).data('checklist-id');
        // get checklist details html to add to modal
        axios.get('/doc_management/get_checklist_items', {
            params: {
                checklist_id: checklist_id
            },
            // added html headers since returning html
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {
            // show modal
            $('#checklist_items_modal').modal();
            // add html form response
            $('#checklist_items_div').html(response.data);

            // add title to modal
            $('#checklist_items_modal_title').html('Checklist Items | <span class="text-yellow-light">' + $('#checklist_header_val').val() + '</span>');
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


            // delete checklist items
            $('.delete-checklist-item-button').click(delete_checklist_item);

            $('.add-to-checklist-button').not('disabled').click(add_to_checklist);

            $('#save_checklist_items_button').off('click').on('click', save_checklist_items);

            sortable_checklist_items();

            forms_status();

            form_elements();

        })
        .catch(function (error) {
            console.log(error);
        });
    }

    function save_checklist_items() {
        let form = $('#checklist_items_form');

        let validate = validate_form(form);

        if (validate == 'yes') {

            //if ($('.checklist-item').length > 0) {

                let checklist_items = [];
                let checklist_id = $('#checklist_id').val();

                $('.checklist-item').each(function (index) {

                    let checklist_item = {};

                    let checklist_form_id = $(this).data('form-id');
                    let checklist_item_required = $(this).find('.checklist-item-required').val();
                    let checklist_item_group_id = $(this).find('.checklist-item-group-id').val();
                    let checklist_item_order = index;

                    checklist_item['checklist_id'] = checklist_id;
                    checklist_item['checklist_form_id'] = checklist_form_id;
                    checklist_item['checklist_item_required'] = checklist_item_required;
                    checklist_item['checklist_item_group_id'] = checklist_item_group_id;
                    checklist_item['checklist_item_order'] = checklist_item_order;

                    checklist_items.push(checklist_item);

                });

                let formData = new FormData();
                formData.append('checklist_id', checklist_id);
                formData.append('checklist_items', JSON.stringify(checklist_items));
                axios.post('/doc_management/add_checklist_items', formData, axios_options)
                .then(function (response) {
                    $('#checklist_items_modal').modal('hide');
                    toastr['success']('Checklist Items Saved');
                    get_checklists($('#add_item_checklist_location_id').val(), $('#add_item_checklist_type').val());
                })
                .catch(function (error) {
                    console.log(error);
                });

            //}
        }

    }


    function add_to_checklist() {
        let form_id = $(this).data('form-id');
        let text_orig = $(this).data('text');
        let form_loc = $(this).data('form-loc');
        let text = text_orig;
        if (text_orig.length > 100) {
            text = text_orig.slice(0, 100) + '...';
        }

        // options are saved in a hidden div on load since they are dynamic
        let checklist_groups_options = $('#checklist_groups_options').html();
        // this is the helper dragged and inserted in checklist items container
        let checklist_item = ' \
            <li class="list-group-item checklist-item w-100" data-form-id="' + form_id + '"> \
                <div class="row"> \
                    <div class="col-8"> \
                        <div class="d-flex justify-content-start"> \
                            <div class="mt-4"> \
                                <i class="fas fa-sort fa-lg mx-3 text-primary checklist-item-handle ui-sortable-handle"></i> \
                            </div> \
                            <div class="h5 text-primary mt-4" title="' + text_orig + '"><a href="' + form_loc + '" target="_blank"> ' + text + '</a></div> \
                        </div> \
                    </div> \
                    <div class="col-4"> \
                        <div class="row"> \
                            <div class="col"> \
                                <select class="custom-form-element form-select form-select-no-cancel form-select-no-search checklist-item-required required" data-label="Required"> \
                                    <option value=""></option> \
                                    <option value="yes">Yes</option> \
                                    <option value="no">No</option> \
                                </select> \
                            </div> \
                            <div class="col-6"> \
                                <select class="custom-form-element form-select form-select-no-cancel form-select-no-search checklist-item-group-id required" data-label="Form Group"> \
                                    <option value=""></option> \
                                    ' + checklist_groups_options + ' \
                                </select> \
                            </div> \
                            <div class="col"> \
                                <a class="btn btn-danger delete-checklist-item-button ml-3 mt-1"><i class="fa fa-trash"></i></a> \
                            </div> \
                        </div> \
                    </div> \
                </div> \
            </li> \
        ';
        $('.sortable-checklist-items').append(checklist_item);
        $('.delete-checklist-item-button').click(delete_checklist_item);

        // if the form is already included in another checklist get the details and add it to this one
        axios.get('/doc_management/get_checklist_item_details', {
            params: {
                form_id: form_id
            },
        })
        .then(function (response) {
            if (response.data) {
                let row = $('.sortable-checklist-items').find('.list-group-item').last();
                row.find('.checklist-item-group-id').val(response.data.checklist_item_group_id);
                row.find('.checklist-item-required').val(response.data.checklist_item_required);
            }

            select_refresh();
            form_elements();
            forms_status();
            sortable_checklist_items();
        })
        .catch(function (error) {
            console.log(error);
        });



    }

    function delete_checklist_item() {
        let button = $(this);
        $('#confirm_remove_file_modal').modal();
        $('#confirm_remove_file').click(function () {
            button.closest('li').remove();
            forms_status();
            $('#confirm_remove_file_modal').modal('hide');
        });
    }

    function forms_status() {

        $('.form-name').removeClass('form-selected');
        $('.add-to-checklist-button').removeClass('disabled');

        let form_ids = [];
        $('.checklist-item').each(function () {
            form_ids.push($(this).data('form-id'));
        });

        $.map(form_ids, function (value, index) {
            $('.form-name[data-form-id="' + value + '"]').addClass('form-selected').find('.add-to-checklist-button').addClass('disabled');
        });
    }

    function show_add_edit_checklist() {

        $('#checklist_modal').modal();

        let checklist_type = $(this).closest('.list-div').find('.checklist-type-option').val();

        let location_id = $(this).data('location-id');
        let property_type = $(this).data('property-type');
        let property_sub_type = $(this).data('property-sub-type');
        let state = $(this).data('state');
        let form_type = $(this).data('form-type');
        let sale_rent = $(this).data('sale-rent');
        let represent = $(this).data('represent');

        // assign form input values
        $('#checklist_location_id').val(location_id).trigger('change');
        $('#checklist_type').val(checklist_type).trigger('change');
        $('#checklist_property_type_id').val(property_type).trigger('change');
        $('#checklist_property_sub_type_id').val(property_sub_type).trigger('change');
        $('#checklist_sale_rent').val(sale_rent).trigger('change');
        $('#checklist_represent').val(represent).trigger('change');

        if (checklist_type== 'listing') {
            $('#checklist_represent').val('seller').trigger('change');
        }


        // assign hidden input values
        $('#checklist_id').val($(this).data('checklist-id'));
        $('#checklist_state').val(state);
        $('#form_type').val(form_type);

        select_refresh();

        show_hide_options();

        $('#checklist_type, #checklist_property_type_id, #checklist_sale_rent, #checklist_property_sub_type_id, #checklist_represent').unbind('change').bind('change', show_hide_options);


        $('#save_checklist_button').off('click').on('click', save_checklist);

    }

    function show_hide_options() {

        let select_checklist_type = $('#checklist_type');
        let select_checklist_property_type = $('#checklist_property_type_id');
        let select_checklist_sale_rent = $('#checklist_sale_rent');
        let select_checklist_property_sub_type_id = $('#checklist_property_sub_type_id');
        let select_checklist_represent = $('#checklist_represent');

        // ######### these are based on resource values and could possibly be changed by user

        if (select_checklist_type.val() == 'listing') {
            select_checklist_represent.val('seller').prop('disabled', true);
        } else {
            select_checklist_represent.prop('disabled', false);
        }

        // if residential show property_sub_types | but not if rental
        if (select_checklist_property_type.find('option:selected').text() == 'Residential') {
            if (select_checklist_sale_rent.val() == 'rental') {
                select_checklist_property_sub_type_id.val('').addClass('hidden').removeClass('required');
            } else {
                select_checklist_property_sub_type_id.removeClass('hidden').addClass('required');
            }
        } else {
            select_checklist_property_sub_type_id.val('').addClass('hidden').removeClass('required');
        }
        // if fsbo you must be representing the buyer, must be a contract and for sale
        if (select_checklist_property_sub_type_id.find('option:selected').text() == 'For Sale By Owner') {
            select_checklist_represent.val('buyer');
            select_checklist_sale_rent.val('sale');
        }

        if (select_checklist_represent.val() == 'buyer') {
            select_checklist_type.val('contract');
        }

        select_refresh();

    }

    function save_checklist() {
        let form = $('#checklist_form');
        let validate = validate_form(form);

        if (validate == 'yes') {
            let formData = new FormData();
            formData.append('checklist_id', $('#checklist_id').val());
            formData.append('checklist_location_id', $('#checklist_location_id').val());
            formData.append('checklist_represent', $('#checklist_represent').val());
            formData.append('checklist_type', $('#checklist_type').val());
            formData.append('checklist_sale_rent', $('#checklist_sale_rent').val());
            formData.append('checklist_property_type_id', $('#checklist_property_type_id').val());
            formData.append('checklist_property_sub_type_id', $('#checklist_property_sub_type_id').val());
            formData.append('checklist_state', $('#checklist_state').val());

            let form_type = $('#form_type').val();

            let url = '/doc_management/edit_checklist';
            if (form_type == 'add') {
                url = '/doc_management/add_checklist';
            }

            axios.post(url, formData, axios_options)
                .then(function (response) {
                    get_checklists($('#checklist_location_id').val(), $('#checklist_type').val());
                    setTimeout(function() {
                        init();
                    }, 500);
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    }

    function get_checklists(checklist_location_id, checklist_type) {
        let options = {
            params: {
                checklist_location_id: checklist_location_id
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        }

        axios.get('/doc_management/get_checklists', options)
            .then(function (response) {
                $('#list_div_' + checklist_location_id + '_files').html($(response.data));
                // $('#list_div_' + checklist_location_id + '_file_count').text($('#files_count').val());
                $('.checklist-items-container').hide();
                $('.checklist-items-' + checklist_type).show();
                $('#checklist_modal').modal('hide');
                // make sure correct checklist type is shown
                $('.checklist-type-option').val(checklist_type);
                select_refresh();
                init();
            })
            .catch(function (error) {

            });
    }

    function confirm_delete_checklist() {
        let ele = $(this);
        $('#confirm_delete_checklist_modal').modal();
        $('#confirm_delete_checklist').off('click').on('click', function () {
            delete_checklist(ele);
        });

    }

    function delete_checklist(ele) {
        let checklist_id = ele.data('checklist-id');
        let checklist_location_id = ele.data('checklist-location-id');
        let checklist_type = ele.data('checklist-type');
        let formData = new FormData();
        formData.append('checklist_id', checklist_id);
        axios.post('/doc_management/delete_checklist', formData, axios_options)
        .then(function (response) {
            get_checklists(checklist_location_id, checklist_type);
            setTimeout(function() {
                init();
            }, 500);
            $('#confirm_delete_checklist_modal').modal('hide');
        })
        .catch(function (error) {
            console.log(error);
        });
    }

}

