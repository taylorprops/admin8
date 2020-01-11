if (document.URL.match(/create\/add_fields/)) {

    $(document).ready(function () {


        // TODO run field_status() after any add or remove

        set_common_fields();
        setTimeout(function () {
            field_list();
            setTimeout(function() {
                check_fields();
            }, 500);
        }, 500);

        // Show active field
        $('.field-wrapper').click(function () {
            $('.field-wrapper').removeClass('active');
            $(this).addClass('active');
            $('#active_field').val($(this).data('type'));
        });

        // set field options on load
        if ($('.field-div').length > 0) {


            $('.field-div').each(function () {

                // get bounding box coordinates
                let rect = this.getBoundingClientRect();
                let container = $(this).closest('.field-container');

                let group_id = $(this).data('group-id');
                let field_type = $(this).data('type');

                // let h = $(this).css('height').replace(/px/, '');
                // let w = $(this).css('width').replace(/px/, '');
                // let h_perc = pix_2_perc_hw('height', h, container);
                // let w_perc = pix_2_perc_hw('width', w, container);
                // let x = $(this).css('left').replace(/px/, '');
                // let y = $(this).css('top').replace(/px/, '');
                // // convert from % to px
                // $(this).css('height', h + 'px');
                // $(this).css('width', w + 'px');
                // $(this).css('top', y + 'px');
                // $(this).css('left', x + 'px');




                $(this).find('.field-properties').data('field-type', field_type);

                // clear other field when changing name
                /* if (field_type != 'checkbox') {
                    clear_fields_on_change(group_id);
                } */
                // add grouped class
                if ($('.group_' + group_id).length > 1) {
                    $('.group_' + group_id).removeClass('standard').addClass('group');
                };
                // hide add item on all fields of group but last
                $('.group_' + group_id).find('.add-item-container').hide();
                $('.group_' + group_id).find('.add-item-container').last().show();

                set_field_options(field_type, $(this), $(this).data('field-id'), rect, container);
                set_hwxy($(this), $(this).data('group-id'), field_type);


            });

            $('.focused').hide();

            field_status();
            setTimeout(function () {
                $('.add-input').off('click').on('click', add_input);
                $('.delete-input').off('click').on('click', delete_input);
            }, 1500);


        }

        $('.delete-input').off('click').on('click', delete_input);



        // on page double click add field
        $('#file_viewer').on('dblclick', '.file-view-page-container.active .file-image', function (e) { // changed from just .file-view-page-container.active - adding .file-image prevents new field being created when double clicking in edit properties div

            // get container so easier to find elements
            let container = $(e.target.parentNode);

            let field_type = $('#active_field').val();

            // only if a field has been selected
            if (field_type != '') {

                // get bounding box coordinates
                let rect = e.target.getBoundingClientRect();
                // get target coordinates
                let x = parseInt(Math.round(e.clientX - rect.left));
                let y = parseInt(Math.round(e.clientY - rect.top));

                let x_perc = pix_2_perc_xy('x', x, container);
                let y_perc = pix_2_perc_xy('y', y, container);

                let ele_h_perc = 1.3;
                if (field_type == 'radio' || field_type == 'checkbox') {
                    ele_h_perc = 1.1;
                }
                // remove element height from top position
                y_perc = y_perc - ele_h_perc;

                // set w and h for new field
                let h_perc, w_perc;
                if (field_type == 'radio' || field_type == 'checkbox') {
                    h_perc = 1.1;
                    w_perc = 1.45;
                } else {
                    h_perc = $('#field_' + field_type + '_heightp').val();
                    w_perc = $('#field_' + field_type + '_widthp').val();
                }
                h_perc = parseFloat(h_perc);
                w_perc = parseFloat(w_perc);
                // let h = perc_2_pix('height', h_perc, container);
                // let w = perc_2_pix('width', w_perc, container);

                // create unique id for field
                let id = Date.now();

                //create field and attach to container
                let field = field_html(h_perc, w_perc, x_perc, y_perc, id, id, $('#active_page').val(), field_type);

                // hide all handles and buttons
                $('.focused').hide();

                // append new field
                $(container).append(field);

                setTimeout(function () {
                    //$('select.mdb-select').not('.initialized').materialSelect();
                    $('.add-input').off('click').on('click', add_input);
                    $('.delete-input').click(delete_input);
                    field_list();
                    /* setTimeout(function() {
                        check_fields();
                    }, 500); */
                }, 500);

                // clear other field when changing name
                /* if ($('#field_' + id).data('type') != 'checkbox') {
                    clear_fields_on_change(id);
                } */

                set_hwxy($('#field_' + id), id, field_type);

                keep_in_view($('#field_' + id), w_perc, x_perc, y_perc, field_type);

                set_field_options(field_type, $('#field_' + id), id, rect, container);

                setTimeout(function () {

                    $('.field-div').removeClass('active');
                    $('#field_' + id).addClass('active');

                }, 100);

                field_status();

                setTimeout(function() {
                    form_elements();
                }, 1000);

            }
        });


        // on page click hide all focused els
        $(document).on('click', '.field-container', function (e) {

            if (!$(e.target).is('.field-div *')) {
                $('.modal').modal('hide');
                reset_field_properties();
            }

        });

        function reset_field_properties() {
            // reset name fields
            $('.form-div').each(function () {
                $(this).find('select, input').not('input.form-select-search-input, input.form-select-value-input').each(function () {
                    $(this).val($(this).data('default-value')).trigger('change');
                    if ($(this).hasClass('form-select')) {
                        select_refresh();
                    }
                });
            });
            //select_dropdown.refresh();
            $('.field-div').removeClass('active');
            $('.focused, .mini-slider-div').hide();

        }

        /* function clear_fields_on_change(id) {
            $('#name_select_' + id).change(function () {
                if ($(this).val() != '') {
                    $('#name_input_' + id).val('').trigger('change');
                } else {
                    $(this);
                }
            });
            $('#name_input_' + id).change(function () {
                if ($(this).val() != '') {
                    $('#name_select_' + id).val('').trigger('change');
                }
            });
        } */

        function set_field_options(field_type, ele, id, rect, container) {

            let handles = {
                'e': '.ui-resizable-e', 'w': '.ui-resizable-w'
            };
            let aspect_ratio = '';
            if (field_type == 'checkbox' || field_type == 'radio') {
                aspect_ratio = '4 / 4';
            }

            ele.click(function (e) {

                if (e.target === this) {
                    e.stopPropagation();
                }
                $('.focused').hide();
                ele.find('.focused').show();
                $('.field-div').removeClass('active');
                ele.addClass('active');
                let group_id = ele.data('group-id');
                set_hwxy(ele, group_id, field_type);

            });

            if (field_type != 'checkbox' && field_type != 'radio') {
                ele.resizable({
                    containment: container,
                    handles: handles,
                    aspectRatio: aspect_ratio,
                    stop: function (e, ui) {
                        let resized_ele = $(e.target);
                        setTimeout(function() {
                            set_hwxy(resized_ele, '', field_type);
                        }, 500);

                    }
                });
            }

            ele.draggable({
                containment: container,
                handle: '.field-handle',
                cursor: 'grab',
                stop: function (e, ui) {
                    let dragged_ele = $(e.target);
                    let dragged_x = ui.position.left;
                    let dragged_y = ui.position.top;
                    let dragged_x_perc = pix_2_perc_xy('x', dragged_x, container);
                    let dragged_y_perc = pix_2_perc_xy('y', dragged_y,container);
                    let dragged_h = dragged_ele.height();
                    let dragged_w = dragged_ele.width();
                    let dragged_h_perc = pix_2_perc_hw('height', dragged_h, container);
                    let dragged_w_perc = pix_2_perc_hw('width', dragged_w, container);
                    let dragged_group_id = dragged_ele.data('group-id');

                    // align checkboxes if grouped
                    if (field_type == 'checkbox') {
                        if ($('.group_' + dragged_group_id).length > 1) {
                            $('.group_' + dragged_group_id).each(function () {
                                $(this).css({ left: dragged_x });
                                set_hwxy($(this), '', field_type);
                            });
                        }
                    }

                    setTimeout(function() {
                        set_hwxy(dragged_ele, '', field_type);
                        keep_in_view(dragged_ele, dragged_w_perc, dragged_x_perc, dragged_y_perc, field_type);
                    }, 500);

                }
            });


            // hide all handles and buttons when another container is selected
            $('.field-select-container').click(function (e) {
                $('.focused').hide();
                $('.field-div').removeClass('active');
            });

            // remove field
            $('.remove-field').off('click').on('click', function () {
                let remove_group_id = $('.field-div.active').data('group-id');
                $('.field-div.active').remove();

                let group = $('.group_' + remove_group_id);
                if (group.length > 1) {
                    // hide add line option for all but last
                    group.find('.add-item-container').hide();
                    group.find('.add-item-container').last().show();
                } else {
                    group.find('.add-item-container').show();
                }
                // see if other divs in a group and if just one remove group class and group icon from field-status-group-div
                if (group.length == 1) {
                    group.removeClass('group').addClass('standard').find('.field-status-group-div').html('');
                }

                field_status();
                setTimeout(function () {
                    field_list();
                    setTimeout(function() {
                        check_fields();
                    }, 500);
                }, 500);
            });

            // add additional fields to group
            if (field_type != 'date') {
                // add items to group
                ele.find('.field-add-item').off('click').on('click', function () {

                    // assign group id for original field
                    let group_id = $(this).data('group-id');

                    let common_name = $('.group_' + group_id).data('commonname');
                    let custom_name = $('.group_' + group_id).data('customname');
                    $('.group_' + group_id).removeClass('standard').addClass('group');


                    // get h,w, x, y etc. incase it was changed during a drag or resize
                    let field_div = ele.closest('.field-div');
                    let h = field_div.height();
                    let w = field_div.width();
                    let h_perc = pix_2_perc_hw('height', h, container);
                    let w_perc = pix_2_perc_hw('width', w, container);
                    let x = field_div.position().left;
                    let y = field_div.position().top;
                    let x_perc = pix_2_perc_xy('x', x, container);
                    let y_perc = pix_2_perc_xy('y', y,container);

                    // drop the new line height of ele below the original
                    let spacing = 1.2;
                    if (field_type == 'radio' || field_type == 'checkbox') {
                        spacing = 1.3;
                    }
                    y_perc = y_perc + (h_perc * spacing);

                    $('.field-div').removeClass('active');
                    // create new id for new field in group
                    id = Date.now();
                    let field = field_html(h_perc, w_perc, x_perc, y_perc, id, group_id, $('#active_page').val(), field_type);
                    // append new field
                    ele.closest('.field-container').append(field);

                    setTimeout(function () {
                        //$('select.mdb-select').not('.initialized').materialSelect();
                        field_list();
                        setTimeout(function() {
                            check_fields();
                        }, 500);
                    }, 500);

                    let new_ele = $('#field_' + id);


                    setTimeout(function () {
                        $('.focused').fadeOut();
                        $('.field-div').removeClass('active');
                        new_ele.addClass('active').find('.focused').fadeIn();
                        let new_h = new_ele.height();
                        let new_w = new_ele.width();
                        let new_h_perc = pix_2_perc_hw('height', new_h, container);
                        let new_w_perc = pix_2_perc_hw('width', new_w, container);
                        let new_x = new_ele.position().left;
                        let new_y = new_ele.position().top;
                        let new_x_perc = pix_2_perc_xy('x', new_x, container);
                        let new_y_perc = pix_2_perc_xy('y', new_y,container);

                        if ((parseFloat(new_y_perc) + parseFloat(new_h_perc)) > 99) {
                            new_ele.css({ border: '3px dotted #900' });
                            setTimeout(function () {
                                new_ele.remove();
                            }, 1000);
                            return false;
                        }

                        set_hwxy(new_ele, '', field_type);

                        // assign group id to new field
                        new_ele.data('group-id', group_id).removeClass('standard').addClass('group').addClass('group_' + group_id);

                        // move add line option to last line
                        $('.group_' + group_id).find('.add-item-container').hide();
                        $('.group_' + group_id).find('.add-item-container').last().show();

                        // clear other field when changing name
                        /* if (new_ele.data('type') != 'checkbox') {
                            clear_fields_on_change(group_id);
                        } */

                        setTimeout(function () {
                            // set values for field name
                            if (field_type != 'checkbox') {
                                new_ele.find('.form-select.field-data-name').val(common_name).data('default-value', common_name).trigger('change');
                                new_ele.find('.form-input.field-data-name').val(custom_name).data('default-value', custom_name).trigger('change');
                            }
                            keep_in_view(new_ele, new_w_perc, new_x_perc, new_y_perc, field_type);
                            set_field_options(field_type, new_ele, id, rect, container);
                            field_status();
                            select_refresh();
                        }, 500);

                        let inputs_div = ele.find('.field-data-inputs-container');
                        add_inputs_to_group(group_id, inputs_div);

                        //select_menu();


                    }, 100);



                });
            }

            // mini-slider
            $('.mini-slider-button').off('click').on('click', function () {
                let minislider = $(this).closest('.field-options-holder').siblings('.mini-slider-div');
                minislider.show();
                $('.mini-slider-option').off('click').on('click', function () {
                    let dir = $(this).data('direction');
                    let operator = (dir == 'up') ? '-' : '+';
                    let field_div = $(this).closest('.field-div');
                    let field_type = field_div.data('type');
                    field_div.css({ top: operator + '=.05%' });
                    // set new h,w,x,y after moving up and down
                    set_hwxy(field_div, '', field_type);
                });
                $(document).mouseup(function (e) {
                    if (!minislider.is(e.target) && minislider.has(e.target).length === 0) {
                        minislider.hide();
                    }
                });
            });

            // add properties
            $('.field-properties').unbind('click').bind('click', function () {
                let field_type = $(this).data('field-type');
                let edit_div = $(this).closest('.field-options-holder').siblings('.edit-properties-div');

                //store inputs html in input to be restored on cancel
                $('#inputs_html').val(edit_div.find('.field-data-inputs-container').html());
                edit_div.modal('show');
                $('.modal-backdrop').appendTo(edit_div.closest('.field-div'));

                // prevent new field being created
                $('.edit-properties-div *').dblclick(function (event) {
                    event.stopPropagation();
                });

                let select = edit_div.find('.form-select.field-data-name');
                let inputs_container = edit_div.find('.field-data-inputs-container');

                // remove common name when custom name is type in
                edit_div.find('.form-input.field-data-name').change(function () {
                    if ($(this).val() != '') {
                        select.val('');
                        select_refresh();
                        inputs_container.html('').next('.add-input').trigger('click');
                    }
                });

                // auto populate helper text for address fields - they will always be the same as the address type
                if (field_type == 'address') {

                    edit_div.find('.field-data-address-type').change(function () {
                        let helper_text = $(this).find('option:selected').text();
                        let helper_text_input = edit_div.find('.field-data-helper-text');
                        helper_text_input.val(helper_text).trigger('change');
                    });

                } else if (field_type == 'name' || field_type == 'date' || field_type == 'textline') {


                    //auto populate helper text from field name
                    select.unbind('change').bind('change', function () {

                        if (select.val() != '') {
                            edit_div.find('.form-input.field-data-name').val('').trigger('change');
                        }

                        let helper_text = select.find('option:selected').text();

                        let helper_text_input = edit_div.find('.field-data-helper-text');
                        helper_text_input.val(helper_text).trigger('change');


                        inputs_container.html('');

                        // auto populate input name and helper text for Buyer and Seller Names
                        if (field_type == 'name') {

                            if (select.val() != '') {

                                let name_type = select.val().replace(/\sName/, '');

                                if (select.val() == 'Seller or Landlord Name' || select.val() == 'Buyer Name') {
                                    // force click to add inputs
                                    inputs_container.next('.add-input').trigger('click').trigger('click');
                                    inputs_container.children('.row:eq(0)').find('.field-data-input').val(name_type + ' One Name').trigger('change');
                                    inputs_container.children('.row:eq(0)').find('.field-data-input-helper-text').val('Enter the Full Name of the First ' + name_type).trigger('change');
                                    inputs_container.children('.row:eq(1)').find('.field-data-input').val(name_type + ' Two Name').trigger('change');
                                    inputs_container.children('.row:eq(1)').find('.field-data-input-helper-text').val('Enter the Full Name of the Second  ' + name_type).trigger('change');

                                } else {

                                    inputs_container.next('.add-input').trigger('click');
                                    inputs_container.children('.row:eq(0)').find('.field-data-input').val(name_type + ' Name').trigger('change');
                                    inputs_container.children('.row:eq(0)').find('.field-data-input-helper-text').val('Enter the Full Name of the ' + name_type).trigger('change');

                                }

                                // $('.field-data-input, .field-data-input-helper-text');

                            } else {

                                inputs_container.next('.add-input').trigger('click')

                            }
                        }
                        select_refresh();

                    });

                } else if (field_type == 'number') {

                    edit_div.find('.form-select.field-data-name').change(function () {
                        let helper_text = $(this).find('option:selected').text();
                        let helper_text_input = edit_div.find('.field-data-helper-text');
                        helper_text_input.val(helper_text).data('default-value', helper_text).trigger('change');
                    });

                }

                $('.field-save-properties').off('click').on('click', function () {

                    let group_id = $(this).data('group-id');
                    let type = $(this).data('type');
                    let form = $(this).parent('div.modal-footer').prev('div.modal-body').find('.form-div');
                    let common_name = form.find('.form-select.field-data-name').val();
                    let custom_name = form.find('.form-input.field-data-name').val();

                    // set default values and group values for properties

                    // set inputs html for reset on close, blur etc.
                    $('#inputs_html').val(edit_div.find('.field-data-inputs-container').html());
                    let inputs_div = form.find('.field-data-inputs-container');
                    // address and name groups have different helper text for each field
                    if (type == 'address' || type == 'name' || type == 'checkbox') {
                        $('.group_' + group_id).each(function () {
                            let input = $(this).find('input.field-data-helper-text');
                            input./* attr('value', input.val()). */data('default-value', input.val()).trigger('change');
                        });
                        add_inputs_to_group(group_id, inputs_div);
                    } else {
                        // all other groups have the same helper text for each field
                        let helper_input = form.find('input.field-data-helper-text');
                        let helper_text = helper_input.val();
                        $('.group_' + group_id).each(function () {
                            $(this).find('input.field-data-helper-text').val(helper_text).data('default-value', helper_text).trigger('change');
                        });
                    }
                    // add value and default value to all added inputs
                    inputs_div.find('.field-data-inputs-div').find('.field-data-input, .field-data-input-helper-text').each(function () {
                        $(this).data('default-value', $(this).val())./* attr('value', $(this).val()). */trigger('change');
                    });

                    let address_select = form.find('select.field-data-address-type');
                    address_select.each(function () {
                        let address_type = $(this).val();
                        $(this).data('default-value', address_type);
                    });

                    let textline_select = form.find('select.field-data-textline-type');
                    textline_select.each(function () {
                        let textline_type = $(this).val();
                        $(this).data('default-value', textline_type);
                    });

                    let number_select = form.find('select.field-data-number-type');
                    let number_type = number_select.val();
                    number_select.data('default-value', number_type);
                    // update other number types to written if this is numeric. There will be one numeric and possibly multiple written
                    if (number_type == 'numeric') {
                        if ($('.group_' + group_id).length > 1) {
                            $('.group_' + group_id).find('select.field-data-number-type').not(number_select).val('written').data('default-value', 'written').trigger('change');
                            number_select.data('default-value', number_type);
                        }
                    }

                    if (type == 'checkbox') {

                        let checkbox = form.find('input.field-data-checkbox-value');
                        let checkbox_value = checkbox.val();
                        checkbox.data('default-value', checkbox_value);
                        form.find('.form-input.field-data-name').data('default-value', custom_name);

                    } else if (type == 'radio') {

                        let radio_input = form.find('input.field-data-radio-value');
                        let radio_value = radio_input.val();
                        radio_input.data('default-value', radio_value);
                        $('.group_' + group_id).each(function () {
                            $(this).data('commonname', common_name).data('customname', custom_name);
                            $(this).find('.form-div').each(function () {
                                $(this).find('.form-input.field-data-name').val(custom_name).data('default-value', custom_name).trigger('change');
                            });
                        });

                    } else {

                        $('.group_' + group_id).each(function () {
                            $(this).data('commonname', common_name).data('customname', custom_name);
                            $(this).find('.form-div').each(function () {
                                $(this).find('.form-select.field-data-name').val(common_name).data('default-value', common_name).trigger('change');
                                $(this).find('.form-input.field-data-name').val(custom_name).data('default-value', custom_name).trigger('change');
                                select_refresh();
                            });
                        });

                    }

                    edit_div.modal('hide');
                    field_status();
                    setTimeout(function () {
                        field_list();
                        setTimeout(function() {
                            check_fields();
                        }, 500);
                    }, 500);


                    $('.group_' + group_id).removeClass('field-error');

                });

                // clear fields on cancel
                $('.modal').on('hide.bs.modal', function () {

                    reset_field_properties();

                    let form = $(this).find('.form-div');
                    // FIXME: this still not working
                    //form.find('.field-data-inputs-container').html($('#inputs_html').val());
                    $('.add-input').off('click').on('click', add_input);
                    $('.delete-input').off('click').on('click', delete_input);

                    edit_div.hide();

                });

            });

            form_elements();

        }

        function add_inputs_to_group(group_id, inputs_div) {

            let c = 0;
            if ($('.group_' + group_id).length > 1) {
                $('.group_' + group_id).each(function () {
                    // inputs to all fields in a group
                    $(this).find('.field-data-inputs-container').html(inputs_div.html());
                    // rename all ids so they are unique and add data-default-value
                    $(this).find('.field-data-inputs-div').find('input, select').each(function () {
                        c += 1;
                        $(this).attr('id', 'input_' + group_id + '_' + c);
                        $(this).data('default-value', $(this).val());
                        // TODO need to activate form-element
                    });
                });
            }
            $('.add-input').off('click').on('click', add_input);
            form_elements();
        }

        function delete_input() {
            $(this).closest('.field-data-inputs-div').fadeOut('slow');
        }

        function add_input() {
            let append_to = $(this).prev('.field-data-inputs-container');
            let input_id = Date.now();
            let field_id = $(this).data('field-id');

            let new_input = ' \
            <div class="row field-data-inputs-div"> \
                <div class="col-12"> \
                    <div class="border border-primary p-2 mb-4"> \
                        <div class="clearfix"> \
                            <a href="javascript: void(0)" class="delete-input float-right mr-2 mt-1"><i class="fas fa-times-square text-danger fa-lg"></i></a> \
                        </div> \
                        <div class="mt-1"> \
                            <input type="text" class="custom-form-element form-input field-data-input" id="input_name_'+ field_id + '_' + input_id + '" data-id="'+input_id+'" data-label="Input Name"> \
                        </div> \
                        <div class="mt-3 mb-2"> \
                            <input type="text" class="custom-form-element form-input field-data-input-helper-text" id="input_helper_text_'+ field_id + '_' + input_id + '" data-id="'+input_id+'" data-label="Input Helper Text"> \
                        </div> \
                    </div> \
                </div> \
            </div> \
            ';

            $(new_input).appendTo(append_to);
            $('.delete-input').click(delete_input);
            form_elements();
        }

        function field_properties(type, group_id, field_id) {

            let input_id = Date.now();
            let custom_name_heading;
            if (type == 'radio') {
                custom_name_heading = 'Radio Group Name';
            } else {
                custom_name_heading = 'Custom Name';
            }

            let properties = ' \
                <div class="modal-header bg-primary"> \
                    <h4 class="modal-title" id="edit_properties_modal_title">Field Properties</h4> \
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> \
                        <span aria-hidden="true" class="text-white">&times;</span> \
                    </button> \
                </div> \
                <div class="modal-body"> \
                    <h5 class="text-primary mt-4 mb-2">Type -'+ type.toUpperCase() + '</h5> \
                    <div class="form-div"> \
                        <div class="container"> \
                            <div class="row"> \
                                <div class="col-12"> \
                                    <h5 class="text-primary mt-4 mb-2">Field Name</h5> \
                                </div> \
            ';
            if (type != 'checkbox' && type != 'radio') {
                properties = properties + ' \
                                <div class="col-12"> \
                                    <select class="custom-form-element form-select field-data-name" id="name_select_'+ field_id + '" data-field-type="common" data-label="Select Common Name (Shared)"> \
                                        <option value="">&nbsp;</option> \
                                        ' + $('#' + type + '_select_options').val() + ' \
                                    </select> \
                                </div> \
                                <div class="text-primary text-center w-100">OR</div> \
                ';
            }
            properties = properties + ' \
                                <div class="col-12"> \
                                    <input type="text" class="custom-form-element form-input field-data-name" id="name_input_'+ field_id + '" data-field-type="custom" data-label="' + custom_name_heading + '"> \
                                </div> \
                            </div> \
            ';
            if (type == 'number') {
                properties = properties + ' \
                            <div class="row"> \
                                <div class="col-12"> \
                                    <h5 class="text-primary mt-4 mb-2">Number Type</h5> \
                                </div> \
                                <div class="col-12"> \
                                    <select class="custom-form-element form-select field-data-number-type" id="number_select_'+ field_id + '" data-field-type="number-type" data-label="Number Type"> \
                                        <option value="">&nbsp;</option> \
                                        <option value="numeric">Numeric - 3,000</option> \
                                        <option value="written">Written - Three Thousand</option> \
                                    </select> \
                                </div> \
                            </div> \
                ';
            } else if (type == 'textline') {
                properties = properties + ' \
                            <div class="row"> \
                                <div class="col-12"> \
                                    <h5 class="text-primary mt-4 mb-2">Text Type <small>(Optional - Use to format the value)</small></h5> \
                                </div> \
                                <div class="col-12"> \
                                    <select class="custom-form-element form-select field-data-textline-type" id="textline_select_'+ field_id + '" data-field-type="textline-type" data-label="Text Type"> \
                                        <option value="">&nbsp;</option> \
                                        <option value="number numbers-only">Number</option> \
                                        <option value="phone numbers-only">Phone Number</option> \
                                    </select> \
                                </div> \
                            </div> \
                ';
            } else if (type == 'address') {
                properties = properties + ' \
                            <div class="row"> \
                                <div class="col-12"> \
                                    <h5 class="text-primary mt-4 mb-2">Address Type</h5> \
                                </div> \
                                <div class="col-12"> \
                                    <select class="custom-form-element form-select field-data-address-type" id="address_select_'+ field_id + '" data-field-type="address-type" data-label="Address Type"> \
                                        <option value="">&nbsp;</option> \
                                        <option value="full">Full Address</option> \
                                        <option value="street">Street Address</option> \
                                        <option value="city">City</option> \
                                        <option value="state">State</option> \
                                        <option value="zip">Zip Code</option> \
                                        <option value="county">County</option> \
                                    </select> \
                                </div> \
                            </div> \
                ';
            } else if (type == 'radio') {
                properties = properties + ' \
                            <div class="row> \
                                <div class="col-12"> \
                                    <h5 class="text-primary mt-4 mb-2">Radio Input Value</h5> \
                                </div> \
                                <div class="col-12"> \
                                    <input type="text" class="custom-form-element form-input field-data-radio-value" id="field_value_input_'+ field_id + '" data-label="Field Value"> \
                                </div> \
                            </div> \
                ';
            } else if (type == 'checkbox') {
                properties = properties + ' \
                            <div class="row"> \
                                <div class="col-12"> \
                                    <h5 class="text-primary mt-4 mb-2">Checkbox Value</h5> \
                                </div> \
                                <div class="col-12"> \
                                    <input type="text" class="custom-form-element form-input field-data-checkbox-value" id="field_value_input_'+ field_id + '" data-label="Field Value"> \
                                </div> \
                            </div> \
                ';
            }
            properties = properties + ' \
                            <div class="row"> \
                                <div class="col-12"> \
                                    <h5 class="text-primary mt-4 mb-2">Helper Text</h5> \
                                </div> \
                                <div class="col-12"> \
                                    <input type="text" class="custom-form-element form-input field-data-helper-text" id="helper_text_input_'+ field_id + '" data-label="Helper Text"> \
                                </div> \
                            </div> \
            ';
            if (type == 'address' || type == 'name') {
                properties = properties + ' \
                            <div class="row"> \
                                <div class="col-12"> \
                                    <h5 class="text-primary mt-4 mb-2">Inputs</h5> \
                                </div> \
                                <div class="col-12"> \
                                    <div class="container field-data-inputs-container"> \
                ';
                if (type == 'name') {
                    properties = properties + ' \
                                        <div class="row field-data-inputs-div"> \
                                            <div class="col-12"> \
                                                <div class="border border-primary p-2 mb-4"> \
                                                    <div class="clearfix"> \
                                                        <a href="javascript: void(0)" class="delete-input float-right mr-2 mt-1"><i class="fas fa-times-square text-danger fa-lg"></i></a> \
                                                    </div> \
                                                    <div class="mt-1"> \
                                                        <input type="text" class="custom-form-element form-input field-data-input" id="input_name_'+ field_id + '_'+input_id+'" data-default-value="" data-id="'+input_id+'" data-label="Input Name"> \
                                                    </div> \
                                                    <div class="mt-3 mb-2"> \
                                                        <input type="text" class="custom-form-element form-input field-data-input-helper-text" id="input_helper_text_'+ field_id + '_'+input_id+'" data-id="'+input_id+'" data-label="Input Helper Text"> \
                                                    </div> \
                                                </div> \
                                            </div> \
                                        </div> \
                    ';
                } else if (type == 'address') {
                    // adding all address field inputs
                    let input_list = JSON.stringify({
                        'street': {
                            'name': 'Street Address',
                            'helper': 'Enter The Street Address'
                        },
                        'city': {
                            'name': 'City',
                            'helper': 'Enter The City'
                        },
                        'county': {
                            'name': 'County',
                            'helper': 'Enter The County'
                        },
                        'state': {
                            'name': 'State',
                            'helper': 'Enter The State'
                        },
                        'zip': {
                            'name': 'Zip Code',
                            'helper': 'Enter The Zip Code'
                        }
                    });
                    let inputs = JSON.parse(input_list);
                    let c = 1;
                    for (let key in inputs) {
                        c += 1;
                        let input_name = inputs[key]['name'];
                        let input_helper_text = inputs[key]['helper'];
                        let inputs_ids = input_id + c;

                        properties = properties + ' \
                                        <div class="row field-data-inputs-div"> \
                                            <div class="col-12"> \
                                                <div class="border border-primary p-2 mb-4"> \
                                                    <div class="clearfix"> \
                                                        <a href="javascript: void(0)" class="delete-input float-right mr-2 mt-1"><i class="fas fa-times-square text-danger fa-lg"></i></a> \
                                                    </div> \
                                                    <div class="mt-4"> \
                                                        <input type="text" class="custom-form-element form-input field-data-input" id="input_name_'+ field_id + '_'+inputs_ids+'" value="'+input_name+'" data-default-value="'+input_name+'" data-id="'+inputs_ids+'" data-label="Input Name"> \
                                                    </div> \
                                                    <div class="mt-3 mb-2"> \
                                                        <input type="text" class="custom-form-element form-input field-data-input-helper-text" id="input_helper_text_'+ field_id + '_'+inputs_ids+'" value="'+input_helper_text+'" data-default-value="'+input_helper_text+'" data-id="'+inputs_ids+'" data-label="Input Helper Text"> \
                                                    </div> \
                                                </div> \
                                            </div> \
                                        </div> \
                        ';
                    }
                }
                properties = properties + ' \
                                    </div> \
                                    <a href="javascript: void(0);" class="text-green add-input" data-field-id="'+ field_id + '"><i class="fa fa-plus"></i> Add Input</a> \
                                </div> \
                            </div> \
                ';
            }
            properties = properties + ' \
                        </div> \
                    </div > \
                </div > \
            ';
            properties = properties + ' \
                <div class="modal-footer"> \
                    <a href="javascript: void(0);" class="btn btn-success btn-sm shadow field-save-properties" data-group-id="'+ group_id + '" data-type="' + type + '">Save</a> \
                    <a href="javascript:void(0);" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</a> \
                </div> \
            ';

            return properties
        }



        function field_html(h_perc, w_perc, x_perc, y_perc, id, group_id, page, type) {

            let properties_html = field_properties(type, group_id, id);

            let field_class = '';
            let field_html = '';
            let hide_add_option = '';
            let handles = ' \
            <div class="ui-resizable-handle ui-resizable-e focused"></div> \
            <div class="ui-resizable-handle ui-resizable-w focused"></div> \
            ';
            if (type == 'textline' || type == 'name' || type == 'address' || type == 'number') {
                field_class = 'textline-div standard';
                field_html = '<div class="textline-html"></div>';
            } else if (type == 'radio') {
                handles = '';
                field_class = type + '-div standard';
                field_html = '<div class="radio-html"></div>';
            } else if (type == 'checkbox') {
                handles = '';
                field_class = type + '-div standard';
                field_html = '<div class="checkbox-html"></div>';
            } else if (type == 'date') {
                field_class = 'textline-div standard';
                field_html = '<div class="textline-html"></div>';
                hide_add_option = 'hidden';
            }

            return ' \
            <div class="field-div '+ field_class + ' active group_' + group_id + '" style="position: absolute; top: ' + y_perc + '%; left: ' + x_perc + '%; height: ' + h_perc + '%; width: ' + w_perc + '%;" id="field_' + id + '" data-field-id="' + id + '" data-group-id="' + group_id + '" data-type="' + type + '" data-page="' + page + '"> \
                <div class="field-status-div d-flex justify-content-left"> \
                    <div class="field-status-name-div"></div> \
                    <div class="field-status-group-div float-right"></div> \
                </div> \
                <div class="field-options-holder focused"> \
                    <div class="btn-group" role="group" aria-label="Field Options"> \
                        <a type="button" class="btn btn-primary field-handle"><i class="fal fa-arrows fa-lg"></i></a> \
                        <a type="button" class="btn btn-primary mini-slider-button"><i class="fal fa-arrows-v fa-lg"></i></a> \
                        <a type="button" class="btn btn-primary field-add-item ' + hide_add_option + '" data-group-id="'+ group_id + '"><i class="fal fa-plus fa-lg"></i></a> \
                        <a type="button" class="btn btn-primary field-properties" data-group-id="'+ group_id + '" data-field-type="' + type +'"><i class="fal fa-info-circle fa-lg"></i></a> \
                        <a type="button" class="btn btn-primary remove-field"><i class="fal fa-times-circle fa-lg"></i></a> \
                    </div> \
                </div> \
                <div class="mini-slider-div"> \
                    <ul class="mini-slider list-group list-group-flush border border-primary p-0"> \
                        <li class="list-group-item text-center p-0"><a href="javascript:void(0);" class="mini-slider-option w-100 h-100 d-block p-2" data-direction="up"><i class="fal fa-arrow-up text-primary"></i></a></li> \
                        <li class="list-group-item text-center p-0"><a href="javascript:void(0);" class="mini-slider-option w-100 h-100 d-block p-2" data-direction="down"><i class="fal fa-arrow-down text-primary"></i></a></li> \
                    </ul> \
                </div> \
                <div class="modal fade edit-properties-div" id="edit_properties_modal" tabindex="-1" role="dialog" aria-labelledby="edit_properties_modal_title" aria-hidden="true"> \
                    <div class="modal-dialog modal-md modal-dialog-centered" role="document"> \
                        <div class="modal-content">'+ properties_html + ' \
                        </div> \
                    </div> \
                </div> \
                '+ handles + ' \
                '+ field_html + ' \
            </div> \
            ';
        }

        function field_status() {
            let group_ids = [];
            $('.field-div').each(function () {
                group_ids.push($(this).data('group-id'));
            });
            group_ids = group_ids.filter(filter_array);

            for (let i = 0; i < group_ids.length; i++) {

                // find out if grouped and add icon
                let grouped = false;
                if ($('.field-div[data-group-id="' + group_ids[i] + '"]').length > 1) {
                    grouped = true;
                }
                if (grouped == true) {
                    $('.field-div[data-group-id="' + group_ids[i] + '"]').each(function () {
                        // remove all group icons
                        $('.field-div[data-group-id="' + group_ids[i] + '"]').find('.field-status-group-div').html('');
                        // add group icon to last of all
                        $('.field-div[data-group-id="' + group_ids[i] + '"]').last().find('.field-status-group-div').html('<i class="fal fa-layer-group"></i>');

                    });
                }


                // add field names
                $('.field-div[data-group-id="' + group_ids[i] + '"]').each(function () {
                    let field_name = '';
                    // all but checkbox get names added only to the last
                    if ($(this).data('type') != 'checkbox') {

                        $(this).find('.field-status-name-div').html('');
                        $(this).find('.field-data-name').each(function () {
                            if ($(this).val() != '') {
                                field_name = $(this).val();
                                // add field name to last of each group
                                $('.field-div[data-group-id="' + group_ids[i] + '"]').find('.field-status-name-div').last().html(field_name);
                            }
                        });
                    } else {
                        // checkboxes get name for each since not really a group
                        field_name = $(this).find('.field-data-name').val();
                        $(this).find('.field-status-name-div').html(field_name);
                    }
                });


            }

        }

        function pix_2_perc_hw(type, px, container) {
            if (type == 'width') {
                return (100 * parseFloat(px / parseFloat(container.width())));
            } else {
                return (100 * parseFloat(px / parseFloat(container.height())));
            }
        }

        function pix_2_perc_xy(type, px, container) {
            if (type == 'x') {
                return (100 * parseFloat(px / parseFloat(container.width())));
            } else {
                return (100 * parseFloat(px / parseFloat(container.height())));
            }
        }

        function perc_2_pix(type, perc, container) {
            if (type == 'width') {
                return parseFloat((perc / 100) * parseFloat(container.width()));
            } else {
                return parseFloat((perc / 100) * parseFloat(container.height()));
            }
        }

        function set_hwxy(ele, group_id, type) {

            let container = ele.closest('.field-container');

            let h = ele.height();
            let w = ele.width();
            let h_perc = pix_2_perc_hw('height', h, container);
            let w_perc = pix_2_perc_hw('width', w, container);

            let x = ele.position().left;
            let y = ele.position().top;
            let x_perc = pix_2_perc_xy('x', x, container);
            let y_perc = pix_2_perc_xy('y', y,container);

            if (h_perc) {
                ele.data('hp', h_perc);
                ele.data('wp', w_perc);
                $('#field_' + type + '_heightp').val(h_perc);
                $('#field_' + type + '_widthp').val(w_perc);
            }
            if (x) {
                ele.data('x', x);
                ele.data('y', y);
                ele.data('xp', x_perc);
                ele.data('yp', y_perc);
                $('#field_' + type + '_x').val(x);
                $('#field_' + type + '_y').val(y);
                $('#field_' + type + '_xp').val(x_perc);
                $('#field_' + type + '_yp').val(y_perc);
            }
            if (group_id) {
                $('#field_' + type + '_group_id').val(group_id);
            }
            ele.data('page', ele.data('page'));

        }

        function keep_in_view(ele, w_perc, x_perc, y_perc, type) {
            // adjust fields if placed out of bounds
            let dist = '';
            let cw = 100;
            let cd_adjusted = '';
            if (type == 'textline' || type == 'name' || type == 'address' || type == 'date' || type == 'number') {
                dist = 3;
                cd_adjusted = cw;
            } else if (type == 'radio' || type == 'checkbox') {
                dist = 3;
                cd_adjusted = cw - 4;
            }

            if (x_perc < dist) {
                ele.animate({ left: dist + '%' });
            }
            if ((x_perc + w_perc) > cd_adjusted) {
                let pos = cw - w_perc - dist;
                ele.animate({ left: pos + '%' });
            }

            if (y_perc < 2) {
                ele.animate({ top: '2%' });
            }

            setTimeout(function () {
                let group_id = ele.data('group-id');
                set_hwxy(ele, group_id, type);
            }, 1500);

        }

        function set_common_fields() {
            $.ajax({
                type: 'get',
                url: '/doc_management/common_fields',
                dataType: "json",
                success: function (data) {
                    $.each(data, function (k) {
                        let type = k;
                        let select_options = '';
                        $.each(this, function (k, v) {
                            select_options = select_options + '<option value="' + v + '">' + v + '</option>';
                        });
                        $('#' + type + '_select_options').val(select_options).trigger('change');
                    });

                }
            });
        }

        function field_list() {
            $('.field-list-container').html('');
            $('.field-list-container').append('<div class="h3 text-white bg-primary-dark p-2"><i class="fal fa-align-left mr-3"></i> Fields</div>');
            $('.file-view-page-container').each(function () {
                let page_number = $(this).data('id');
                $('.field-list-container').append('<div class="font-weight-bold text-white bg-primary p-1 pl-2 mb-2">Page ' + page_number + '</div>');
                // get unique group ids
                let group_ids = [];
                $(this).find('.field-div').each(function () {
                    group_ids.push($(this).data('group-id'));
                });
                group_ids = group_ids.filter(filter_array);
                // get all field names and add to field list
                $.each(group_ids, function (index, group_id) {
                    let name = $('.group_' + group_id).data('customname');
                    if ($('.group_' + group_id).data('commonname') != undefined && $('.group_' + group_id).data('commonname') != '') {
                        name = $('.group_' + group_id).data('commonname');
                    }
                    if (name == undefined || name == '') {
                        name = '<span class="text-danger">Not Named</span>';
                    }
                    $('.field-list-container').append('<div class="mb-1 border-bottom border-primary"><a href="javascript: void(0)" class="field-list-link" data-group-id="' + group_id + '">' + name + '</a></div>');
                });
                $('.field-list-link').off('click').on('click', function () {
                    let group_id = $(this).data('group-id');
                    let ele = $('.field-div[data-group-id="' + group_id + '"]').first();
                    $('.focused').hide();
                    ele.find('.focused').show();
                    $('.field-div').removeClass('active');
                    ele.addClass('active');

                    let container = ele.parent();
                    // let h = ele.height();
                    // let w = ele.width();
                    // let h_perc = pix_2_perc_hw('height', h, container);
                    // let w_perc = pix_2_perc_hw('width', w, container);
                    // let y = ele.position().top;
                    // let x = ele.position().left;
                    let type = ele.data('type');
                    set_hwxy(ele, group_id, type);

                    let $container = $('#file_viewer'),
                        $scrollTo = $('#field_' + group_id).first();
                    $container.animate({
                        scrollTop: ($scrollTo.offset().top - $container.offset().top + $container.scrollTop()) - 200
                    });

                });

            });

        }

        function check_fields() {
            // remove all error divs
            $('.field-error-div').remove();
            $('.field-error').removeClass('field-error');
            $('.field-list-link').removeClass('text-danger');
            let errors = 'no';

            $('.field-div').each(function () {
                // add error divs
                $('<div class="field-error-div"></div>').insertBefore($(this).find('.form-div'));

                let type = $(this).data('type');
                let errors_found = 'no';

                let field_name = null;
                $(this).find('.field-data-name').each(function () {
                    if ($(this).val() != '') {
                        field_name = $(this).val();
                    }
                });
                if (field_name == null) {
                    $(this).addClass('field-error').find('.field-error-div').append('<div class="field-error-item"><i class="fal fa-exclamation-triangle mr-2"></i> You must name the field</div>');
                    errors = 'yes';
                    errors_found = 'yes';
                }

                if ($(this).find('input.field-data-helper-text').val() == '') {
                    $(this).addClass('field-error').find('.field-error-div').append('<div class="field-error-item"><i class="fal fa-exclamation-triangle mr-2"></i> You must enter the helper text</div>');
                    errors = 'yes';
                    errors_found = 'yes';
                }

                if (type == 'number') {
                    if ($(this).find('select.field-data-number-type').val() == '') {
                        $(this).addClass('field-error').find('.field-error-div').append('<div class="field-error-item"><i class="fal fa-exclamation-triangle mr-2"></i> You must enter the number type</div>');
                        errors = 'yes';
                        errors_found = 'yes';
                    }
                }

                if (type == 'address') {
                    if ($(this).find('select.field-data-address-type').val() == '') {
                        $(this).addClass('field-error').find('.field-error-div').append('<div class="field-error-item"><i class="fal fa-exclamation-triangle mr-2"></i> You must enter the address type</div>');
                        errors = 'yes';
                        errors_found = 'yes';
                    }
                }

                if (type == 'radio') {
                    if ($(this).find('.field-data-radio-value').val() == '') {
                        $(this).addClass('field-error').find('.field-error-div').append('<div class="field-error-item"><i class="fal fa-exclamation-triangle mr-2"></i> You must enter the value</div>');
                        errors = 'yes';
                        errors_found = 'yes';
                    }
                }

                if (errors_found == 'yes') {
                    $('.field-list-link[data-group-id="' +$(this).data('group-id')+'"]').addClass('text-danger');
                }

            });

            if (errors == 'yes') {
                return false;
            }
            return true;

        }

        /////////////////// Save data ///////////////////////
        $('#save_add_fields').click(save_add_fields);

        function save_add_fields() {

            let check = check_fields();

            if(check == true) {

                let data = [];

                if ($('.field-div').length > 0) {

                    $('.field-div').each(function () {

                        let field_data = {};
                        let type = $(this).data('type');

                        field_data['file_id'] = $('#file_id').val();
                        field_data['field_id'] = $(this).data('field-id');
                        field_data['group_id'] = $(this).data('group-id');
                        field_data['page'] = $(this).data('page');
                        field_data['field_type'] = type;
                        field_data['left'] = $(this).data('x');
                        field_data['top'] = $(this).data('y');
                        field_data['height'] = $(this).data('h');
                        field_data['width'] = $(this).data('w');
                        field_data['left_perc'] = $(this).data('xp');
                        field_data['top_perc'] = $(this).data('yp');
                        field_data['height_perc'] = $(this).data('hp');
                        field_data['width_perc'] = $(this).data('wp');

                        // inputs are arrays and have their own table
                        field_data['field_data_input'] = [];
                        field_data['field_data_input_helper_text'] = [];
                        field_data['field_data_input_id'] = [];

                        if ($(this).find('.field-data-inputs-div').length > 0) {
                            $(this).find('.field-data-inputs-div').each(function () {
                                if ($(this).find('.field-data-input').val() != '') {
                                    field_data['field_data_input'].push($(this).find('.field-data-input').val());
                                    field_data['field_data_input_helper_text'].push($(this).find('.field-data-input-helper-text').val());
                                    field_data['field_data_input_id'].push($(this).find('.field-data-input').data('id'));
                                }
                            });
                        }

                        $(this).find('.field-data-name').each(function () {
                            if ($(this).val() != '') {
                                field_data['field_name'] = $(this).val();
                                field_data['field_name_type'] = $(this).data('field-type');
                            }
                        });


                        field_data['helper_text'] = $(this).find('input.field-data-helper-text').val();

                        field_data['number_type'] = $(this).find('select.field-data-number-type').val();

                        field_data['address_type'] = $(this).find('select.field-data-address-type').val();

                        field_data['textline_type'] = $(this).find('select.field-data-textline-type').val();

                        field_data['radio_value'] = $(this).find('.field-data-radio-value').val();

                        field_data['checkbox_value'] = $(this).find('.field-data-checkbox-value').val();

                        data.push(field_data);

                    });

                } else {

                    let field_data = {};
                    field_data['file_id'] = $('#file_id').val();

                    data.push(field_data);

                }


                $.ajax({
                    type: 'POST',
                    url: '/doc_management/save_add_fields',
                    data: { data: JSON.stringify(data) },
                    success: function (response) {
                        $('#modal_success').modal().find('.modal-body').html('Fields Successfully Saved');
                    }
                });
            } else {
                $('#modal_danger').modal().find('.modal-body').html('All Fields Must Be Completed');
            }


        }

        // highlight active thumb when clicked and scroll into view
        $('.file-view-thumb-container').click(function () {
            $('.file-view-thumb-container').removeClass('active');
            $(this).addClass('active');
            let id = $(this).data('id');
            $('#active_page').val(id);
            document.getElementById('page_' + id).scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'nearest' });
        });

        // change highlighted thumb on scroll when doc is over half way in view
        $('#file_viewer').scroll(function () {

            // Stop the loop once the first is found
            let cont = 'yes';

            $('.file-view-page-container').each(function () {
                if (cont == 'yes') {
                    let id, center, start, end;
                    id = $(this).data('id');
                    // see if scrolled past half way
                    center = $(window).height() / 2;
                    start = $(this).offset().top;
                    end = start + $(this).height();
                    if (start < center && end > center) {
                        // set opacity to 1 for active and .2 for not active
                        $('.file-view-page-container').removeClass('active');
                        $(this).addClass('active');
                        $('#active_page').val(id);
                        // add border to thumb and scroll into view
                        $('.file-view-thumb-container').removeClass('active');
                        $('#thumb_' + id).addClass('active');
                        document.getElementById('thumb_' + id).scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'nearest' });
                        cont = 'no';
                    }
                }
            });

        });


    });

}
