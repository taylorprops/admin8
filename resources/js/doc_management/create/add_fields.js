if (document.URL.match(/create\/add_fields/)) {

    /* TODO:
    set all default values
    restore default values when not saved
    on add field - get common/custom name and other details from group

    delete docs_create_field_types table
    remove FieldTypes model
    */
    $(function() {

        // Show active field
        $(document).on('click', '.field-wrapper', function () {
            $('.field-wrapper').removeClass('active');
            $(this).addClass('active');
            $('#active_field').val($(this).data('type'));
        });

        // open field when clicked
        $(document).on('click', '.field-div', function() {
            hide_active_field();
            $(this).closest('.field-div-container').addClass('show');
        });

        // remove field
        $(document).on('click', '.remove-field', function() {
            $(this).closest('.field-div-container').remove();
        });

        // hide all active fields when page clicked
        $('.field-select-container div, .file-image').on('click', function() {
            hide_active_field();
        });

        //$('#save_add_fields').off('click').on('click', save_add_fields);

        $('.delete-page-button').on('click', delete_page);

        // highlight active thumb when clicked and scroll into view
        $('.file-view-thumb-container').on('click', function (e) {
            $('.file-view-thumb-container').removeClass('active');
            $(this).addClass('active');
            let id = $(this).data('id');
            $('#active_page').val(id);
            window.location = '#page_' + id;
            //document.getElementById('page_' + id).scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'nearest' });

        });

        // change highlighted thumb on scroll when doc is over half way in view
        $('#file_viewer').on('scroll', function () {

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

        // add new field on dblclick
        $('#file_viewer').off('dblclick').on('dblclick', '.file-view-page-container.active .file-image', function (e) {

            let field_type = $('#active_field').val();
            let active_page = $('#active_page').val();

            // only if a field has been selected
            if (field_type != '') {

                hide_active_field();

                let container = $(e.target.parentNode);

                let coords = set_and_get_field_coordinates(e, null, field_type);
                let x_perc = coords.x;
                let y_perc = coords.y;
                let h_perc = coords.h;
                let w_perc = coords.w;

                // create unique id for field
                let field_id = Date.now();

                //create field and attach to container
                let field = field_html(h_perc, w_perc, x_perc, y_perc, field_id, field_id, active_page, field_type);

                // append new field
                container.append(field);

                let ele = $('.field-div-container.show');

                // run this again in case it was placed out of bounds
                set_and_get_field_coordinates(null, ele, field_type);

                set_field_options(ele);



            }
        });

        function set_field_options(ele) {

            let field_type = ele.find('.field-div').data('type');
            let container = ele.closest('.field-container');

            if(field_type != 'checkbox') {
                get_edit_properties_html($('.field-div-container.show .field-div'));
            }

            let handles = {
                'e': '.ui-resizable-e', 'w': '.ui-resizable-w'
            };
            let aspect_ratio = '';
            // not resizable
            if (field_type == 'checkbox' || field_type == 'radio') {
                aspect_ratio = '4 / 4';
            }

            // make field resizable
            if (field_type != 'checkbox' && field_type != 'radio') {
                ele.resizable({
                    containment: container,
                    handles: handles,
                    aspectRatio: aspect_ratio,
                    stop: function (e, ui) {
                        let resized_ele = $(e.target);
                        set_and_get_field_coordinates(null, resized_ele, field_type);
                    }
                });
            }

            // make field draggable
            ele.draggable({
                containment: container,
                handle: '.field-handle',
                cursor: 'grab',
                stop: function (e, ui) {
                    let dragged_ele = $(e.target);
                    set_and_get_field_coordinates(null, dragged_ele, field_type);
                }
            });

            // add additional fields to group
            if (field_type != 'date') {
                // add items to group
                ele.find('.add-field-button').off('click').on('click', function () {
                    add_item(ele, field_type);
                });
            }

            // adjust y_perc with slider
            $('.mini-slider-option').on('click', function () {
                mini_slider($(this));
            });

        }

        function field_html(h_perc, w_perc, x_perc, y_perc, field_id, group_id, page, field_type) {

            //console.log('running field_html');

            let handles = ' \
            <div class="ui-resizable-handle ui-resizable-e focused field-div-options"></div> \
            <div class="ui-resizable-handle ui-resizable-w focused field-div-options"></div> \
            ';
            let field_class = '';
            let field_data = '';
            let hide_add_option = '';

            let position = x_perc > 50 ? 'right' : '';
            let field_div_properties = ' \
            <div class="field-div-properties bg-white border shadow pt-0 pb-2 px-2  '+position+'"> \
                <div class="field-div-properties-html"></div> \
            </div> \
            ';

            if (field_type == 'textline' || field_type == 'number') {
                field_class = 'textline-div standard';
                field_data = '<div class="textline-html"></div>';
            } else if (field_type == 'radio') {
                handles = '';
                field_class = 'radio-div standard';
                field_data = '<div class="radio-html"></div>';
            } else if (field_type == 'checkbox') {
                handles = '';
                field_class = 'checkbox-div standard';
                field_data = '<div class="checkbox-html"></div>';
                field_div_properties = '';
            } else if (field_type == 'date') {
                field_class = 'textline-div standard';
                field_data = '<div class="textline-html"></div>';
                hide_add_option = 'hidden';
            }

            let field_div_html = ' \
            <div class="field-div-container show" id="field_container_' + field_id + '" style="position: absolute; top: ' + y_perc + '%; left: ' + x_perc + '%; height: ' + h_perc + '%; width: ' + w_perc + '%;"> \
                <div class="field-div '+ field_class + ' group_' + group_id + '" id="field_' + field_id + '" data-field-id="' + field_id + '" data-group-id="' + group_id + '" data-type="' + field_type + '" data-page="' + page + '"> \
                    <div class="field-status-name-div"></div> \
                </div> \
                <div class="field-div-options"> \
                    <div class="d-flex justify-content-start field-div-controls '+position+'"> \
                        <a type="button" href="javascript: void(0)" class="btn btn-primary ml-0 mr-1 field-handle"><i class="fal fa-arrows fa-lg"></i></a> \
                        <a type="button" href="javascript: void(0)" class="btn btn-primary ml-0 mr-1 mini-slider-option" data-direction="up"><i class="fal fa-arrow-up fa-lg"></i></a> \
                        <a type="button" href="javascript: void(0)" class="btn btn-primary ml-0 mr-1 mini-slider-option" data-direction="down"><i class="fal fa-arrow-down fa-lg"></i></a> \
                        <a type="button" href="javascript: void(0)" class="btn btn-primary ml-0 mr-1 add-field-button '+hide_add_option+'"><i class="fal fa-plus fa-lg"></i></a> \
                    </div> \
                    '+field_div_properties+' \
                </div> \
                '+handles+' \
                '+field_data+' \
            </div> \
            ';

            return field_div_html;
        }

        function get_edit_properties_html(ele) {

            //console.log('running get_edit_properties_html');

            let field_id = ele.data('field-id');
            let group_id = ele.data('group-id');
            let field_type = ele.data('type');

            let common_name = $('.group_' + group_id).data('commonname');
            let custom_name = $('.group_' + group_id).data('customname');

            axios.get('/doc_management/get_edit_properties_html', {
                params: {
                    file_id: $('#file_id').val(),
                    field_id: field_id,
                    field_type: field_type,
                    group_id: group_id
                },
                headers: {
                    'Accept-Version': 1,
                    'Accept': 'text/html',
                    'Content-Type': 'text/html'
                }
            })
            .then(function (response) {

                let edit_properties_html = response.data;
                let field_container = $('#field_container_'+field_id);
                field_container.find('.field-div-properties-html').html(edit_properties_html);

                // dropdowns for common fields
                field_container.find('.dropdown').not('.dropdown-input').on('mouseenter', function() {
                    $(this).addClass('show').find('.dropdown-menu').first().addClass('show');
                })
                .on('mouseleave', function() {
                    $(this).removeClass('show').find('.dropdown-menu').first().removeClass('show');
                });
                field_container.find('.dropdown-input').on('click', function(e) {
                    e.stopImmediatePropagation();
                    e.preventDefault();
                    $(this).addClass('show').find('.dropdown-menu').first().addClass('show');
                }).on('mouseleave', function() {
                    $(this).removeClass('show').find('.dropdown-menu').first().removeClass('show');
                });

                $('.save-field-properties-button').on('click', function() {
                    save_edit_properties($(this));
                });

                // when the common field name is clicked add to input and add ID to hidden input
                $('.field-name').on('click', function() {

                    let field_name = $(this).text();
                    let field_id = $(this).data('field-id');
                    let field_sub_type = $(this).data('field-sub-type');

                    let parent = $(this).closest('.edit-properties-div');
                    // clear custom name input
                    parent.find('.custom-field-name').val('').removeClass('required');
                    // add field name
                    parent.find('.common-field-name-input, .common-field-name').val(field_name).addClass('required');
                    // add field id
                    parent.find('.common-field-id').val(field_id);
                    parent.find('.common-field-sub-type').val(field_sub_type);
                    // hide dropdown
                    setTimeout(function() {
                        $('.dropdown-input, .dropdown-menu').removeClass('show');
                    }, 100);

                });

                // clear common name if custom name entered
                field_container.find('.custom-field-name').on('change', function() {
                    if($(this).val() != '') {
                        $(this).addClass('required');
                        field_container.find('.common-field-name-input, .common-field-name').val('').removeClass('required');
                    }
                });

                // show list of custom names already added
                field_container.find('.field-data-name').on('keyup', show_custom_names);

                // clear button on dropdown to remove value from input
                $('.clear-common-field-name').on('click', function() {
                    $(this).closest('.edit-properties-div').find('.common-field-name-input, .common-field-name').val('');
                    setTimeout(function() {
                        $('.dropdown-input, .dropdown-menu').removeClass('show');
                    }, 200);
                });

                // close field options button
                $('.close-field-options').on('click', function() {
                    hide_active_field();
                });

                ele.find('.form-select.field-data-name').val(common_name).data('default-value', common_name);
                ele.find('.form-input.field-data-name').val(custom_name).data('default-value', custom_name);

                select_refresh();

            })
            .catch(function (error) {
                console.log(error);
            });
        }

        function save_edit_properties(button) {

            //console.log('running save_edit_properties');

            // TODO: need to validate form


            let field_div_container = button.closest('.field-div-container');
            let field_div = field_div_container.find('.field-div');
            let group_id = button.data('group-id');
            let field_id = button.data('field-id');
            let field_type = button.data('field-type');

            let common_name = field_div_container.find('.common-field-name').val();
            let custom_name = field_div_container.find('.custom-field-name').val();
            let common_field_id = field_div_container.find('.common-field-id').val();
            let common_field_sub_type = field_div_container.find('.common-field-sub-type').val();


            if (field_type != 'checkbox') {

                // set default values of field
                field_div_container.find('input').not('[type=radio]').each(function () {
                    $(this).data('default-value', $(this).val());
                });

                // set values and default values of any other fields in group.
                $('.group_' + group_id).each(function () {

                    $(this).data('commonname', common_name).data('customname', custom_name);

                    let group_div_container = $(this).closest('.field-div-container');
                    // radio will always be a custom name
                    if (field_type != 'radio') {
                        // add field name to display
                        group_div_container.find('.field-status-name-div').text(custom_name+common_name);
                        group_div_container.find('.common-field-name-input, .common-field-name').val(common_name).data('default-value', common_name);
                        group_div_container.find('.common-field-id').val(common_field_id).data('default-value', common_field_id);
                        group_div_container.find('.common-field-sub-type').val(common_field_sub_type).data('default-value', common_field_sub_type);
                    }
                    group_div_container.find('.custom-field-name').val(custom_name).data('default-value', custom_name);

                });

                if(field_type == 'number') {
                    // set default values for number inputs
                    let number_type_radio = field_div_container.find('.number-type');
                    number_type_radio.data('default-value', '');
                    number_type_radio.filter(':checked').data('default-value', 'checked');
                    let number_type = number_type_radio.filter(':checked').val();

                    // update other number types to written if this is numeric. There will be one numeric and possibly multiple written
                    if (number_type == 'numeric') {
                        $('.group_' + group_id).each(function() {
                            if($(this).prop('id') != 'field_'+field_id) {
                                // uncheck all and clear default value
                                $(this).closest('.field-div-container').find('.number-type').prop('checked', false).data('default-value', '');
                                // set written field as checked and update default value
                                $(this).closest('.field-div-container').find('[value=written]').prop('checked', true).data('default-value', 'checked');
                            }
                        });
                    }

                }

            }

            field_div_container.find('.alert').removeClass('hide').addClass('show');
            field_div_container.find('input').on('change', function() {
                field_div_container.find('.alert').removeClass('show').addClass('hide');
            });




        }

        function add_item(ele, field_type) {

            //console.log('running add_item');

            hide_active_field();

            // assign group id for original field
            let group_id = ele.find('.field-div').data('group-id');

            // get original field input values
            let common_name, common_field_id, common_field_sub_type, custom_name, number_type;

            if (field_type != 'radio') {
                common_name = ele.find('.common-field-name').val();
                common_field_id = ele.find('.common-field-id').val();
                common_field_sub_type = ele.find('.common-field-sub-type').val();
            }
            custom_name = ele.find('.custom-field-name').val();

            if(field_type == 'number') {
                number_type = ele.find('.number-type:checked').val();
            }


            let coords = set_and_get_field_coordinates(null, ele, field_type);
            let x_perc = coords.x;
            let y_perc = coords.y;
            let h_perc = coords.h;
            let w_perc = coords.w;
            // add spacing
            y_perc = y_perc + (h_perc * 1.5);

            if ((parseFloat(y_perc) + parseFloat(h_perc)) > 99) {
                toastr['error']('Field must be on the page');
                return false;
            }

            // create new id for new field in group
            field_id = Date.now();
            let field = field_html(h_perc, w_perc, x_perc, y_perc, field_id, group_id, $('#active_page').val(), field_type);
            // append new field
            ele.closest('.field-container').append(field);

            let new_ele = $('.field-div-container.show');

            set_and_get_field_coordinates(null, new_ele, field_type);
            set_field_options(new_ele);

            setTimeout(function() {
                // assign group field_id to new field
                new_ele.find('.field-div').data('group-id', group_id).addClass('group_' + group_id);
                $('.group_' + group_id).removeClass('standard').addClass('group');

                // add field values and default data values from other fields in group
                if (field_type != 'radio') {
                    // add field name to display
                    new_ele.find('.field-status-name-div').text(custom_name+common_name);
                    new_ele.find('.common-field-name-input, .common-field-name').val(common_name).data('default-value', common_name);
                    new_ele.find('.common-field-id').val(common_field_id).data('default-value', common_field_id);
                    new_ele.find('.common-field-sub-type').val(common_field_sub_type).data('default-value', common_field_sub_type);
                }
                new_ele.find('.custom-field-name').val(custom_name).data('default-value', custom_name);

                // if a number and previous field is numeric this has to be written
                if(field_type == 'number') {
                    if(number_type == 'numeric') {
                        new_ele.find('.number-type[value=written]').prop('checked', true).data('default-value', 'checked');
                    }
                }
            }, 200);

        }

        function mini_slider(ele) {

            let dir = ele.data('direction');
            let operator = (dir == 'up') ? '-' : '+';
            let field_div_container = ele.closest('.field-div-container');
            let field_div = ele.closest('.field-div');
            let field_type = field_div.data('type');
            field_div_container.css({ top: operator + '=.05%' });
            // set new h,w,x,y after moving up and down
            set_and_get_field_coordinates(null, field_div_container, field_type);

        }

        function set_and_get_field_coordinates(e, ele, field_type) {

            let container, x, y;

            // if from dblclick to add field
            if(e) {

                // get container
                container = $(e.target.parentNode);
                ele = $(e.target);
                // get bounding box coordinates
                let target_boundaries = e.target.getBoundingClientRect();

                // get target coordinates
                // subtract bounding box coordinates from target coordinates to get top and left positions
                // coordinates are relative to bounding box coordinates
                x = parseInt(Math.round(e.clientX - target_boundaries.left));
                y = parseInt(Math.round(e.clientY - target_boundaries.top));

            // coordinates of existing field
            } else {

                container = ele.parent();
                x = ele.position().left;
                y = ele.position().top;

            }

            // convert to percent
            let x_perc = pix_2_perc_xy('x', x, container);
            let y_perc = pix_2_perc_xy('y', y, container);

            //set heights
            let ele_h_perc = 1.3;
            if (field_type == 'radio' || field_type == 'checkbox') {
                ele_h_perc = 1.1;
            }
            if(e) {
                // remove element height from top position
                y_perc = y_perc - ele_h_perc;
            }

            // set w and h for new field
            let h_perc, w_perc;
            if (field_type == 'radio' || field_type == 'checkbox') {
                h_perc = 1.1;
                w_perc = 1.45;
            } else {
                h_perc = 1.3;
                w_perc = 15;
            }
            h_perc = parseFloat(h_perc);
            w_perc = parseFloat(w_perc);

            // field data percents
            let field_div = ele.find('.field-div');
            field_div.data('hp', h_perc);
            field_div.data('wp', w_perc);
            field_div.data('xp', x_perc);
            field_div.data('yp', y_perc);


            // keep in view
            if (x_perc < 0) {
                ele.animate({ left: 0 + '%' }).find('field-div').data('wp', '0');
            }
            if ((x_perc + w_perc) > 100) {
                let pos = 100 - w_perc;
                ele.animate({ left: pos + '%' }).find('field-div').data('wp', pos);
            }

            if (y_perc < 0) {
                ele.animate({ top: '0%' }).find('field-div').data('yp', '0');
            }

            setTimeout(function() {
                ele.find('.dropdown, .field-div-properties, .field-div-controls').removeClass('right');
                if(x_perc > 50) {
                    ele.find('.dropdown, .field-div-properties, .field-div-controls').addClass('right');
                }
            }, 300);

            return {
                h: h_perc,
                w: w_perc,
                x: x_perc,
                y: y_perc
            }

        }

        function hide_active_field() {

            // reset all fields to default values
            let field_div_container = $('.field-div-container.show');

            field_div_container.find('input[type="text"]').each(function() {
                $(this).val($(this).data('default-value'));
            });
            field_div_container.find('input[type="radio"]').each(function() {
                if($(this).data('default-value') == 'checked') {
                    $(this).prop('checked', true);
                }
            });

            field_div_container.removeClass('show');
        }

        function pix_2_perc_xy(type, px, container) {
            if (type == 'x') {
                return (100 * parseFloat(px / parseFloat(container.width())));
            } else {
                return (100 * parseFloat(px / parseFloat(container.height())));
            }
        }

        function show_custom_names() {
            let input = $(this);
            let field_div = input.closest('.field-div-container');

            if(input.val() != '') {

                axios.get('/doc_management/get_custom_names', {
                    params: {
                        val: input.val()
                    }
                })
                .then(function (response) {

                    field_div.find('.dropdown-results-div').html('');
                    response.data.custom_names.forEach(function (result) {
                        field_div.find('.dropdown-results-div').append('<a href="javascript: void(0)" class="list-group-item list-group-item-action field-name-result">'+result['field_name_display']+'</a>');
                    });
                    field_div.find('.custom-name-results').show();

                    $('.field-name-result').on('click', function() {
                        let edit_div = $(this).closest('.field-div-container').find('.edit-properties-div');
                        edit_div.find('.form-input.field-data-name').val($(this).text());
                        edit_div.find('.custom-name-results').hide();
                    });

                })
                .catch(function (error) {
                    console.log(error);
                });

            } else {
                field_div.find('.custom-name-results').hide();

            }
        }

        function delete_page() {

            //console.log('running delete_page');

            let page = $(this).data('page-number');
            let file_id = $(this).data('file-id');

            $('#page_div_'+page).next('.file-view-page-info').fadeOut('slow').remove();
            $('#page_'+page+', #page_div_'+page+', #thumb_'+page).fadeOut('slow').remove();

            let formData = new FormData();
            formData.append('page', page);
            formData.append('file_id', file_id);
            axios.post('/doc_management/delete_page', formData, axios_options)
            .then(function (response) {
                toastr['success']('Page Successfully Removed')
            })
            .catch(function (error) {
                console.log(error);
            });
        }

/*

        // set field options on load
        if ($('.field-div').length > 0) {

            $('.field-div').each(function () {

                let group_id = $(this).data('group-id');
                let field_type = $(this).data('type');

                $(this).find('.field-properties').data('field-type', field_type);

                // add grouped class
                if ($('.group_' + group_id).length > 1) {
                    $('.group_' + group_id).removeClass('standard').addClass('group');
                };
                // hide add item on all fields of group but last
                $('.group_' + group_id).find('.add-item-container').hide();
                $('.group_' + group_id).find('.add-item-container').last().show();

                set_field_options(field_type, $(this));
                set_hwxy($(this), $(this).data('group-id'), field_type);

            });

            $('.focused').hide();


            setTimeout(function () {

                global_loading_off();
            }, 10);


        }





        function check_fields() {

            //console.log('running check_fields');

            // remove all error divs
            $('.field-error-div').remove();
            $('.field-error').removeClass('field-error');
            $('.field-list-link').removeClass('text-danger');
            let errors = 'no';

            $('.field-div').each(function () {

                let field_div = $(this);
                let type = field_div.data('type');

                if(type != 'checkbox') {

                    // add error divs
                    $('<div class="list-group field-error-div"></div>').insertAfter(field_div.find('.form-div'));

                    let errors_found = 'no';

                    let field_name = null;
                    field_div.find('.field-data-name').each(function () {
                        if ($(this).val() != '') {
                            field_name = $(this).val();
                        }
                    });
                    if (field_name == null) {
                        field_div.addClass('field-error');
                        field_div.find('.field-error-div').append('<div class="field-error-item list-group-item list-group-item-danger"><i class="fal fa-exclamation-triangle mr-2"></i> You must name the field</div>');
                        errors = 'yes';
                        errors_found = 'yes';
                    }

                    if (type == 'number') {
                        if (field_div.find('select.field-data-number-type').val() == '') {
                            field_div.addClass('field-error');
                            field_div.find('.field-error-div').append('<div class="field-error-item list-group-item list-group-item-danger"><i class="fal fa-exclamation-triangle mr-2"></i> You must enter the number type</div>');
                            errors = 'yes';
                            errors_found = 'yes';
                        }
                    }

                    if (type == 'address') {
                        if (field_div.find('select.field-data-address-type').val() == '') {
                            field_div.addClass('field-error');
                            field_div.find('.field-error-div').append('<div class="field-error-item list-group-item list-group-item-danger"><i class="fal fa-exclamation-triangle mr-2"></i> You must enter the address type</div>');
                            errors = 'yes';
                            errors_found = 'yes';
                        }
                    }

                    if (errors_found == 'yes') {
                        $('.field-list-link[data-group-id="' +field_div.data('group-id')+'"]').addClass('text-danger');
                    } else {
                        field_div.find('.field-error-div').remove();
                    }

                }

            });



            if (errors == 'yes') {
                return false;
            }
            return true;

        }


        function save_add_fields() {

            //console.log('running save_add_fields');

            let check = check_fields();

            if(check == true) {

                $('#save_add_fields').html('<span class="spinner-border spinner-border-sm mr-2"></span> Saving');

                let data = [];

                if ($('.field-div').length > 0) {

                    $('.field-div').each(function () {

                        let field_data = {};
                        let field_div = $(this);
                        let field_id = field_div.data('field-id');
                        let type = field_div.data('type');

                        field_data['file_id'] = $('#file_id').val();
                        field_data['field_id'] = field_id;
                        field_data['group_id'] = field_div.data('group-id');
                        field_data['page'] = field_div.data('page');
                        field_data['field_type'] = type;
                        field_data['left'] = field_div.data('x');
                        field_data['top'] = field_div.data('y');
                        field_data['height'] = field_div.data('h');
                        field_data['width'] = field_div.data('w');
                        field_data['left_perc'] = field_div.data('xp');
                        field_data['top_perc'] = field_div.data('yp');
                        field_data['height_perc'] = field_div.data('hp');
                        field_data['width_perc'] = field_div.data('wp');

                        // inputs are arrays and have their own table
                        field_data['field_data_input'] = [];
                        //field_data['field_data_input_helper_text'] = [];
                        field_data['field_data_input_id'] = [];

                        if (field_div.find('.field-data-input').length > 0) {
                            field_div.find('.field-data-input').each(function () {
                                if ($(this).val() != '') {
                                    field_data['field_data_input'].push($(this).val());
                                    field_data['field_data_input_id'].push($(this).data('id'));
                                }
                            });
                        }

                        field_div.find('.field-data-name').each(function () {
                            if ($(this).val() != '') {
                                field_data['field_name'] = $(this).val();
                                let field_type = $(this).data('field-type');
                                field_data['field_name_type'] = field_type;
                            }
                        });


                        //field_data['helper_text'] = field_div.find('input.field-data-helper-text').val();

                        field_data['number_type'] = field_div.find('select.field-data-number-type').val();

                        field_data['address_type'] = field_div.find('select.field-data-address-type').val();

                        field_data['textline_type'] = field_div.find('select.field-data-textline-type').val();

                        field_data['name_type'] = field_div.find('select.field-data-name-type').val();

                        field_data['radio_value'] = field_div.find('.field-data-radio-value').val();

                        field_data['checkbox_value'] = field_div.find('.field-data-checkbox-value').val();

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
                        if(response.error == 'published') {
                            $('#modal_danger').modal('show').find('.modal-body').html('This form has already been published. It can no longer be edited');
                        } else {
                            toastr['success']('Fields Successfully Saved');
                        }

                        $('#save_add_fields').html('<i class="fad fa-save mr-2"></i> Save');
                    }
                });
            } else {
                $('#modal_danger').modal('show').find('.modal-body').html('All Fields Must Be Completed');
            }


        }
*/

    });

}


