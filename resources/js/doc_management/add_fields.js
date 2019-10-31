$(document).ready(function() {


    // Show active field
    $('.field-wrapper').click(function () {
        $('.field-wrapper').removeClass('active');
        $(this).addClass('active');
        $('#active_field').val($(this).data('type'));
    });


    if ($('.text-line-div').length > 0) {

        var group_id = '';
        $('.text-line-div').each(function () {
            // get bounding box coordinates
            var rect = this.getBoundingClientRect();
            var container = $(this).closest('.field-container');

            var h = $(this).css('height').replace(/px/, '');
            var w = $(this).css('width').replace(/px/, '');
            var x = $(this).css('left').replace(/px/, '');
            var y = $(this).css('top').replace(/px/, '');
            // convert from % to px
            $(this).css('height', h+'px');
            $(this).css('width', w+'px');
            $(this).css('top', y+'px');
            $(this).css('left', x+'px');

            set_field_textline_options($(this), h, w, x, y, $(this).data('fieldid'), rect, container);
            set_hwxy($(this), h, w, x, y, $(this).data('groupid'), 'textline');
            group_id = $(this).data('groupid');

            // clear other field when changing name
            $(this).find('.field-data-name').each(function() {
                $(this).change(function() {
                    $(this).closest('.form-div').find('.field-data-name').not($(this)).val('');
                });
            });
        });

        $('.focused').hide();


        $('.group_' + group_id).find('.field-textline-addline-container').hide();
        $('.group_' + group_id).find('.field-textline-addline-container').last().show();

        field_status();

    }

    // on page double click add field
    $('#file_viewer').on('dblclick', '.file-view-page-container.active', function (e) { // changed from .field-container

        // disable zoom
        //$('.zoom-container').hide();
        // get container so easier to find elements
        var container = $(e.target.parentNode);

        var t = $('#active_field').val();
        // only if a field has been selected
        if (t != '') {

            // get bounding box coordinates
            var rect = e.target.getBoundingClientRect();
            // get target coordinates
            var x = parseInt(Math.round(e.clientX - rect.left));
            var y = parseInt(Math.round(e.clientY - rect.top));
            // remove excess from field panel at top
            y = (y - parseInt($('#field_textline_height').val()));

            // set w and h for next element created - always the same as last element added
            var w = parseInt($('#field_textline_width').val());
            var h = parseInt($('#field_textline_height').val());

            // create unique id for field
            var id = Date.now();


            if (t == 'textline') {

                // hide all handles and buttons
                $('.focused').hide();

                //create field and attach to container
                var field = field_text(h, w, x, y, id, id, $('#active_page').val());
                // append new field
                $(container).append(field);

                field_status();

                // clear other field when changing name
                $('.field-data-name').each(function() {
                    $(this).change(function() {
                        $(this).closest('.form-div').find('.field-data-name').not($(this)).val('');
                    });
                });

                set_hwxy($('#field_' + id), h, w, x, y, id, 'textline');

                keep_in_view($('#field_' + id), id, h, w, x, y, container, 'textline');

                set_field_textline_options($('#field_' + id), h, w, x, y, id, rect, container);

                setTimeout(function () {

                    $('#field_textline_groupid').val(id);
                    $('.field-div').removeClass('active');
                    $('#field_' + id).addClass('active');

                }, 100);

            }

        }
    });

    ///////////////// textlines /////////////////////////
    // on page click hide all focused els
    $(document).on('click', '.field-container', function (e) {
        if(!$(e.target).is('.field-div *')){
            $('.focused, .field-popup').hide();
            $('#field_textline_groupid').val('');
            $('.field-div').removeClass('active');
            // reset name fields
            $('.form-div').each(function () {
                $(this).find('select, input').each(function () {
                    $(this).val($(this).data('defaultvalue'));
                });
            });
        }
    });

    function set_field_textline_options(ele, h, w, x, y, id, rect, container) {

        ele.click(function () {
            $('.focused').hide();
            $(this).find('.focused').show();
            $('.field-div').removeClass('active');
            $(this).addClass('active');
            set_hwxy($(this), $(this).height(), $(this).width(), $(this).position().left, $(this).position().top, $(this).data('groupid'), 'textline');
        })
        .resizable({
            containment: container, //$('.field-container'), // tried containment: $('#page_div_'+$('#active_field').val()), but not go
            handles: { 'ne': '.ui-resizable-ne', 'nw': '.ui-resizable-nw', 'se': '.ui-resizable-se', 'sw': '.ui-resizable-sw' },
            maxHeight: 50,
            minHeight: 15,
            stop: function (e, ui) {
                set_hwxy($(this), $(this).height(), $(this).width(), ui.position.left, ui.position.top, '', 'textline');
            }
        })
        .draggable({
            containment: container,
            handle: '.field-handle',
            cursor: 'grab',
            stop: function (e, ui) {
                set_hwxy($(this), $(this).height(), $(this).width(), ui.position.left, ui.position.top, '', 'textline');
            }
        });

        // hide all handles and buttons when another container is selected
        $('.field-container-textline').click(function (e) {
            $('.focused').hide();
            $('.field-div').removeClass('active');
        });
        // remove field
        $('.remove-field-textline').off('click').on('click', function () {
            var remove_groupid = $('.field-div.active').data('groupid');
            $('.field-div.active').remove();

            if (remove_groupid != '') {
                var group = $('.group_' + remove_groupid);
                // hide add line option for all but last
                group.find('.field-textline-addline-container').hide();
                group.find('.field-textline-addline-container').last().show();
                // see if other divs in a group and if just one remove group class
                if (group.length == 1) {
                    group.removeClass('group').addClass('standard');
                }
            }
        });
        // add lines
        $('.field-textline-addline').off('click').on('click', function () {

            // add line confirm div
            var add_lines = $(this).next('.add-new-line-div');
            add_lines.toggle();
            $('.field-close-newline').click(function () {
                add_lines.hide();
            });
            // after confirming
            $('.add-new-line').off('click').on('click', function () {
                // assign group id for original field
                var group_id = $(this).data('groupid');

                var common_name = $('.group_' + group_id).data('commonname');
                var custom_name = $('.group_' + group_id).data('customname');

                $('.group_' + group_id).removeClass('standard').addClass('group').addClass('group_' + group_id);


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
                var field = field_text(h,w,x,y,id,group_id, $('#active_page').val());
                // append new field
                field_div.closest('.field-container').append(field);

                var new_ele = $('#field_' + id);

                field_status();

                add_lines.toggle();
                setTimeout(function() {
                    $('.focused').fadeOut();
                    $('.field-div').removeClass('active');
                    new_ele.addClass('active').find('.focused').fadeIn();
                    var h = new_ele.height();
                    var w = new_ele.width();
                    var x = new_ele.position().left;
                    var y = new_ele.position().top;

                    if ((parseInt(y) + parseInt(h)) > parseInt(container.height())) {
                        new_ele.css({ border: '3px dotted #900' });
                        setTimeout(function() {
                            new_ele.parent('div').remove();
                        }, 1000);
                        return false;
                    }

                    set_hwxy(new_ele, h, w, x, y, '', 'textline');

                    // assign group id to new field
                    new_ele.data('groupid', group_id).removeClass('standard').addClass('group').addClass('group_' + group_id);

                    // move add line option to last line
                    $('.group_' + group_id).find('.field-textline-addline-container').hide();
                    $('.group_' + group_id).find('.field-textline-addline-container').last().show();

                    // clear other field when changing name
                    new_ele.find('.field-data-name').each(function() {
                        $(this).change(function() {
                            $(this).closest('.form-div').find('.field-data-name').not($(this)).val('');
                        });
                    });


                    setTimeout(function() {
                        // set values for field name
                        var new_form = new_ele.find('.form-div');
                        new_form.find('select').val(common_name).data('defaultvalue', common_name);
                        new_form.find('input').val(custom_name).data('defaultvalue', custom_name);
                    }, 1500);

                }, 100);

                keep_in_view(new_ele, id, h, w, x, y, container, 'textline');

                set_field_textline_options(new_ele, h, w, x, y, id, rect, container);
            });
        });
        // add properties
        $('.field-properties').off('click').on('click', function () {

            var edit_div = $(this).next('.edit-properties-div');

            var cw = container.width();
            var ch = container.height();

            x = edit_div.closest('.field-div').position().left;
            y = edit_div.closest('.field-div').position().top;

            if (x > (cw / 2)) {
                edit_div.css({ right: '-40px' });
            } else {
                edit_div.css({ left: '-40px' });
            }
            if (y > (ch / 2)) {
                edit_div.css({ top: '-85px' });
            } else {
                edit_div.css({ top: '-10px' });
            }

            var groupid = $('#field_textline_groupid').val();
            if ($('.group_' + groupid).length > 1) { // test
                edit_div.find('.grouped-header').remove();
                edit_div.prepend('<div class="text-orange font-weight-bold grouped-header mb-2"><i class="fad fa-layer-group"></i> Grouped</div>');
            }

            edit_div.toggle();

            $('.save_field_properties_textline').off('click').on('click', function () {
                var group_id = $(this).data('groupid');
                var form = $(this).closest('.form-div');
                var common_name = form.find('select').val();
                var custom_name = form.find('input').val();
                $('.group_' + group_id).each(function () {
                    $(this).data('commonname', common_name).data('customname', custom_name);
                    $(this).find('.form-div').each(function () {
                        $(this).find('select').val(common_name).data('defaultvalue', common_name);
                        $(this).find('input').val(custom_name).data('defaultvalue', custom_name);
                    });
                });
                edit_div.hide();
            });

            $('.field-close-properties-textline').off('click').on('click', function () {
                var form = $(this).closest('.form-div');
                form.find('select, input').each(function () {
                    $(this).val($(this).data('defaultvalue'));
                });
                edit_div.hide();
            });
        });

    }


    // get common names for select
    $.ajax({
        type: 'get',
        url: '/common_fields',
        dataType: "json",
        success: function (data) {
            var select_options = '';
            $.each(data, function () {
                $.each(this, function (k, v) {
                    select_options = select_options + '<option value="' + v + '">' + v + '</option>';
                });
            });
            $('#name_select_options').val(select_options);
        }
    });



    function field_text(h, w, x, y, id, group_id, page) {

        var properties_html = ' \
        <div class="form-div"> \
            <strong>Field Name</strong><br> \
            <div class="container"> \
                <div class="row"> \
                    <div class="col-6"> \
                        Common Field<br> \
                        <select class="custom-select field-data-name select_'+ id + '" data-fieldtype="common"> \
                            <option value=""></option> \
                            ' + $('#name_select_options').val() + ' \
                        </select> <br> \
                    </div> \
                    <div class="col-1"> \
                        <div class="small">Or</div> \
                    </div> \
                    <div class="col-5"> \
                        Add New Name<br> \
                        <input type="text" class="form-control field-data-name" data-fieldtype="new"> \
                    </div> \
                </div> \
            </div> \
            <div class="d-flex justify-content-around mt-3"> \
                <a href="javascript: void(0);" class="btn btn-success btn-sm shadow save_field_properties_textline" data-groupid="'+group_id+'">Save</a> \
                <a href="javascript:void(0);" class="btn btn-danger btn-sm field-close-properties-textline">Cancel</a> \
            </div> \
        </div> \ ';

        return ' \
            <div class="field-div text-line-div standard rounded active group_'+ group_id + '" style="position: absolute; top: ' + y + 'px; left: ' + x + 'px; height: ' + h + 'px; width: ' + w + 'px;" id="field_' + id + '" data-fieldid="' + id + '" data-groupid="' + group_id + '" data-page="' + page +'" data-type="textline"> \
                <div class="field-status-div d-flex justify-content-between w-100"> \
                    <div class="field-status-name-div"></div> \
                    <div class="field-status-group-div"></div> \
                </div> \
                <div class="field-options-holder focused shadow container text-center"> \
                    <div class="row m-0 p-0"> \
                        <div class="col-2 p-0"> \
                            <div class="field-handle"><i class="fal fa-ellipsis-v-alt fa-lg text-primary"></i></div> \
                        </div> \
                        <div class="col-8 p-0"> \
                            <div class="d-flex justify-content-around"> \
                                <div class="field-textline-addline-container"> \
                                    <div class="field-textline-addline mr-3"> \
                                        <i class="fas fa-horizontal-rule fa-lg text-primary"></i> \
                                        <i class="fal fa-plus fa-xs ml-1 text-primary add-line-plus"></i> \
                                    </div> \
                                    <div class="add-new-line-div shadow-lg field-popup"> \
                                        <div class="add-new-line-content"> \
                                            Add Line To Group? \
                                            <div class="d-flex justify-content-around"> \
                                                <a href="javascript: void(0);" class="btn btn-success btn-sm add-new-line shadow" data-groupid="'+group_id+'">Confirm</a> \
                                                <a href="javascript:void(0);" class="btn btn-danger btn-sm field-close-newline">Cancel</a> \
                                            </div> \
                                        </div> \
                                    </div> \
                                </div> \
                                <div> \
                                    <div class="field-properties"> \
                                        <i class="fal fa-info-circle fa-lg text-primary"></i> \
                                    </div> \
                                    <div class="edit-properties-div shadow field-popup">'+properties_html+'</div> \
                                </div> \
                            </div> \
                        </div> \
                        <div class="col-2 p-0"> \
                            <div class="remove-field-textline"><i class="fal fa-times-circle fa-lg text-danger"></i></div> \
                        </div> \
                    </div> \
                </div> \
                <div class="ui-resizable-handle ui-resizable-ne focused"></div> \
                <div class="ui-resizable-handle ui-resizable-se focused"></div> \
                <div class="ui-resizable-handle ui-resizable-nw focused"></div> \
                <div class="ui-resizable-handle ui-resizable-sw focused"></div> \
                <div class="text-line"></div> \
            </div> \
        '
    }
    ///////////////// end textlines /////////////////////////


    function filter_array(value, index, self) {
        return self.indexOf(value) === index;
    }
    function field_status() {
        var group_ids = [];
        $('.field-div').each(function () {
            group_ids.push($(this).data('groupid'));
        });
        group_ids = group_ids.filter(filter_array);
        for (i = 0; i < group_ids.length; i++) {
            grouped = false;
            if ($('.field-div[data-groupid="' + group_ids[i] + '"]').length > 1) {
                grouped = true;
            }
            if (grouped == true) {
                $('.field-div[data-groupid="' + group_ids[i] + '"]').each(function () {
                    $('.field-div[data-groupid="' + group_ids[i] + '"]').last().find('.field-status-group-div').html('<i class="fal fa-layer-group"></i>');
                });
            }
            $('.field-div[data-groupid="' + group_ids[i] + '"]').last().find('.field-status-name-div').html('field name');
        }

        $('.field-div').each(function () {
            $(this).find('.field-data-name').each(function () {
                if ($(this).val() != '') {
                    $(this).removeClass('field-data-name-not-named');
                } else {
                    $(this).addClass('field-data-name-not-named');
                }
            });
        });

    }


    function set_hwxy(ele, h, w, x, y, groupid, type) {

        var hp, wp, xp, yp;
        var container = ele.closest('.field-container');

        if (h) {
            hp = (100 * parseFloat(h / parseFloat(container.height()))).toFixed(4);
            wp = (100 * parseFloat(w / parseFloat(container.width()))).toFixed(4);
            ele.data('h', h);
            ele.data('w', w);
            ele.data('hp', hp);
            ele.data('wp', wp);
            $('#field_'+type+'_height').val(h);
            $('#field_' + type + '_width').val(w);
            $('#field_'+type+'_heightp').val(hp);
            $('#field_' + type + '_widthp').val(wp);
        }
        if (x) {
            xp = (100 * parseFloat(x / parseFloat(container.width()))).toFixed(4);
            yp = (100 * parseFloat(y / parseFloat(container.height()))).toFixed(4);
            ele.data('x', x);
            ele.data('y', y);
            ele.data('xp', xp);
            ele.data('yp', yp);
            $('#field_'+type+'_x').val(x);
            $('#field_' + type + '_y').val(y);
            $('#field_'+type+'_xp').val(xp);
            $('#field_'+type+'_yp').val(yp);
        }
        if (groupid) {
            $('#field_'+type+'_groupid').val(groupid);
        }
        ele.data('page', $('#active_page').val());

    }

    function keep_in_view(ele, id, h, w, x, y, container, type) {
        // adjust fields if placed out of bounds
        var x_orig = x;
        var y_orig = y;

        if (x < 15) {
            ele.animate({ left: '15px' });
        }
        if ((x + w) > container.width()) {
            var cw = container.width();
            var pos = cw - w - 15;
            ele.animate({ left: pos + 'px' });
        }
        if (y < 40) {
            ele.animate({ top: '40px' });
        }

        setTimeout(function() {
            var h = ele.css('height').replace('px', '');
            var w = ele.css('width').replace('px', '');
            var x = ele.css('left').replace('px', '');
            var y = ele.css('top').replace('px', '');
            var groupid = ele.data('groupid');

            set_hwxy(ele, h, w, x, y, groupid, type);
        }, 1500);

    }




    /////////////////// Save data ///////////////////////
    $('#save_fields').click(save);
    function save() {
        var data = [];
        $('.field-div').each(function () {
            field_data = {};
            field_data['file_id'] = $('#file_id').val();
            field_data['field_id'] = $(this).data('fieldid');
            field_data['group_id'] = $(this).data('groupid');
            field_data['page'] = $(this).data('page');
            field_data['field_type'] = $(this).data('type');
            field_data['left'] = $(this).data('x');
            field_data['top'] = $(this).data('y');
            field_data['height'] = $(this).data('h');
            field_data['width'] = $(this).data('w');
            field_data['left_perc'] = $(this).data('xp');
            field_data['top_perc'] = $(this).data('yp');
            field_data['height_perc'] = $(this).data('hp');
            field_data['width_perc'] = $(this).data('wp');
            field_data['field_name'] = null;
            field_data['field_name_type'] = null;
            $(this).find('.field-data-name').each(function () {
                if ($(this).val() != '') {
                    var field_name = $(this).val();
                    var field_type = $(this).data('fieldtype');
                    field_data['field_name'] = field_name;
                    field_data['field_name_type'] = field_type;
                }
            });

            data.push(field_data);

        });


        $.ajax({
            type: 'POST',
            url: '/save_fields',
            data: { data: JSON.stringify(data) },
            success: function(response) {

            }
        });

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
