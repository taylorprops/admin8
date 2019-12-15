export function form_elements() {
    /*
    Element classes
    input | .form-input
    select | .form-select
    textarea | .form-textarea
    checkbox | .form-checkbox
    radio | .form-radio
    file input | .form-input-file

    Element options
    all
        data-label | adds label
        disabled | functions as expected
    select
        .form-select-no-search | removes search option
        .form-select-value-cancel | removes cancel option

        multiple select uses mdbootstrap checkboxes

    */

    const form_elements = ['form-input', 'form-textarea', 'form-select', 'form-checkbox', 'form-radio', 'form-input-file'];

    form_elements.map(function (form_type) {

        const form_element = $('.' + form_type);

        // add container and label
        form_element.each(function () {

            const element = $(this);

            const multiple = (element.attr('multiple') == 'multiple' || element.attr('multiple') == true) ? true : false;

            // check if form-ele already applied
            if (!element.parent().hasClass('form-ele')) {

                let select_input_id = Date.now();
                let id = 'input_' + select_input_id;

                if (element.attr('id') != undefined) {
                    id = element.attr('id');
                } else {
                    element.attr('id', id);
                }

                let active_label = '';
                if (element.val() != '') {
                    active_label = 'active';
                }


                // wrap element with form-ele
                //add labels on all except: select and file input
                //select label is added in select_html
                //file label is added in file_html
                let label = $(this).data('label');
                if (form_type == 'form-input' || form_type == 'form-textarea') {

                    element.wrap('<div class="form-ele"></div>').parent('.form-ele').append('<label for="' + id + '" class="' + form_type + '-label ' + active_label + '">' + element.data('label') + '</label>');

                    // hide any open select dropdowns
                    element.click(function () {
                        $('.form-select-dropdown').fadeOut();
                        $('.form-select-search-input').val('').trigger('change');
                    });

                } else if (form_type == 'form-select') {

                    element.wrap('<div class="form-ele"></div>');

                } else if (form_type == 'form-input-file') {

                    let clone = element.wrap('<div></div>').parent().html();
                    element.unwrap();

                    let file_html = ' \
                    <div class="form-ele md-form my-0 mt-2"> \
                        <div class="file-field"> \
                            <a class="btn-floating blue-gradient mt-0 float-left"> \
                                <i class="fad fa-upload"></i> \
                                '+clone+' \
                            </a> \
                            <div class="file-path-wrapper"> \
                            <input class="file-path" type="text" placeholder="'+label+'"> \
                            </div> \
                        </div> \
                    </div> \
                    ';
                    element.parent().html(file_html);
                    element.remove();

                } else if (form_type == 'form-checkbox') {

                    element.addClass('form-check-input');
                    element.wrap('<div class="form-ele form-check"></div>').parent('.form-ele').append('<label for="' + id + '" class="form-check-label ' + form_type + '-label ' + active_label + ' w-100">' + element.data('label') + '</label>');


                }

                // get wrapper to append to
                let wrapper = element.parent();

                if (form_type == 'form-select') {
                    // hide select element
                    element.hide();



                    let disabled = '';
                    if (element.prop('disabled') == true) {
                        disabled = 'disabled';
                    }

                    // add delete if clearable
                    let clear_value = '';
                    if (disabled == '') {
                        clear_value = '<div class="form-select-value-cancel"><i class="fal fa-times"></i></div>';
                    }

                    let label = '';
                    if (element.data('label') != '') {
                        label = element.data('label');
                    }
                    let select_html = ' \
                    <div class=form-select-wrapper"> \
                        ' + clear_value + ' \
                        <label class="' + form_type + '-label" for="select_value_' + select_input_id + '">' + label + '</label> \
                        <input type="text" class="form-select-value-input '+disabled+'" id="select_value_'+ select_input_id + '" readonly '+disabled+'> \
                        <div class="form-select-dropdown z-depth-1"> \
                            <div class="form-select-search-div"> \
                                <div class="w-100 d-flex justify-content-center"> \
                                    <input type="text" class="form-select-search-input" placeholder="Search"> \
                                </div> \
                            </div> \
                            <div class="form-select-options-div" data-simplebar data-simplebar-auto-hide="false"> \
                                <ul class="form-select-ul"></ul> \
                            </div> \
                        </div > \
                    </div> \
                    ';

                    wrapper.append(select_html);

                    let input = wrapper.find('.form-select-value-input');

                    // add dropdown html
                    element.children('option').map(function (index, option) {
                        option = $(option);
                        let value = option.prop('value');
                        let selected = '';
                        if (option.prop('selected')) {
                            selected = (option.prop('selected') == 'selected' || option.prop('selected') == true) ? 'active' : '';
                        }

                        if (value != '') {

                            let text = option.text();
                            let li_html = text;
                            let multiple_li_class = '';

                            if (multiple) {
                                let checked = (option.prop('selected') == 'checked' || option.prop('selected') == true) ? 'checked' : '';
                                li_html = ' \
                                <div class="form-check"> \
                                    <input type="checkbox" class="form-check-input" id="check_'+ select_input_id + '_' + index + '" data-index="' + index + '" data-value="' + value + '" data-text="' + text + '" ' + checked + '> \
                                    <label class="form-check-label w-100" for="check_'+ select_input_id + '_' + index + '">' + text + '</label> \
                                </div > \
                                ';
                                multiple_li_class = 'form-check-input-multiple';
                            }

                            let li = '<li class="form-select-li ' + selected + ' ' + multiple_li_class + '" data-index="' + index + '" data-value="' + value + '" data-text="' + text + '">' + li_html + '</li>';
                            wrapper.find('.form-select-ul').append(li);

                        }


                        if (element.val() != '') {
                            setTimeout(function () {
                                if (!multiple) {
                                    // add value to input if selected
                                    input.val(value).trigger('change');
                                }
                                // show cancel option
                                wrapper.find('.form-select-value-cancel').show();
                            }, 100);
                        }

                    });

                    // add save button to exit out of multiple select
                    if (multiple) {
                        wrapper.find('.form-select-dropdown').append('<div class="w-100 form-select-save-div"><div class="d-flex d-flex justify-content-center p-0"><a href="javascript: void(0)" class="form-select-multiple-save btn btn-success btn-sm">Save</a></div></div>');
                        $('.form-select-multiple-save').click(function () {
                            $('.form-select-dropdown').fadeOut();
                            $('.form-select-search-input').val('').trigger('change');
                        });

                        set_multiple_select_value(wrapper, input);
                    }



                    // remove cancel option if class form-select-no-cancel is set
                    if (!element.hasClass('form-select-no-cancel')) {
                        wrapper.find('.form-select-value-cancel').click(function () {
                            element.val('').trigger('change');
                            wrapper.find('.form-select-value-input').val('').trigger('change');
                            wrapper.find('li').removeClass('active');
                            wrapper.find('.form-select-value-cancel').hide();
                            wrapper.find('.form-check-input').prop('checked', false);
                            reset_select();
                        });
                    }

                    // remove search option if class form-select-no-search is set
                    if (element.hasClass('form-select-no-search')) {

                        wrapper.find('.form-select-search-div').hide();

                    } else {

                        let search_input = wrapper.find('.form-select-search-input');
                        // search for matching li's if tab not pressed
                        search_input.keydown(function (e) {
                            var keyCode = e.keyCode || e.which;
                            if (keyCode != 9) {
                                search_input.keyup(function () {
                                    let search_value = new RegExp($(this).val(), 'i');
                                    if (search_input.val() != '') {
                                        $('.form-select-li').hide().removeClass('matched form-select-matched-option');
                                        wrapper.find('.form-select-li').each(function () {
                                            if ($(this).text().match(search_value)) {
                                                $(this).show().addClass('matched');
                                            }
                                        });
                                        $('.matched').first().addClass('form-select-matched-option');

                                    } else {
                                        $('.form-select-li').show().removeClass('matched form-select-matched-option');
                                    }
                                });
                            } else {
                                // if tab pressed
                                e.preventDefault();

                                if (!multiple) {

                                    input.val(wrapper.find('.form-select-matched-option').text()).trigger('change');
                                    reset_select();
                                    element.find('option').attr('selected', false);
                                    element.find('option').eq($('.form-select-matched-option').data('index')).attr('selected', true);
                                    element.trigger('change');
                                    // remove active from all li and add to selected
                                    wrapper.find('.form-select-li').removeClass('active');
                                    $('.form-select-matched-option').addClass('active');

                                } else {

                                    $('.form-select-matched-option input').prop('checked', true);
                                    element.find('option').eq($('.form-select-matched-option').data('index')).attr('selected', true);
                                    $('.form-select-matched-option').addClass('active');
                                    set_multiple_select_value(wrapper, input);
                                    $('.form-select-dropdown').fadeOut();
                                    $('.form-select-search-input').val('').trigger('change');

                                }

                                if (!element.hasClass('form-select-no-cancel')) {
                                    input.siblings('.form-select-value-cancel').show();
                                }
                            }

                        });

                    }

                    // when a checkbox in a multiple select is changed
                    wrapper.find('.form-check-input').change(function (e) {

                        let li = $(this);
                        let input = li.closest('.form-ele').find('.form-select-value-input');
                        let form_ele = li.closest('.form-ele').find('.form-select');
                        let selected_checks = [];

                        wrapper.find('.form-select-li').removeClass('active');
                        form_ele.find('option').attr('selected', false);
                        wrapper.find('input[type="checkbox"]:checked').each(function () {

                            let checked = $(this);
                            let index = checked.data('index');
                            selected_checks.push(checked.data('value'));
                            checked.closest('li').addClass('active');
                            // to show selected first use below
                            // $(this).closest('li').addClass('active').prependTo('.form-select-ul');

                            // set select element value
                            form_ele.find('option').eq(index).attr('selected', true).trigger('change');

                        });

                        form_ele.trigger('change');

                        if (form_ele.val() == '') {
                            form_ele.closest('.form-ele').find('.form-select-value-cancel').hide();
                        }

                        // shorten input value if too long
                        set_multiple_select_value(wrapper, input);



                    });
                    // when a single li is clicked
                    wrapper.find('.form-select-li').click(function () {

                        if (!$(this).hasClass('form-check-input-multiple')) {
                            let li = $(this);
                            let value = li.data('value');
                            let text = li.data('text');
                            let input = li.closest('.form-ele').find('.form-select-value-input');
                            let dropdown = li.closest('.form-ele').find('.form-select-dropdown');
                            let element = li.closest('.form-ele').find('.form-select');

                            $('.form-select-matched-option').removeClass('form-select-matched-option');
                            // set input value
                            input.val(text).trigger('change');

                            // remove active from all li and add to selected
                            dropdown.find('.form-select-li').removeClass('active');
                            li.addClass('active');
                            // hide select options and add
                            $('.form-select-dropdown').fadeOut();
                            // update select element
                            element.val(value);
                            element.trigger('change');
                        }
                        if (!element.hasClass('form-select-no-cancel')) {
                            input.siblings('.form-select-value-cancel').show();
                        }

                    });

                } // end if (form_type == 'form-select') {

            } // end if (!element.parent().hasClass('form-ele')) {


        }); // end form_element.each(function () {



    }); // end form_elements.map(function (form_type) {


    // show dropdown on focus
    $('.form-select-value-input').unbind('focus').bind('focus', function (e) {
        // prevent labels from becoming active until after a selection is made
        let input = $(this);
        reset_select();
        let select = input.closest('.form-ele').find('select');
        // show dropdown
        input.next('.form-select-dropdown').fadeIn();
        // focus on search input is searchable
        if (!select.hasClass('form-select-no-search')) {
            setTimeout(function() {
                input.next('.form-select-dropdown').find('.form-select-search-input').focus();
                input.prev('label').addClass('active');
            }, 100);
        }

    });


    // hide select dropdown on blur
    $(document).on('mousedown', function (e) {
        if (!$(e.target).is('.form-ele *')) {
            reset_select();
        }
    });

    // add tabindexes
    let c = 0;
    $('.form-input, .form-textarea, .form-select, .form-checkbox, .form-radio').each(function () {
        c += 1;
        if ($(this).hasClass('form-select')) {
            let input = $(this).next('div').find('.form-select-value-input');
            input.attr('tabindex', c);
        } else {
            $(this).attr('tabindex', c);
        }
    });





}

function set_multiple_select_value(wrapper, input) {
    // add value to multiple select
    let selected_checks = [];
    wrapper.find('input[type="checkbox"]:checked').each(function () {
        let checked = $(this);
        selected_checks.push(checked.data('value'));
    });

    let value = '';
    if (selected_checks.length > 0) {
        value = selected_checks.join(', ');
    }
    // shorten value if bigger than input
    let max_chars = Math.round(parseInt(input.width()) * .12);
    if (value.length > max_chars) {
        value = value.substring(0, max_chars) +'...';
    }
    input.val(value).trigger('change');

}

export function select_refresh(select) {
    let wrapper = select.closest('.form-ele');
    let input = wrapper.find('.form-select-value-input');
    input.val(select.val()).trigger('change');
    wrapper.find('.form-select-li').removeClass('active').each(function () {
        if ($(this).text() == select.val()) {
            $(this).addClass('active');
        }
    });
}


export function reset_select() {
    $('.form-select-dropdown').fadeOut();
    $('.form-select-search-input').val('').trigger('change');
    $('.form-select-li').removeClass('matched').show();
    $('.form-select-value-input').trigger('change');
}
