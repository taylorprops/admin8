// Show active field
$('.field-wrapper').click(function () {
    $('.field-wrapper').removeClass('active');
    $(this).addClass('active');
    $('#active_field').val($(this).data('type'));
});

// on page double click add field
$(document).on('dblclick', '.file-view-image', function (e) {
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
        y = (y - parseInt($('#field_textline_height').val())) + 3;

        // set w and h for next element created - always the same as last element added
        var w = parseInt($('#field_textline_width').val());
        var h = parseInt($('#field_textline_height').val());

        // create unique id for field
        var id = Date.now();


        if (t == 'textline') {

            // hide all handles and buttons
            $('.focused').hide();

            //create field and attach to container
            var field = field_text(h,w,x,y,id);
            // append new field
            $(container).append(field);

            set_hwxy(h, w, x, y, id, 'textline');

            keep_in_view(id, h, w, x, y, container);

            set_field_textline_options($('#field_' + id), h, w, x, y, id, rect, container);

            setTimeout(function () {

                $('#field_textline_groupid').val(id);
                $('#field_' + id).data('groupid', id);

                $('.text-line-div').removeClass('active');
                $('#field_' + id).addClass('active');

            }, 100);

        }


    }
});


///////////////// textlines /////////////////////////
// on page click hide all focused els
$(document).on('click', '.file-view-image', function (e) {
    if(!$(e.target).is('.text-line-div *')){
        $('.focused').hide();
        $('#field_textline_groupid').val('');
    }
});

function set_field_textline_options(ele, h, w, x, y, id, rect, container) {

    ele.click(function () {
        $('.focused').hide();
        $(this).find('.focused').show();
        $('.text-line-div').removeClass('active');
        $(this).addClass('active');
        set_hwxy($(this).css('height'), $(this).css('width'), $(this).css('left'), $(this).css('top'), $(this).data('groupid'), 'textline');
    })
    .resizable({
        containment: container, // tried containment: $('#page_div_'+$('#active_field').val()), but not go
        handles: { 'ne': '.ui-resizable-ne', 'nw': '.ui-resizable-nw', 'se': '.ui-resizable-se', 'sw': '.ui-resizable-sw' },
        maxHeight: 50,
    })
    .draggable({
        containment: container,
        handle: '.field-handle',
        cursor: 'grab',
        stop: function () {
            var x = parseInt(Math.round($(this).position().left));
            var y = parseInt(Math.round($(this).position().top));
            set_hwxy('', '', x, y, '', 'textline');
        }
    })
    .resize(function (e) {
        var h = $('#field_' + id).height();
        var w = $('#field_' + id).width();
        var x = parseInt(Math.round(e.clientX - rect.left));
        var y = parseInt(Math.round(e.clientY - rect.top));
        set_hwxy(h, w, x, y, '', 'textline');
    });

    // hide all handles and buttons when another container is selected
    $('.field-container-textline').click(function (e) {
        $('.focused').hide();
        $('.text-line-div').removeClass('active');
    });
    // remove field
    $('.remove-field-textline').off('click').on('click', function () {
        var remove_groupid = $('.text-line-div.active').data('groupid');
        $('.text-line-div.active').parent('div').remove();

        if (remove_groupid != '') {
            var group = $('.c_' + remove_groupid);
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
            var group_id = $('#field_textline_groupid').val();

            ele.data('groupid', group_id).removeClass('standard').addClass('group').addClass('c_' + group_id);
            $('.c_' + group_id).removeClass('standard').addClass('group').addClass('c_' + group_id);
            h = parseInt($('#field_textline_height').val());
            w = parseInt($('#field_textline_width').val());
            x = parseInt($('#field_textline_x').val());
            y = parseInt($('#field_textline_y').val());
            y = y + h + 10;

            $('.text-line-div').removeClass('active');
            // create new id for new field in group
            id = Date.now();
            var field = field_text(h,w,x,y,id);
            // append new field
            $(container).append(field);
            var new_ele = $('#field_' + id);
            add_lines.toggle();
            setTimeout(function() {
                $('.focused').fadeOut();
                $('.text-line-div').removeClass('active');
                new_ele.addClass('active').find('.focused').fadeIn();
                var h = new_ele.css('height').replace('px', '');
                var w = new_ele.css('width').replace('px', '');
                var x = new_ele.css('left').replace('px', '');
                var y = new_ele.css('top').replace('px', '');

                if ((parseInt(y) + parseInt(h)) > parseInt(container.height())) {
                    new_ele.css({ border: '3px dotted #900' });
                    setTimeout(function() {
                        new_ele.parent('div').remove();
                    }, 1000);
                    return false;
                }

                set_hwxy(h, w, x, y, '', 'textline');

                // assign group id to new field
                new_ele.data('groupid', group_id).removeClass('standard').addClass('group').addClass('c_' + group_id);

                $('.c_' + group_id).find('.field-textline-addline-container').hide();
                $('.c_' + group_id).find('.field-textline-addline-container').last().show();

            }, 100);

            keep_in_view(id, h, w, x, y, container);

            set_field_textline_options(new_ele, h, w, x, y, id, rect, container);
        });
    });
    // add properties
    $('.field-properties-textline').off('click').on('click', function () {
        var edit_html;
        var groupid = $('#field_textline_groupid').val();
        if (groupid != '') {
            edit_html = 'GroupId: ' + groupid + '<br>';
        }
        edit_html = edit_html + '<div class="my-2">Field ID<br><input type="text" class="field-textline-input" data-type="id" data-fieldid="' + id + '" data-groupid="' + groupid + '"></div> \
            <div class="d-flex justify-content-around mt-3"> \
                <a href="javascript: void(0);" class="btn btn-success btn-sm shadow" id="save_field_properties_textline">Save</a> \
                <a href="javascript:void(0);" class="btn btn-danger btn-sm field-close-properties-textline">Cancel</a> \
            </div> \ ';
        var edit_div = $('.edit-properties-div');
        edit_div.toggle().html(edit_html);

        $.get('/common_fields', function( data ) {
            console.log(data);
        });

        var cw = container.width();
        var ch = container.height();
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

        $('.field-close-properties-textline').click(function () {
            $('.edit-properties-div').hide();
        });
    });

}

function field_text(h, w, x, y, id) {
    return '<div class="field-set"> \
        <div class="text-line-div standard rounded active" style="position: absolute; top: ' + y + 'px; left: ' + x + 'px; height: ' + h + 'px; width: ' + w + 'px;" id="field_' + id + '"> \
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
                                <div class="add-new-line-div shadow-lg"> \
                                    <div class="add-new-line-content"> \
                                        Add Line To Group? \
                                        <div class="d-flex justify-content-around"> \
                                            <a href="javascript: void(0);" class="btn btn-success btn-sm add-new-line shadow">Confirm</a> \
                                            <a href="javascript:void(0);" class="btn btn-danger btn-sm field-close-newline">Cancel</a> \
                                        </div> \
                                    </div> \
                                </div> \
                            </div> \
                            <div> \
                                <div class="field-properties-textline"> \
                                    <i class="fal fa-info-circle fa-lg text-primary"></i> \
                                </div> \
                                <div class="edit-properties-div shadow"> \
                                </div> \
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
    </div>'
}
///////////////// end textlines /////////////////////////

function set_hwxy(h, w, x, y, groupid, type) {
    if (h) {
        $(this).data('h', h);
        $(this).data('w', w);
        $('#field_'+type+'_height').val(h);
        $('#field_'+type+'_width').val(w);
    }
    if (x) {
        $(this).data('x', x);
        $(this).data('y', y);
        $('#field_'+type+'_x').val(x);
        $('#field_'+type+'_y').val(y);
    }
    if (groupid) {
        $('#field_'+type+'_groupid').val(groupid);
    }
    $(this).data('page', $('#active_page').val());


    //var x_perc = ( 100 * parseFloat(x / parseFloat($(this).parent().width())) ) + "%" ;
        //var y_perc = (100 * parseFloat(y / parseFloat($(this).parent().height()))) + "%";

    //console.log('height = '+$(this).data('h'), 'width = '+$(this).data('w'), 'x = '+$(this).data('x'), 'y = '+$(this).data('y'));
}

function keep_in_view(id, h, w, x, y, container) {
    // adjust fields if placed out of bounds
    if (x < 15) {
        $('#field_' + id).animate({ left: '15px' });
    }
    if ((x + w) > container.width()) {
        var cw = container.width();
        var pos = cw - w - 15;
        $('#field_' + id).animate({ left: pos+'px' });
    }
    if (y < 40) {
        $('#field_' + id).animate({ top: '40px' });
    }
}

$('#zoom_control').change(function () {
    var z = $(this).val();
    $('#file_viewer').css({ 'width': z + '%' });

});
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



