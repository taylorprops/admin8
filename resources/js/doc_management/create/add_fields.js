import { filter_array } from '../../global.js';
if (document.URL.match(/create\/add_fields/)) {

    $(document).ready(function () {


        // TODO run field_status() after any add or remove

        set_common_fields();

        // Show active field
        $('.field-wrapper').click(function () {
            $('.field-wrapper').removeClass('active');
            $(this).addClass('active');
            $('#active_field').val($(this).data('type'));
        });

        // set field options on load
        if ($('.field-div').length > 0) {


            $('.field-div').each(function () {
                var group_id = '';

                // get bounding box coordinates
                var rect = this.getBoundingClientRect();
                var container = $(this).closest('.field-container');

                var h = $(this).css('height').replace(/px/, '');
                var w = $(this).css('width').replace(/px/, '');
                var x = $(this).css('left').replace(/px/, '');
                var y = $(this).css('top').replace(/px/, '');
                // convert from % to px
                $(this).css('height', h + 'px');
                $(this).css('width', w + 'px');
                $(this).css('top', y + 'px');
                $(this).css('left', x + 'px');


                group_id = $(this).data('groupid');

                // clear other field when changing name
                if ($(this).data('type') != 'checkbox') {
                    $('#name_select_' + group_id).change(function () {
                        $('#name_input_' + group_id).val('').next('label').removeClass('active');
                        if ($(this).val() == '') {
                            $(this).next('label').removeClass('active');
                        }
                    });
                    $('#name_input_' + group_id).change(function () {
                        $('#name_select_'+group_id).val('').next('label').removeClass('active');
                    });
                }
                // add grouped class
                if ($('.group_' + group_id).length > 1) {
                    $('.group_' + group_id).removeClass('standard').addClass('group');
                };
                // hide add item on all fields of group but last
                $('.group_' + group_id).find('.add-item-container').hide();
                $('.group_' + group_id).find('.add-item-container').last().show();

                set_field_options($(this).data('type'), $(this), x, y, $(this).data('fieldid'), rect, container);
                set_hwxy($(this), h, w, x, y, $(this).data('groupid'), $(this).data('type'));


            });

            $('.focused').hide();

            field_status();

            $('.add-input').click(add_input);
            $('.delete-input').click(delete_input);

        }




        // on page double click add field
        $('#file_viewer').on('dblclick', '.file-view-page-container.active', function (e) { // changed from .field-container

            // disable zoom
            //$('.zoom-container').hide();
            // get container so easier to find elements
            var container = $(e.target.parentNode);

            var field_type = $('#active_field').val();

            // only if a field has been selected
            if (field_type != '') {

                // get bounding box coordinates
                var rect = e.target.getBoundingClientRect();
                // get target coordinates
                var x = parseInt(Math.round(e.clientX - rect.left));
                var y = parseInt(Math.round(e.clientY - rect.top));

                // yset bottom left of field div at pointer tip
                y = (y - parseInt($('#field_' + field_type + '_height').val()));

                // set w and h for next element created - always the same as last field type added
                var w = parseInt($('#field_' + field_type + '_width').val());
                var h = parseInt($('#field_' + field_type + '_height').val());


                // create unique id for field
                var id = Date.now();

                //create field and attach to container
                var field = field_html(h, w, x, y, id, id, $('#active_page').val(), field_type);

                // hide all handles and buttons
                $('.focused').hide();

                // append new field
                $(container).append(field);

                setTimeout(function() {
                    $('select.mdb-select').not('.initialized').materialSelect();
                    $('.add-input').click(add_input);
                    $('.delete-input').click(delete_input);
                }, 500);




                // clear other field when changing name
                if ($('#field_' + id).data('type') != 'checkbox') {
                    $('#name_select_' + id).change(function () {
                        $('#name_input_'+id).val('').next('label').removeClass('active');
                    });
                    $('#name_input_' + id).change(function () {
                        $('#name_select_'+id).val('').next('label').removeClass('active');
                    });
                }

                set_hwxy($('#field_' + id), h, w, x, y, id, field_type);

                keep_in_view($('#field_' + id), id, h, w, x, y, container, field_type);

                set_field_options(field_type, $('#field_' + id), x, y, id, rect, container);

                setTimeout(function () {

                    $('#field_textline_groupid').val(id);
                    $('.field-div').removeClass('active');
                    $('#field_' + id).addClass('active');

                    $('.edit-properties-div *').dblclick(function (event) {
                        console.log('test');
                        event.stopPropagation();
                    });

                }, 100);

                field_status();

            }
        });


        // on page click hide all focused els
        $(document).on('click', '.field-container', function (e) {
            if (!$(e.target).is('.field-div *')) {
                $('.focused, .field-popup').hide();
                /*
                var fields = ['textline', 'name', 'address', 'date', 'number', 'radio', 'checkbox'];
                fields.forEach(function (field) {
                    $('#field_' + field + '_groupid').val('');
                });
                */
                $('.field-div').removeClass('active');
                // reset name fields
                $('.form-div').each(function () {
                    $(this).find('select, input').each(function () {
                        $(this).val($(this).data('defaultvalue'));
                    });
                });
            }
        });



        function set_field_options(field_type, ele, x, y, id, rect, container) {
            var handles = {
                'ne': '.ui-resizable-ne', 'nw': '.ui-resizable-nw', 'se': '.ui-resizable-se', 'sw': '.ui-resizable-sw'
            };
            var aspect_ratio = '';
            var max_height = 30;
            var min_height = 15;
            if (field_type == 'checkbox' || field_type == 'radio') {
                aspect_ratio = '4 / 4';
                max_height = 40;
            }

            ele.click(function (e) {
                e.stopPropagation();
                $('.focused').hide();
                $(this).find('.focused').show();
                $('.field-div').removeClass('active');
                $(this).addClass('active');
                set_hwxy($(this), $(this).height(), $(this).width(), $(this).position().left, $(this).position().top, $(this).data('groupid'), field_type);
            })
                .resizable({
                    containment: container, //$('.field-container'), // tried containment: $('#page_div_'+$('#active_field').val()), but not go
                    handles: handles,
                    maxHeight: max_height,
                    minHeight: min_height,
                    aspectRatio: aspect_ratio,
                    stop: function (e, ui) {
                        var height = $(this).height();
                        var width = $(this).width();
                        var left = ui.position.left;
                        var top = ui.position.top;
                        // set size of checkboxes if groupped
                        if (field_type == 'checkbox') {
                            if ($('.group_' + ele.data('groupid')).length > 1) {
                                $('.group_' + ele.data('groupid')).each(function () {
                                    $(this).css({ left: left, height: height, width: width });
                                    set_hwxy($(this), $(this).height(), $(this).width(), $(this).position().left, $(this).position().top, '', field_type);
                                });
                            }
                        } else {
                            set_hwxy(ele, height, width, left, top, '', field_type);
                        }
                    }
                })
                .draggable({
                    containment: container,
                    handle: '.field-handle',
                    cursor: 'grab',
                    stop: function (e, ui) {
                        var height = $(this).height();
                        var width = $(this).width();
                        var left = ui.position.left;
                        var top = ui.position.top;
                        // align checkboxes if groupped
                        if (field_type == 'checkbox') {
                            if ($('.group_' + ele.data('groupid')).length > 1) {
                                $('.group_' + ele.data('groupid')).each(function () {
                                    $(this).css({ left: left });
                                    set_hwxy($(this), $(this).height(), $(this).width(), $(this).position().left, $(this).position().top, '', field_type);
                                });
                            }
                        }

                        keep_in_view(ele, id, height, width, left, top, container, field_type);
                        //set_hwxy($(this), $(this).height(), $(this).width(), ui.position.left, ui.position.top, '', field_type);
                    }
                });

            // hide all handles and buttons when another container is selected
            $('.field-select-container').click(function (e) {
                $('.focused').hide();
                $('.field-div').removeClass('active');
            });
            // remove field
            $('.remove-field').off('click').on('click', function () {
                var remove_groupid = $('.field-div.active').data('groupid');
                $('.field-div.active').remove();

                var group = $('.group_' + remove_groupid);
                if (group.length > 1) {
                    // hide add line option for all but last
                    group.find('.add-item-container').hide();
                    group.find('.add-item-container').last().show();
                } else {
                    group.find('.add-item-container').show();
                }
                // see if other divs in a group and if just one remove group class
                if (group.length == 1) {
                    group.removeClass('group').addClass('standard');
                }

                field_status();
            });

            if (field_type != 'date') {
                // add items to group
                ele.find('.field-add-item').off('click').on('click', function () {

                    // add line confirm div
                    var add_item = $(this).next('.add-item-div');
                    add_item.toggle();
                    $('.field-close-add-item').click(function () {
                        add_item.hide();
                    });
                    // after confirming
                    ele.find('.add-item').off('click').on('click', function () {
                        // assign group id for original field
                        var group_id = $(this).data('groupid');

                        var common_name = $('.group_' + group_id).data('commonname');
                        var custom_name = $('.group_' + group_id).data('customname');
                        $('.group_' + group_id).removeClass('standard').addClass('group');


                        var field_div = $(this).closest('.field-div');
                        var h = field_div.height();
                        var w = field_div.width();
                        var x = field_div.position().left;
                        var y = field_div.position().top;
                        // drop the new line 10px below the original
                        y = y + h + 10;

                        $('.field-div').removeClass('active');
                        // create new id for new field in group
                        id = Date.now();
                        var field = field_html(h, w, x, y, id, group_id, $('#active_page').val(), field_type);
                        // append new field
                        field_div.closest('.field-container').append(field);

                        setTimeout(function() {
                            $('select.mdb-select').not('.initialized').materialSelect();
                        }, 500);

                        var new_ele = $('#field_' + id);

                        add_item.toggle();
                        setTimeout(function () {
                            $('.focused').fadeOut();
                            $('.field-div').removeClass('active');
                            new_ele.addClass('active').find('.focused').fadeIn();
                            var h = new_ele.height();
                            var w = new_ele.width();
                            var x = new_ele.position().left;
                            var y = new_ele.position().top;

                            if ((parseInt(y) + parseInt(h)) > parseInt(container.height())) {
                                new_ele.css({ border: '3px dotted #900' });
                                setTimeout(function () {
                                    new_ele.remove();
                                }, 1000);
                                return false;
                            }

                            set_hwxy(new_ele, h, w, x, y, '', field_type);

                            // assign group id to new field
                            new_ele.data('groupid', group_id).removeClass('standard').addClass('group').addClass('group_' + group_id);

                            // move add line option to last line
                            $('.group_' + group_id).find('.add-item-container').hide();
                            $('.group_' + group_id).find('.add-item-container').last().show();

                            // clear other field when changing name
                            if (new_ele.data('type') != 'checkbox') {
                                $('#name_select_' + group_id).change(function () {
                                    $('#name_input_'+group_id).val('').next('label').removeClass('active');
                                });
                                $('#name_input_' + group_id).change(function () {
                                    $('#name_select_'+group_id).val('').next('label').removeClass('active');
                                });
                            }

                            setTimeout(function () {
                                // set values for field name
                                if (field_type != 'checkbox') {
                                    var new_form = new_ele.find('.form-div');
                                    new_form.find('select').val(common_name).data('defaultvalue', common_name);
                                    new_form.find('input').val(custom_name).data('defaultvalue', custom_name);
                                }
                                keep_in_view(new_ele, id, h, w, x, y, container, field_type);
                                set_field_options(field_type, new_ele, x, y, id, rect, container);
                                field_status();
                            }, 500);


                        }, 100);


                    });
                });
            }
            // add properties
            $('.field-properties').off('click').on('click', function () {

                var container = $(this).closest('.field-container');
                var edit_div = $(this).next('.edit-properties-div');

                /*

                var cw = container.width();
                var ch = container.height();

                x = edit_div.closest('.field-div').position().left;
                y = edit_div.closest('.field-div').position().top;
                if (x > (cw / 2)) {
                    edit_div.css({ right: '10px' });
                } else {
                    edit_div.css({ left: '-10px' });
                }
                if (y > (ch / 2)) {
                    edit_div.css({ top: '-85px' });
                } else {
                    edit_div.css({ top: '-10px' });
                }
                */

                edit_div.toggle();


                $('.field-save-properties').click(function () {
                    var group_id = $(this).data('groupid');
                    var type = $(this).data('type');
                    var form = $(this).closest('.form-div');
                    var common_name = form.find('select.field-data-name').val();
                    var custom_name = form.find('input.field-data-name').val();
                    var radio_input = form.find('input.field-data-radio-value');
                    var radio_value = radio_input.val();
                    var number_select = form.find('select.field-data-number-type');
                    var number_type = number_select.val();
                    var address_select = form.find('select.field-data-address-type');
                    var address_type = address_select.val();
                    var helper_textarea = form.find('textarea.field-data-helper_text');
                    var helper_text = helper_textarea.val();

                    // set default values and group values for properties
                    address_select.data('defaultvalue', address_type);


                    $('.group_' + group_id).each(function () {
                        $(this).find('.form-div').each(function () {
                            $(this).find('textarea.field-data-helper_text').val(helper_text).data('defaultvalue', helper_text).next('label').addClass('active');
                        });
                    });

                    if (number_type != '') {
                        if ($('.group_' + group_id).length > 1) {
                            var other_number_type = 'numeric';
                            if (number_type == 'numeric') {
                                other_number_type = 'written';
                            }
                            $('.group_' + group_id).find('select.field-data-number-type').not(number_select).val(other_number_type).data('defaultvalue', other_number_type);
                            number_select.data('defaultvalue', number_type);
                        }
                    }

                    if (type == 'checkbox') {
                        form.find('input.field-data-name').data('defaultvalue', custom_name);
                    } else if (type == 'radio') {
                        radio_input.data('defaultvalue', radio_value);
                        $('.group_' + group_id).each(function () {
                            $(this).find('.form-div').each(function () {
                                $(this).find('input.field-data-name').val(custom_name).data('defaultvalue', custom_name);
                            });
                        });
                    } else {
                        $('.group_' + group_id).each(function () {
                            $(this).data('commonname', common_name).data('customname', custom_name);
                            $(this).find('.form-div').each(function () {
                                $(this).find('select.field-data-name').val(common_name).data('defaultvalue', common_name);
                                $(this).find('input.field-data-name').val(custom_name).data('defaultvalue', custom_name);
                            });
                        });
                    }
                    edit_div.hide();
                    field_status();
                    $('.group_' + group_id).removeClass('field-error');

                });

                $('.field-close-properties').off('click').on('click', function () {
                    var form = $(this).closest('.form-div');
                    form.find('select, input').each(function () {
                        $(this).val($(this).data('defaultvalue'));
                    });
                    edit_div.hide();
                });
            });

        }

        function delete_input() {
            $(this).closest('.field-data-inputs-div').remove();
        }


        function add_input() {
            var append_to = $(this).prev('.field-data-inputs-container');
            var id = parseInt(append_to.find('.field-data-inputs-div').length) + 1;
            var field_id = $(this).data('fieldid');

            var new_input = ' \
            <div class="row p-2 border border-secondary mb-1 field-data-inputs-div"> \
                <div class="col-12"> \
                    <div class="md-form my-1"> \
                        <input type="text" class="form-control field-data-input" id="input_name_'+field_id+'_'+id+'" data-defaultvalue=""> \
                        <label for="input_name_'+field_id+'_'+id+'">Input Name</label> \
                    </div> \
                </div> \
                <div class="col-12"> \
                    <div class="md-form my-1"> \
                        <textarea class="form-control md-textarea field-data-input-helper-text" id="textarea_name_'+field_id+'_'+id+'" rows="1" data-defaultvalue=""></textarea> \
                        <label for="textarea_name_'+field_id+'_'+id+'">Input Helper Text</label> \
                    </div> \
                </div> \
            </div> \
            ';

            $(new_input).appendTo(append_to);
        }

        function field_properties(type, group_id, id) {
            var temp_id = Date.now();
            if (type == 'radio') {
                var custom_name_heading = 'Radio Group Name';
            } else {
                var custom_name_heading = 'Custom Name';
            }
            var properties = ' \
            <div class="form-div"> \
                <div class="card-header bg-secondary text-white h4 p-2">Field Properties</div> \
                <div class="bg-secondary-light text-orange p-2">Type - '+ type.toUpperCase() + '</div> \
                <div class="card-body p-1"> \
                    <div class="container"> \
                        <div class="row p-3 border border-secondary mb-1"> \
            ';
            if (type != 'checkbox' && type != 'radio') {
                properties = properties + ' \
                            <div class="col-12 md-form my-1" > \
                                <select class="field-data-name mdb-select colorful-select dropdown-primary" id="name_select_'+id+'" data-fieldtype="common"> \
                                    <option value="">&nbsp;</option> \
                                    ' + $('#' + type + '_select_options').val() + ' \
                                </select> \
                            </div> \
                            <label for="name_select_'+id+'" class="mdb-main-label">Select Common Name (Shared)</label> \
                            <div class="text-primary text-center w-100">OR</div> \
                ';
            }
            properties = properties + ' \
                            <div class="col-12 md-form my-1"> \
                                <input type="text" class="form-control field-data-name" id="name_input_'+ id +'" data-fieldtype="custom"> \
                                <label for="name_input_'+id+'">'+custom_name_heading+'</label> \
                            </div> \
                        </div> \
            ';
            if (type == 'number') {
                properties = properties + ' \
                        <div class="row p-3 border border-secondary mb-1"> \
                            <div class="col-12 md-form my-1"> \
                                <select class="field-data-number-type mdb-select colorful-select dropdown-primary" id="number_select_'+id+'" data-fieldtype="number-type"> \
                                    <option value="">&nbsp;</option> \
                                    <option value="numeric">Numeric - 3,000</option> \
                                    <option value="written">Written - Three Thousand</option> \
                                </select> \
                                <label for="number_select_'+id+'" class="mdb-main-label">Number Type</label> \
                            </div> \
                        </div> \
                ';
            } else if (type == 'address') {
                properties = properties + ' \
                        <div class="row p-3 border border-secondary mb-1"> \
                            <div class="col-12 md-form my-1"> \
                                <select class="field-data-address-type mdb-select colorful-select dropdown-primary" id="address_select_'+id+'" data-fieldtype="address-type"> \
                                    <option value="">&nbsp;</option> \
                                    <option value="full">Full Address</option> \
                                    <option value="street">Street Address</option> \
                                    <option value="city">City</option> \
                                    <option value="state">State</option> \
                                    <option value="zip">Zip Code</option> \
                                    <option value="county">County</option> \
                                </select> \
                                <label for="address_select_'+id+'" class="mdb-main-label">Address Type</label> \
                            </div> \
                        </div> \
                ';
            } else if (type == 'radio') {
                properties = properties + ' \
                        <div class="row p-3 border border-secondary mb-1"> \
                            <div class="col-12 md-form my-1"> \
                                <input type="text" class="form-control field-data-radio-value" id="field_value_input_'+id+'"> \
                                <label for="field_value_input_'+id+'">Field Value</label> \
                            </div> \
                        </div> \
                ';
            }
            properties = properties + ' \
                        <div class="row p-3 border border-secondary mb-1"> \
                            <div class="col-12 md-form my-1"> \
                                <textarea class="form-control md-textarea field-data-helper_text" rows="1" id="helper_text_input_'+ id + '"></textarea> \
                                <label for="helper_text_input_'+ id + '">Helper Text</label> \
                            </div> \
                        </div> \
            ';
            if (type == 'address' || type == 'name') {
                properties = properties + ' \
                        <div class="row p-3 border border-secondary mb-1"> \
                            <div class="col-12"> \
                                <h5 class="text-secondary">Inputs</h5> \
                                <div class="container field-data-inputs-container"> \
                                    <div class="row p-2 border border-secondary mb-1 field-data-inputs-div"> \
                                        <a href="javascript: void(0)" class="delete-input"><i class="fas fa-times-square text-danger fa-lg"></i></a> \
                                        <div class="col-12 mt-3"> \
                                            <div class="md-form my-1"> \
                                                <input type="text" class="form-control field-data-input" id="input_name_'+temp_id+'_1" data-defaultvalue=""> \
                                                <label for="input_name_'+temp_id+'_1">Input Name</label> \
                                            </div> \
                                        </div> \
                                        <div class="col-12"> \
                                            <div class="md-form my-1"> \
                                                <textarea class="form-control md-textarea field-data-input-helper-text" id="textarea_name_'+temp_id+'_1" rows="1" data-defaultvalue=""></textarea> \
                                                <label for="textarea_name_'+temp_id+'_1">Input Helper Text</label> \
                                            </div> \
                                        </div> \
                                    </div> \
                                </div> \
                                <a href="javascript: void(0);" class="text-green add-input" data-fieldid="'+id+'"><i class="fa fa-plus"></i> Add Input</a> \
                            </div> \
                        </div> \
                ';
            }

            properties = properties + '</div > \
                </div> \
            ';
            properties = properties + ' \
                <div class="card-footer d-flex justify-content-around bg-white"> \
                    <a href="javascript: void(0);" class="btn btn-success btn-sm shadow field-save-properties" data-groupid="'+ group_id + '" data-type="' + type + '">Save</a> \
                    <a href="javascript:void(0);" class="btn btn-danger btn-sm field-close-properties">Cancel</a> \
                </div> \
            </div> \
            ';

            return properties
        }

        function add_option(group_id, type) {
            if (type == 'textline') {
                var icon = '<i class="fas fa-horizontal-rule fa-lg text-primary"></i>';
            } else if (type == 'radio') {
                var icon = '<i class="fas fa-circle fa-lg text-primary"></i>';
            } else if (type == 'checkbox') {
                var icon = '<i class="fal fa-square-full fa-lg text-primary"></i>';
            }
            var option = ' \
            <div class="add-item-container mr-2" > \
                <div class="field-add-item mr-3 h-100"> \
                    '+ icon + ' \
                    <i class="fal fa-plus fa-xs ml-1 text-primary add-item-plus"></i> \
                </div> \
                <div class="add-item-div shadow-lg field-popup"> \
                    <div class="add-item-content"> \
                        Add Item To Group? \
                        <div class="d-flex justify-content-around"> \
                            <a href="javascript: void(0);" class="btn btn-success btn-sm add-item shadow" data-groupid="'+ group_id + '">Confirm</a> \
                            <a href="javascript:void(0);" class="btn btn-danger btn-sm field-close-add-item">Cancel</a> \
                        </div> \
                    </div> \
                </div> \
            </div> \
        ';
            return option;
        }

        function field_html(h, w, x, y, id, group_id, page, type) {

            var properties_html = field_properties(type, group_id, id);

            var options = '';
            var field_class = '';
            var field_html = '';
            var handles = ' \
            <div class="ui-resizable-handle ui-resizable-ne focused"></div> \
            <div class="ui-resizable-handle ui-resizable-se focused"></div> \
            <div class="ui-resizable-handle ui-resizable-nw focused"></div> \
            <div class="ui-resizable-handle ui-resizable-sw focused"></div> \
            ';
            if (type == 'textline' || type == 'name' || type == 'address' || type == 'number') {
                options = add_option(group_id, 'textline');
                field_class = 'textline-div standard';
                field_html = '<div class="textline-html"></div>';
            } else if (type == 'radio') {
                options = add_option(group_id, 'radio');
                field_class = type + '-div standard';
                field_html = '<div class="radio-html"></div>';
            } else if (type == 'checkbox') {
                options = add_option(group_id, 'checkbox');
                field_class = type + '-div standard';
                field_html = '<div class="checkbox-html"></div>';
            } else if (type == 'date') {
                field_class = 'textline-div standard';
                field_html = '<div class="textline-html"></div>';
            }

            return ' \
            <div class="field-div '+ field_class + ' active group_' + group_id + '" style="position: absolute; top: ' + y + 'px; left: ' + x + 'px; height: ' + h + 'px; width: ' + w + 'px;" id="field_' + id + '" data-fieldid="' + id + '" data-groupid="' + group_id + '" data-type="' + type + '" data-page="' + page + '"> \
                <div class="field-status-div d-flex justify-content-left"> \
                    <div class="field-status-name-div"></div> \
                    <div class="field-status-group-div float-right"></div> \
                </div> \
                <div class="field-options-holder focused shadow container"> \
                    <div class="row m-0 p-0"> \
                        <div class="col-2 p-0"> \
                            <div class="field-handle"><i class="fal fa-ellipsis-v-alt fa-lg text-primary"></i></div> \
                        </div> \
                        <div class="col-8 p-0"> \
                            <div class="d-flex justify-content-center"> \
                                '+ options + ' \
                                <div> \
                                    <div class="field-properties" data-groupid="'+ group_id + '"> \
                                        <i class="fal fa-info-circle fa-lg text-primary"></i> \
                                    </div> \
                                    <div class="edit-properties-div shadow field-popup card">'+ properties_html + '</div> \
                                </div> \
                            </div> \
                        </div> \
                        <div class="col-2 p-0"> \
                            <div class="remove-field"><i class="fal fa-times-circle fa-lg text-danger"></i></div> \
                        </div> \
                    </div> \
                </div> \
                '+ handles + ' \
                '+ field_html + ' \
            </div> \
            ;'
        }

        function field_status() {
            var group_ids = [];
            $('.field-div').each(function () {
                group_ids.push($(this).data('groupid'));
            });
            group_ids = group_ids.filter(filter_array);

            for (var i = 0; i < group_ids.length; i++) {

                // find out if grouped and add icon
                var grouped = false;
                if ($('.field-div[data-groupid="' + group_ids[i] + '"]').length > 1) {
                    grouped = true;
                }
                if (grouped == true) {
                    $('.field-div[data-groupid="' + group_ids[i] + '"]').each(function () {
                        // remove all group icons
                        $('.field-div[data-groupid="' + group_ids[i] + '"]').find('.field-status-group-div').html('');
                        // add group icon to last of all
                        $('.field-div[data-groupid="' + group_ids[i] + '"]').last().find('.field-status-group-div').html('<i class="fal fa-layer-group"></i>');

                    });
                }


                // add field names
                $('.field-div[data-groupid="' + group_ids[i] + '"]').each(function () {
                    var field_name = '';
                    // all but checkbox get names added only to the last
                    if ($(this).data('type') != 'checkbox') {

                        $(this).find('.field-status-name-div').html('');
                        $(this).find('.field-data-name').each(function () {
                            if ($(this).val() != '') {
                                field_name = $(this).val();
                                // add field name to last of each group
                                $('.field-div[data-groupid="' + group_ids[i] + '"]').find('.field-status-name-div').last().html(field_name);
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

        function set_hwxy(ele, h, w, x, y, groupid, type) {

            var hp, wp, xp, yp;
            var container = ele.closest('.field-container');

            if (h) {
                hp = (100 * parseFloat(h / parseFloat(container.height())));
                wp = (100 * parseFloat(w / parseFloat(container.width())));
                ele.data('h', h);
                ele.data('w', w);
                ele.data('hp', hp);
                ele.data('wp', wp);
                $('#field_' + type + '_height').val(h);
                $('#field_' + type + '_width').val(w);
                $('#field_' + type + '_heightp').val(hp);
                $('#field_' + type + '_widthp').val(wp);
            }
            if (x) {
                xp = (100 * parseFloat(x / parseFloat(container.width())));
                yp = (100 * parseFloat(y / parseFloat(container.height())));
                ele.data('x', x);
                ele.data('y', y);
                ele.data('xp', xp);
                ele.data('yp', yp);
                $('#field_' + type + '_x').val(x);
                $('#field_' + type + '_y').val(y);
                $('#field_' + type + '_xp').val(xp);
                $('#field_' + type + '_yp').val(yp);
            }
            if (groupid) {
                $('#field_' + type + '_groupid').val(groupid);
            }
            ele.data('page', ele.data('page'));

        }

        function keep_in_view(ele, id, h, w, x, y, container, type) {
            // adjust fields if placed out of bounds
            var dist = '';
            var cw = container.width();
            var cd_adjusted = '';
            if (type == 'textline' || type == 'name' || type == 'address' || type == 'date' || type == 'number') {
                dist = 15;
                cd_adjusted = cw;
            } else if (type == 'radio' || type == 'checkbox') {
                dist = 60;
                cd_adjusted = cw - 80;
            }

            if (x < dist) {
                ele.animate({ left: dist + 'px' });
            }
            if ((x + w) > cd_adjusted) {
                var pos = cw - w - dist;
                ele.animate({ left: pos + 'px' });
            }

            if (y < 40) {
                ele.animate({ top: '40px' });
            }

            setTimeout(function () {
                var h = ele.css('height').replace('px', '');
                var w = ele.css('width').replace('px', '');
                var x = ele.css('left').replace('px', '');
                var y = ele.css('top').replace('px', '');
                var groupid = ele.data('groupid');

                set_hwxy(ele, h, w, x, y, groupid, type);
            }, 1500);

        }

        function set_common_fields() {
            $.ajax({
                type: 'get',
                url: '/common_fields',
                dataType: "json",
                success: function (data) {
                    $.each(data, function (k) {
                        var type = k;
                        var select_options = '';
                        $.each(this, function (k, v) {
                            select_options = select_options + '<option value="' + v + '">' + v + '</option>';
                        });
                        $('#' + type + '_select_options').val(select_options);
                    });

                }
            });
        }

        /////////////////// Save data ///////////////////////
        $('#save_fields').click(save_add_fields);

        function save_add_fields() {
            // remove all error divs
            $('.field-error-div').remove();
            $('.field-error').removeClass('field-error');
            var errors = 'no';
            var data = [];

            if ($('.field-div').length > 0) {

                $('.field-div').each(function () {
                    // add error divs
                    $('<div class="field-error-div"></div>').insertBefore($(this).find('.form-div').find('.card-body'));

                    var field_data = {};

                    var type = $(this).data('type');

                    field_data['file_id'] = $('#file_id').val();
                    field_data['field_id'] = $(this).data('fieldid');
                    field_data['group_id'] = $(this).data('groupid');
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

                    if ($(this).find('.field-data-inputs-div').length > 0) {
                        $(this).find('.field-data-inputs-div').each(function () {
                            field_data['field_data_input'].push($(this).find('.field-data-input').val());
                            field_data['field_data_input_helper_text'].push($(this).find('.field-data-input-helper-text').val());
                        });
                    }

                    field_data['field_name'] = null;
                    field_data['field_name_type'] = null;
                    $(this).find('.field-data-name').each(function () {
                        if ($(this).val() != '') {
                            field_data['field_name'] = $(this).val();
                            field_data['field_name_type'] = $(this).data('fieldtype');
                        }
                    });
                    if (field_data['field_name'] == null) {
                        $(this).addClass('field-error').find('.field-error-div').append('<div class="field-error-item"><i class="fal fa-exclamation-triangle mr-2"></i> You must name the field</div>');
                        errors = 'yes';
                    }

                    field_data['helper_text'] = null;
                    if ($(this).find('textarea.field-data-helper_text').val() != '') {
                        field_data['helper_text'] = $(this).find('textarea.field-data-helper_text').val();
                    }
                    if (field_data['helper_text'] == null) {
                        $(this).addClass('field-error').find('.field-error-div').append('<div class="field-error-item"><i class="fal fa-exclamation-triangle mr-2"></i> You must enter the helper text</div>');
                        errors = 'yes';
                    }

                    field_data['number_type'] = null;
                    if (type == 'number') {
                        if ($(this).find('select.field-data-number-type').val() != '') {
                            field_data['number_type'] = $(this).find('select.field-data-number-type').val();
                        }
                        if (field_data['number_type'] == null) {
                            $(this).addClass('field-error').find('.field-error-div').append('<div class="field-error-item"><i class="fal fa-exclamation-triangle mr-2"></i> You must enter the number type</div>');
                            errors = 'yes';
                        }
                    }

                    field_data['address_type'] = null;
                    if (type == 'address') {
                        if ($(this).find('select.field-data-address-type').val() != '') {
                            field_data['address_type'] = $(this).find('select.field-data-address-type').val();
                        }
                        if (field_data['address_type'] == null) {
                            $(this).addClass('field-error').find('.field-error-div').append('<div class="field-error-item"><i class="fal fa-exclamation-triangle mr-2"></i> You must enter the address type</div>');
                            errors = 'yes';
                        }
                    }

                    field_data['radio_value'] = null;
                    if (type == 'radio') {
                        if ($(this).find('.field-data-radio-value').val() != '') {
                            field_data['radio_value'] = $(this).find('.field-data-radio-value').val();
                        }
                        if (field_data['radio_value'] == null) {
                            $(this).addClass('field-error').find('.field-error-div').append('<div class="field-error-item"><i class="fal fa-exclamation-triangle mr-2"></i> You must enter the value</div>');
                            errors = 'yes';
                        }
                    }


                    data.push(field_data);

                });

            } else {

                var field_data = {};
                field_data['file_id'] = $('#file_id').val();

                data.push(field_data);

            }

            if (errors == 'no') {
                $.ajax({
                    type: 'POST',
                    url: '/save_fields',
                    data: { data: JSON.stringify(data) },
                    success: function (response) {
                        $('#modal_success').modal().find('.modal-body').html('Fields Successfully Saved');
                    }
                });
            } else {
                $('#modal_danger').modal().find('.modal-body').html('All Fields Must Be Completed');
            }

        }
        /*
        $('#zoom_control').change(function () {
            var z = $(this).val();
            $('#file_viewer').css({ 'width': z + '%' });

        });
        */
        // highlight active thumb when clicked and scroll into view
        $('.file-view-thumb-container').click(function () {
            $('.file-view-thumb-container').removeClass('active');
            $(this).addClass('active');
            var id = $(this).data('id');
            $('#active_page').val(id);
            document.getElementById('page_' + id).scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'nearest' });
        });

        // change highlighted thumb on scroll when doc is over half way in view
        $('#file_viewer').scroll(function () {
            // Stop the loop once the first is found
            var cont = 'yes';
            var id = '';
            $('.file-view-page-container').each(function () {
                if (cont == 'yes') {
                    id = $(this).data('id');
                    // see if scrolled past half way
                    var center = $(window).height() / 2;
                    var start = $(this).offset().top;
                    var end = start + $(this).height();
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