const writtenNumber = require('written-number');
/* import datepicker from 'js-datepicker'; */


if (document.URL.match(/edit_files/)) {

    $(document).ready(function () {



        form_elements();

        $('[data-address-type="state"]').addClass('uppercase').attr('maxlength', 2);
        $('[data-address-type="zip"]').addClass('numbers-only').attr('maxlength', 5);

        if ($('.field-datepicker').length > 0) {

            $('.field-datepicker').each(function() {
                let id = $(this).prop('id');
                window.picker = datepicker('#'+id, {
                    onSelect: (instance, date) => {
                        const value = date.toLocaleDateString();
                        $('#' + instance.el.id).prev('div.data-div').html(value);
                    },
                    onHide: instance => {
                        $('.field-div').removeClass('active');
                    },
                    formatter: (input, date, instance) => {
                        const value = date.toLocaleDateString();
                        input.value = value;
                    },
                    showAllDates: true,
                });
            });


        }

        let field_div_count = $('.field-div').length;
        let field_count = 0;
        $('.field-div').each(function () {
            var group_id = '';
            group_id = $(this).data('group-id');
            // add grouped class
            if ($('.group_' + group_id).length > 1) {
                $('.group_' + group_id).removeClass('standard').addClass('group');
            }
            // date field has no form-div so using field-div instead
            var type = $(this).data('type');
            var form_div;
            if (type == 'date' || type == 'radio' || type == 'checkbox') {
                form_div = $(this);
            } else {
                form_div = $(this).find('.form-div');
            }
            fill_fields(type, group_id, form_div, 'load');
            field_count += 1;
            if(field_count == field_div_count) {
                save_field_input_values('yes');
            }
        });

        field_list();

        $('#save_field_input_values_button').click(function() {
            save_field_input_values('no');
        });



        // on page click hide all focused els
        $(document).on('click', '.field-container', function (e) {
            if (!$(e.target).is('.field-div *')) {
                $('.focused').hide();
                field_list();
            }
        });

        $('.modal').on('hide.bs.modal', function () {
            reset_field_properties();
        });

        $('.field-div').not('.disabled').click(function () {

            var group_id = $(this).data('group-id');
            // checkboxes and radios never get highlighted
            if ($(this).data('type') != 'checkbox' && $(this).data('type') != 'radio') {

                $('.field-div').removeClass('active');
                $(this).addClass('active');

                if(!$(this).hasClass('date')) {
                    $(this).find('.modal').modal('show');
                    $('.modal-backdrop').appendTo($(this).closest('.field-div'));
                    // hide sidebar. not sure why it shows
                    $('.edit-file-sidebar').css({ 'z-index': '-1' });
                    $('.modal').on('hidden.bs.modal', function (e) {
                        $('.edit-file-sidebar').css({ 'z-index': '1' });
                    });
                }

            } else {

                if ($(this).data('type') == 'radio') {

                    $('.group_' + group_id).find('.data-div').html('');
                    $('.group_' + group_id).find('input[type="radio"]').attr('checked', false);
                    $(this).find('.data-div').next('input[type="radio"]').attr('checked', true);
                    $(this).find('.data-div').html('x');

                } else {

                    // FIXME: need to show/hide checkboxes on click
                    // have to add input value to checkboxes like radios
                    let check = $(this).find('input[type="checkbox"]');
                    let checked = check.attr('checked');
                    if (checked == false || checked == undefined) {
                        check.attr('checked', true);
                        $(this).find('.data-div').html('x');
                    } else {
                        check.attr('checked', false);
                        $(this).find('.data-div').html('');
                    }

                }

            }

        });


        $('.save-fillable-fields').click(function () {
            var type = $(this).data('type');
            var group_id = $(this).data('group-id');
            var form_div = $(this).parent('div.modal-footer').prev('div.modal-body').find('.form-div');
            fill_fields(type, group_id, form_div, 'save');
        });

        // highlight active thumb when clicked and scroll into view
        $('.file-view-thumb-container').click(function () {
            $('.file-view-thumb-container').removeClass('active');
            $(this).addClass('active');
            let id = $(this).data('id');
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


        /* if($(window).width() < 768) {
            $('.form-options-container').draggable({ axis: 'x' });
        } */

        // action buttons
        $(document).on('click', '#rotate_form_button:not(.disabled)', rotate_form);
        //$(document).on('click', '#to_pdf_button:not(.disabled)', to_pdf);
        $(document).on('click', '#show_edit_options_button:not(.disabled)', show_edit_options);
        $(document).on('click', '#save_edit_options_button', save_edit_options);
        $(document).on('click', '#cancel_edit_options_button', close_edit_options);

        $('.edit-form-action').click(function() {
            $('.edit-form-action').removeClass('active text-white').addClass('text-primary-dark');
            $(this).removeClass('text-primary-dark').addClass('active text-white');
        });

        /* setTimeout(function() {
            if(window.location.href.match(/topdf/)) {
                to_pdf();
                window.history.pushState('data', 'Title', window.location.href.replace(/\/topdf/, ''));
                change_url();
            }
        }, 1000); */

    });

    function show_edit_options() {
        // show edit menu options and hide others
        $('.form-options-container').find('.form-options-div').not('.edit-options').hide();
        $('.edit-options').show();
        // disable filling for all fields
        $('.field-div').addClass('disabled');
        // remove user fields
        $('.user-field-div').remove();

        let Listing_ID = $('#Listing_ID').val();
        let Agent_ID = $('#Agent_ID').val();
        let file_id = $('#file_id').val();
        let file_type = $('#file_type').val();

        let formData = new FormData();
        formData.append('Listing_ID', Listing_ID);
        formData.append('Agent_ID', Agent_ID);
        formData.append('file_id', file_id);
        formData.append('file_type', file_type);
        axios.post('/agents/doc_management/transactions/edit_files/get_user_fields', formData, axios_options)
        .then(function (response) {
            response.data.forEach(function(field) {

                let type = field['field_type'];
                let field_class = '';
                let field_html = '';
                let handles = ' \
                <div class="ui-resizable-handle ui-resizable-e focused"></div> \
                <div class="ui-resizable-handle ui-resizable-w focused"></div> \
                ';

                if(type == 'highlight') {
                    handles = ' \
                    <div class="ui-resizable-handle ui-resizable-nw focused"></div> \
                    <div class="ui-resizable-handle ui-resizable-ne focused"></div> \
                    <div class="ui-resizable-handle ui-resizable-se focused"></div> \
                    <div class="ui-resizable-handle ui-resizable-sw focused"></div> \
                    ';
                }

                if(type == 'user_text') {
                    field_class = 'user-field-div textline-div';
                    field_html = '<div class="textline-html"></div>';
                } else if (type == 'strikeout') {
                    field_class = 'user-field-div strikeout-div';
                    field_html = '<div class="strikeout-html"></div>';
                } else if (type == 'highlight') {
                    field_class = 'user-field-div highlight-div';
                    field_html = '<div class="highlight-html"></div>';
                }

                let field_div = ' \
                    <div class="field-div '+field_class+' group_'+field['group_id']+' ui-resizable ui-draggable" style="position: absolute; top: '+field['top_perc']+'%; left: '+field['left_perc']+'%; height: '+field['height_perc']+'%; width: '+field['width_perc']+'%;" id="field_'+field['group_id']+'" data-field-id="'+field['group_id']+'" data-group-id="'+field['group_id']+'" data-type="'+type+'" data-page="'+field['page']+'"> \
                        <div class="field-options-holder focused" style="right: 0px; display: none;"> \
                            <div class="btn-group" role="group" aria-label="Field Options"> <a type="button" class="btn btn-primary field-handle ui-draggable-handle"><i class="fal fa-arrows fa-lg"></i></a> <a type="button" class="btn btn-primary remove-field"><i class="fal fa-times-circle fa-lg"></i></a> </div> \
                        </div> \
                        '+handles+' \
                        '+field_html+' \
                    </div> \
                ';

                let field_container = $('.file-view-page-container[data-id="'+field['page']+'"]').find('.field-container');
                field_container.append(field_div);
                setTimeout(function() {
                    $('.focused').hide();
                }, 500);
                set_hwxy($('#field_' + field['group_id']), field['group_id'], type);

                set_field_options(type, $('#field_' + field['group_id']), '', '', field_container);

            });
        })
        .catch(function (error) {
            console.log(error);
        });


        $('#file_viewer').off('dblclick').on('dblclick', '.file-view-page-container.active .file-image-bg', function (e) {
            add_field(e);
        });
    }

    function add_field(e) {

        let field_type = $('.edit-form-action.active').data('field-type');
        if(field_type) {

            let container = $(e.target.parentNode);

            // get bounding box coordinates
            let rect = e.target.getBoundingClientRect();
            // get target coordinates
            let x = parseInt(Math.round(e.clientX - rect.left));
            let y = parseInt(Math.round(e.clientY - rect.top));

            let x_perc = pix_2_perc_xy('x', x, container);
            let y_perc = pix_2_perc_xy('y', y, container);

            let ele_h_perc = 1.3;

            // remove element height from top position
            y_perc = y_perc - ele_h_perc;

            // set w and h for new field
            let h_perc = 1.3;
            let w_perc = 15;
            h_perc = parseFloat(h_perc);
            w_perc = parseFloat(w_perc);

            // create unique id for field
            let id = Date.now();

            //create field and attach to container
            let field = field_html(h_perc, w_perc, x_perc, y_perc, id, id, $('#active_page').val(), field_type);

            // hide all handles and buttons
            //$('.focused').hide();

            // append new field
            $(container).append(field);

            set_hwxy($('#field_' + id), id, field_type);

            keep_in_view($('#field_' + id), w_perc, x_perc, y_perc, field_type);

            set_field_options(field_type, $('#field_' + id), id, rect, container);

        }
    }

    function set_field_options(field_type, ele, id, rect, container) {

        ele.click(function (e) {

            if (e.target === this) {
                e.stopPropagation();
            }
            $('.focused').hide();
            ele.find('.focused').show();
            let group_id = ele.data('group-id');
            let x_pos = ele.position().left;
            let doc_width = $('#file_viewer').width();
            let ele_pos = {
                left: '0px'
            }
            if(x_pos > doc_width / 2) {
                ele_pos = {
                    right: '0px'
                }
            }
            ele.find('.field-options-holder').css(ele_pos);
            set_hwxy(ele, group_id, field_type);

        });

        let handles = {
            'e': '.ui-resizable-e', 'w': '.ui-resizable-w'
        };
        if(field_type == 'highlight') {
            handles = {
                'nw': '.ui-resizable-nw', 'ne': '.ui-resizable-ne', 'se': '.ui-resizable-se', 'sw': '.ui-resizable-sw'
            }
        }
        ele.resizable({
            containment: container,
            handles: handles,
            stop: function (e, ui) {
                let resized_ele = $(e.target);
                setTimeout(function() {
                    set_hwxy(resized_ele, '', field_type);
                }, 500);

            }
        });

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
                //let dragged_h_perc = pix_2_perc_hw('height', dragged_h, container);
                let dragged_w_perc = pix_2_perc_hw('width', dragged_w, container);
                //let dragged_group_id = dragged_ele.data('group-id');


                setTimeout(function() {
                    set_hwxy(dragged_ele, '', field_type);
                    keep_in_view(dragged_ele, dragged_w_perc, dragged_x_perc, dragged_y_perc, field_type);
                }, 500);

            }
        });


        // hide all handles and buttons when another container is selected
        $('.field-select-container').click(function (e) {
            $('.focused').hide();
        });

        // remove field
        $('.remove-field').off('click').on('click', function () {
            $(this).closest('.field-div').remove();

            setTimeout(function () {
                field_list();
            }, 500);
        });



    }

    function field_html(h_perc, w_perc, x_perc, y_perc, id, group_id, page, type) {

        let field_class = '';
        let field_html = '';
        let handles = ' \
        <div class="field-handle ui-resizable-handle ui-resizable-e focused"></div> \
        <div class="field-handle ui-resizable-handle ui-resizable-w focused"></div> \
        ';

        if(type == 'highlight') {
            handles = ' \
            <div class="field-handle ui-resizable-handle ui-resizable-nw focused"></div> \
            <div class="field-handle ui-resizable-handle ui-resizable-ne focused"></div> \
            <div class="field-handle ui-resizable-handle ui-resizable-se focused"></div> \
            <div class="field-handle ui-resizable-handle ui-resizable-sw focused"></div> \
            ';
        }

        if(type == 'user_text') {
            field_class = 'user-field-div textline-div standard';
            field_html = '<div class="textline-html"></div>';
        } else if (type == 'strikeout') {
            field_class = 'user-field-div strikeout-div standard';
            field_html = '<div class="strikeout-html"></div>';
        } else if (type == 'highlight') {
            field_class = 'user-field-div highlight-div standard';
            field_html = '<div class="highlight-html"></div>';
        }

        return ' \
        <div class="field-div new '+ field_class + ' active group_' + group_id + '" style="position: absolute; top: ' + y_perc + '%; left: ' + x_perc + '%; height: ' + h_perc + '%; width: ' + w_perc + '%;" id="field_' + id + '" data-field-id="' + id + '" data-group-id="' + group_id + '" data-type="' + type + '" data-page="' + page + '"> \
            <div class="field-options-holder focused"> \
                <div class="btn-group" role="group" aria-label="Field Options"> \
                    <a type="button" class="btn btn-primary field-handle"><i class="fal fa-arrows fa-lg"></i></a> \
                    <a type="button" class="btn btn-primary remove-field"><i class="fal fa-times-circle fa-lg"></i></a> \
                </div> \
            </div> \
            '+ handles + ' \
            '+ field_html + ' \
        </div> \
        ';
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
        dist = 3;
        let cd_adjusted = cw;

        let x_pos = ele.position().left;
        let doc_width = $('#file_viewer').width();

        if(x_pos > (doc_width / 2)) {
            ele.find('.field-options-holder').css({ left: '' }).animate({ right: '0px' });
        } else {
            ele.find('.field-options-holder').css({ right: '' }).animate({ left: '0px' });
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

    function save_edit_options() {

        let data = [];

        if ($('.user-field-div').length > 0) {

            $('.user-field-div').each(function () {

                let field_data = {};
                field_data['Listing_ID'] = $('#Listing_ID').val();
                field_data['Agent_ID'] = $('#Agent_ID').val();

                let type = $(this).data('type');
                field_data['file_type'] = $('#file_type').val();
                field_data['file_id'] = $('#file_id').val();
                field_data['field_id'] = $(this).data('field-id');

                let user_field_type = 'Text';
                if(type == 'strikeout') {
                    user_field_type = 'Strikeout';
                } else if(type == 'highlight') {
                    user_field_type = 'Highlight';
                }

                field_data['field_name'] = 'User '+user_field_type;
                field_data['field_name_display'] = 'User '+user_field_type;
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

                data.push(field_data);

            });

        } else {

            let field_data = {};
            field_data['Listing_ID'] = $('#Listing_ID').val();
            field_data['Agent_ID'] = $('#Agent_ID').val();
            field_data['file_type'] = $('#file_type').val();
            field_data['file_id'] = $('#file_id').val();

            data.push(field_data);

        }

        data = JSON.stringify(data);

        let formData = new FormData();
        formData.append('data', data);
        axios.post('/agents/doc_management/transactions/edit_files/save_edit_options', formData, axios_options)
        .then(function (response) {
            location.reload();
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    function close_edit_options() {
        location.reload();
    }

    function to_pdf() {

        global_loading_on('', '<div class="h3 text-white">Merging Fields, Creating and Saving PDF.</div> <div class="h3 mt-5 text-yellow">Please be patient, this process can take <br>5 - 10 seconds for each page.</div>');
        // fields that css will be changed during export to pdf. They will be reset after
        let els = '.data-div, .file-image-bg, .field-div, .data-div-radio-check';
        let styles;
        $(els).each(function () {
            let data_div = $(this);
            styles = ['color', 'font-size', 'line-height', 'font-weight', 'opacity', 'background', 'margin-left', 'padding-left', 'height', 'display', 'position', 'top'];
            $.each(styles, function (index, style) {
                data_div.data(style, data_div.css(style));
            });
        });

        // set inline styles for PDF
        // system fields
        $('.data-div').not('.data-div-radio-check, .highlight, .strikeout').css({ 'font-size': '.9rem', 'color': '#000', 'padding-left': '5px', 'padding-top': '3px', 'font-family': 'Arial', 'letter-spacing': '0.03rem' });
        $('.data-div-radio-check').css({ 'margin-left': '1px', 'color': '#000', 'font-size': '1.2em', 'line-height': '80%', 'font-weight': 'bold' });
        // remove background
        $('.file-image-bg').css({ opacity: '0.0' });
        $('.field-div').css({ background: 'none' });

        // user fields
        $('.data-div.highlight').css({ background: 'yellow', opacity: '0.5', height: '100%' });
        $('.data-div.strikeout').css({ width: '100%', height: '2px', background: 'black', display: 'block', position: 'relative', 'margin-top': '7px' });


        let file_id = $('#file_id').val();
        let file_name = $('#file_name').val();
        let file_type = $('#file_type').val();
        let Listing_ID = $('#Listing_ID').val();

        // remove datepicker html, datepicker input, background img, modals, left over input fields
        let elements_remove = '.qs-datepicker-container, .field-datepicker, .file-image-bg, .fillable-field-input, .modal';

        let formData = new FormData();

        // get html from all pages to add to pdf layer
        let c = 0;
        $('.file-view-page-container').each(function () {
            c += 1;
            let container = $(this);
            let page_html = container.clone();
            page_html.find(elements_remove).remove();
            page_html = page_html.wrap('<div>').parent().html();

            formData.append('page_' + c, page_html);
            console.log(page_html);
        });

        formData.append('page_count', c);
        formData.append('file_id', file_id);
        formData.append('file_type', file_type);
        formData.append('file_name', file_name);
        formData.append('Listing_ID', Listing_ID);

        // reset all styles
        setTimeout(function () {
            $(els).each(function () {
                let data_div = $(this);
                $.each(styles, function (index, style) {
                    data_div.css(style, data_div.data(style));
                });
            });

        }, 300);

        axios_options['header'] = { 'content-type': 'multipart/form-data' };
        axios.post('/agents/doc_management/transactions/edit_files/convert_to_pdf', formData, axios_options)
            .then(function (response) {

                global_loading_off();
                toastr['success']('Document Saved Successfully');
            })
            .catch(function (error) {
                //console.log(error);
                });



    }

    function rotate_form() {
        $('.fa-sync-alt').addClass('fa-spin');
        global_loading_on('', '<div class="text-white">Rotating Document</div>');
        $('.file-view-page-container, .file-view-thumb-container').addClass('fadeOut');
        let file_id = $('#file_id').val();
        let file_type = $('#file_type').val();
        let Listing_ID = $('#Listing_ID').val();
        let formData = new FormData();
        formData.append('file_id', file_id);
        formData.append('file_type', file_type);
        formData.append('Listing_ID', Listing_ID);
        axios.post('/agents/doc_management/transactions/edit_files/rotate_document', formData, axios_options)
        .then(function (response) {
            setTimeout(function() {
                location.reload();
            }, 500);
        })
        .catch(function (error) {
            console.log(error);
        });

    }

    function save_field_input_values(on_load) {
        let field_data = [];

        $('.fillable-field-input').not('div.fillable-field-input').each(function () {
            let input_value = '';
            let input_id = $(this).attr('id');
            let file_id = $('#file_id').val();
            let file_type = $('#file_type').val();
            let common_name = $(this).data('common-name');
            let Listing_ID = $('#Listing_ID').val();
            let Agent_ID = $('#Agent_ID').val();
            if ($(this).attr('type') == 'radio' || $(this).attr('type') == 'checkbox') {
                if ($(this).is(':checked')) {
                    input_value = $(this).val();
                }
            } else {
                input_value = $(this).val();
            }

            field_data.push({
                input_id: input_id,
                input_value: input_value,
                file_id: file_id,
                file_type: file_type,
                common_name: common_name,
                Listing_ID: Listing_ID,
                Agent_ID: Agent_ID
            });
        });
        axios.post('/agents/doc_management/transactions/edit_files/save_field_input_values', field_data, axios_options)
            .then(function (response) {
                if(on_load == 'no') {
                    to_pdf();
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    function fill_fields(type, group_id, form_div, fill_type) {

        if (type == 'number') {

            let input = form_div.find('.fillable-field-input');

            let num = '';
            if (input.val() != '') {
                num = parseInt(input.val());
            }
            // add values to data-div for each field in group
            $('.group_' + group_id).each(function () {
                // add values to all inputs in group
                $(this).find('.fillable-field-input').val(num).data('default-value', num).trigger('change');
                // only the written fields will be split.
                let subtype = $(this).data('number-type');
                let data_div = $(this).find('.data-div');
                if (input.val() == '') {
                    let data_div = $(this).find('.data-div');
                    data_div.html('');
                } else {
                    if (subtype == 'numeric') {
                        data_div.html(global_format_number(num));
                    } else {
                        split_lines(group_id, writtenNumber(num));
                    }
                }
            });



        } else if (type == 'address') {
            // get inputs array
            let inputs = form_div.find('.fillable-field-input');
            // create labels and names from array
            let address_labels = [];
            let address_names = [];
            inputs.each(function () {
                address_labels.push($(this).data('type'));
                address_names.push($(this).data('address-type'));
            });
            // add values for inputs and match with name/label
            let address_values = [];
            $.each(address_labels, function (index, address_label) {
                inputs.each(function () {
                    if ($(this).data('type') == address_label) {
                        address_values.push($(this).val());
                    }
                });
            });

            $('.group_' + group_id).each(function () {

                let group = $(this);
                let address_type = $(this).data('address-type');

                group.find('.fillable-field-input').each(function () {
                    let input = $(this);
                    $.each(address_labels, function (index, address_label) {
                        if (input.data('type') == address_label) {
                            input.val(address_values[index]).data('default-value', address_values[index]).trigger('change');
                        }
                    });
                }).trigger('change');

                $.each(address_names, function (index, address_name) {
                    if (address_type != 'full') {
                        if (group.data('address-type') == address_name) {
                            group.find('.data-div').html(address_values[index]);
                        }
                    } else {
                        let full_address = address_values[0] + ' ' + address_values[1] + ' ' + address_values[3] + ' ' + address_values[4];
                        group.find('.data-div').html(full_address);
                        split_lines(group_id, full_address);
                    }
                });

            });

        } else if (type == 'name') {

            let inputs = form_div.find('.fillable-field-input');

            // get all input labels from data-type
            let name_labels = [];
            inputs.each(function () {
                name_labels.push($(this).data('type'));
            });

            let name_values = [];
            // get values for each label
            $.each(name_labels, function (index, name_label) {
                inputs.each(function () {
                    if ($(this).data('type') == name_label) {
                        name_values.push($(this).val());
                    }
                });
            });

            $('.group_' + group_id).each(function () {
                let group = $(this);
                let name1 = $(this).find('.fillable-field-input').eq(0).val();
                let name2 = $(this).find('.fillable-field-input').eq(1).val();
                let names = name1;

                if (name2 != undefined && name2 != '') {
                    names = names + ', ' + name2;
                }

                if (!names.match(/undefined/)) {

                    group.find('.data-div').html(names);
                    split_lines(group_id, names);

                    group.find('.fillable-field-input').each(function () {
                        let inputs = $(this);
                        $.each(name_labels, function (index, name_label) {
                            if (inputs.data('type') == name_label) {
                                inputs.val(name_values[index]).data('default-value', name_values[index]).trigger('change');
                            }
                        });
                    }).trigger('change');
                }
            });


        } else if (type == 'textline') {

            let textarea = form_div.find('.fillable-field-input');
            let text = textarea.val();
            textarea.data('default-value', text);
            split_lines(group_id, text);

        } else if (type == 'date') {

            let input = form_div.find('.fillable-field-input');
            input.data('default-value', input.val());
            $('.group_' + group_id).find('.data-div').html(input.val());

        } else if (type == 'radio') {

            let input = form_div.find('.fillable-field-input');
            if (input.is(':checked')) {
                input.data('default-value', 'checked');
                input.prev('.data-div').html('x');
            }

        } else if (type == 'checkbox') {

            let input = form_div.find('.fillable-field-input');
            if (input.is(':checked')) {
                input.data('default-value', 'checked');
                input.prev('.data-div').html('x');
            }

        } else if (type == 'user_text') {

            let textarea = form_div.find('.fillable-field-input');
            let text = textarea.val();

            textarea.data('default-value', text);
            form_div.closest('.field-div').find('.data-div').html(text);

        }

        if (fill_type == 'save') {
            $('.modal').modal('hide');
        }
    }

    function split_lines(group_id, text) {

        text = text.trim();
        //let str_len = text.length;
        let field_type = $('.group_' + group_id).data('type');

        // split value between lines
        if ($('.group_' + group_id).not('[data-number-type="numeric"]').length == 1) {
            if (field_type == 'number') {
                $('.group_' + group_id + '[data-number-type="written"]').first().find('.data-div').html(text);
            } else {
                $('.group_' + group_id).first().find('.data-div').html(text);
            }

        } else {

            $('.group_' + group_id).not('[data-number-type="numeric"]').find('.data-div').html('');
            $('.group_' + group_id).not('[data-number-type="numeric"]').each(function () {
                // if there is still text left over
                if (text != '') {

                    let width = String(Math.ceil($(this).width()));
                    let text_len = text.length;
                    let max_chars = width * .18;
                    if (text_len > max_chars) {
                        let section = text.substring(0, max_chars);
                        let end = section.lastIndexOf(' ');
                        let field_text = text.substring(0, end);
                        $(this).find('.data-div').html(field_text);
                        let start = end + 1;
                        text = text.substring(start);
                    } else {
                        $(this).find('.data-div').html(text);
                        text = '';
                    }
                }
            });

        }
    }

    function field_list() {
        $('.field-list-container').html('');
        $('.file-view-page-container').each(function () {

            let page_number = $(this).data('id');

            $('.field-list-container').append('<div class="page-container" id="page_container_' + page_number + '"></div>');

            let page_container = $('#page_container_' + page_number);

            page_container.append('<div class="font-weight-bold text-white bg-primary p-1 pl-2 mb-2">Page ' + page_number + '</div>');

            // get unique group ids
            var group_ids = [];

            $(this).find('.field-div').each(function () {
                group_ids.push($(this).data('group-id'));
            });
            group_ids = group_ids.filter(global_filter_array);
            // get all field names and add to field list
            $.each(group_ids, function (index, group_id) {
                let group = $('.group_' + group_id);
                let type = group.data('type');
                let order = Math.ceil(group.data('y'));
                let name = '';
                if (group.data('type') == 'checkbox') {
                    group.each(function () {
                        name = $(this).data('customname');
                        page_container.append('<div class="mb-1 border-bottom border-primary field-list-div" data-order="' + order + '"><a href="javascript: void(0)" class="field-list-link ml-3" data-group-id="' + group_id + '" data-type="' + type + '">' + name + '</a></div>');
                    });
                } else {

                    name = group.data('customname');
                    if (group.data('commonname') != undefined && group.data('commonname') != '') {
                        name = group.data('commonname');
                    }

                    if (name == undefined || name == 'undefined' || name == '' || name == 'User Field') {
                        if(type == 'user_text') {
                            page_container.append('<div class="mb-1 border-bottom border-primary field-list-div" data-order="' + order + '"><a href="javascript: void(0)" class="field-list-link ml-3" data-group-id="' + group_id + '" data-type="' + type + '">User Text Field</a></div>');
                        }
                    } else {
                        page_container.append('<div class="mb-1 border-bottom border-primary field-list-div" data-order="' + order + '"><a href="javascript: void(0)" class="field-list-link ml-3" data-group-id="' + group_id + '" data-type="' + type + '">' + name + '</a></div>');
                    }

                }

            });

            let fields = page_container.find('.field-list-div');
            fields.sort(function(a, b){
                return $(a).data('order')-$(b).data('order')
            });


            page_container.append(fields);

            $('.field-list-link').off('click').on('click', function (e) {
                //e.stopPropagation();
                let group_id = $(this).data('group-id');
                let type = $(this).data('type');
                let ele = $('.field-div[data-group-id="' + group_id + '"]').first();

                if (type == 'date') {
                    setTimeout(function() {
                        ele.find('.field-datepicker').focus().trigger('click').next('.qs-datepicker-container').removeClass('qs-hidden');
                    }, 500);
                } else {
                    if (type != 'checkbox' && type != 'radio') {
                        ele.find('.modal').modal('show');
                    }
                }
                $('.field-div').removeClass('active');
                ele.addClass('active');

                let container = $('#file_viewer');
                let scrollTo = $('#field_' + group_id).first();
                container.animate({
                    scrollTop: (scrollTo.offset().top - container.offset().top + container.scrollTop()) - 200
                });
                setTimeout(function() {
                    if (type != 'checkbox' && type != 'radio') {
                        ele.trigger('click');
                    }
                    //$('.modal.show').find('input').eq(0).trigger('click').focus().next('label').addClass('active');
                }, 200);

            });

        });

    }

    function reset_field_properties() {
        // reset name fields
        $('.form-div').each(function () {
            $(this).find('input, textarea').each(function () {
                $(this).val($(this).data('default-value')).trigger('change');
            });
        });
        $('.field-div').removeClass('active');
    }
}
