export function form_elements() {

    let form_elements = ['form-input', 'form-textarea', 'form-select', 'form-checkbox', 'form-radio'];

    form_elements.map(function (form_type) {

        let form_element = $('.' + form_type);

        // add container and label
        form_element.each(function () {

            let element = $(this);
            // check if form-ele already applied
            if (!element.parent().hasClass('form-ele')) {

                // wrap element with form-ele div and add label
                element.wrap('<div class="form-ele"></div>').parent('.form-ele').append('<label class="' + form_type + '-label">' + element.data('label') + '</label>');
                // get wrapper to append to
                let wrapper = element.parent();

                if (form_type == 'form-select') {
                    // hide select element
                    element.hide();

                    let select_html = ' \
                    <div class=form-select-wrapper"> \
                        <input type="text" class="form-select-value-input" readonly> \
                        <div class="form-select-dropdown z-depth-2"> \
                            <div class="form-select-search-div d-flex justify-content-center"> \
                                <input type="text" class="form-select-search-input" placeholder="Search"> \
                            </div> \
                            <div class="form-select-options-div"> \
                                <ul class="form-select-ul"></ul> \
                            </div> \
                        </div > \
                    </div> \
                    ';

                    wrapper.append(select_html);

                    let input = wrapper.find('.form-select-value-input');

                    element.children('option').map(function (index, option) {
                        option = $(option);
                        let value = option.prop('value');
                        let selected = (option.prop('selected') == true) ? 'active' : '';

                        if (value != '') {
                            let text = option.text();
                            let li = '<li class="form-select-li ' + selected + '" data-index="' + index + '" data-value="' + value + '">' + text + '</li>';
                            wrapper.find('.form-select-ul').append(li);
                        }
                        // add value to input if selected
                        if (selected) {
                            setTimeout(function() {
                                input.val(value);
                            }, 200);
                        }
                    });

                    if (element.hasClass('form-select-searchable')) {
                        wrapper.find('.form-select-search-div').show();
                        let search_input = wrapper.find('.form-select-search-input');
                        search_input.keyup(function () {
                            let search_value = new RegExp($(this).val(), 'i');
                            $('.form-select-li').hide().removeClass('matched');
                            wrapper.find('.form-select-li').each(function () {
                                if ($(this).text().match(search_value)) {
                                    $(this).show().addClass('matched');
                                }
                            });
                            $('.matched').first().addClass('active');
                        });
                        // select on tab
                        $(wrapper).on('keydown', search_input, function(e) {
                            var keyCode = e.keyCode || e.which;
                            if (keyCode == 9) {
                                e.preventDefault();
                                input.val(wrapper.find('.form-select-li.active').text());
                                $('.form-select-dropdown').fadeOut();
                                element.find('option').attr('selected', false);
                                element.find('option').eq(index).attr('selected', true);
                                element.trigger('change');
                            }
                        });
                    }

                    // check if multiple
                    let multiple = (element.attr('multiple') == true) ? true : false;

                    if (multiple) {

                    } else {
                        $('.form-select-li').click(function (e) {
                            e.stopImmediatePropagation();
                            let li = $(this);
                            option_selected(li, element, input);



                        });
                    }

                    // show dropdown on focus
                    $('.form-select-value-input').focus(function () {
                        $(this).next('.form-select-dropdown').fadeIn();
                    });

                }

                // set labels to active or not
                activate_label(element);
                element.change(function () {
                    activate_label(element);
                });

            }

        });

        // focus on el when label is clicked
        $('.' + form_type + '-label').click(function () {
            $(this).prev('.' + form_type).focus();
        });

    });

    // hide on blur
    $(document).mousedown(function (e) {
        if (!$(e.target).is('.form-ele *')) {
            reset_select();
        }
    });

}

function reset_select() {
    $('.form-select-dropdown').fadeOut();
    $('.form-select-li').removeClass('active');
    $('.form-select-li').show().removeClass('matched');
    $('.form-select-search-input').val('');
    $('.form-select').each(function () {
        $(this).val($(this).data('default-value'));
        $(this).parent('div').find('.form-select-value-input').val($(this).data('default-value'));
    });

}

function option_selected(li, element, input) {
    let value = li.data('value');
    let index = li.data('index');
    // set input value
    input.val(value);
    // remove active from all li and add to selected
    $('.form-select-li').removeClass('active');
    li.addClass('active');
    // hide select options and add
    $('.form-select-dropdown').fadeOut();
    // update select element
    element.find('option').attr('selected', false);
    element.find('option').eq(index).attr('selected', true);
    element.trigger('change');
}

function activate_label(element) {
    if (element.val() != '') {
        element.next('label').addClass('active');
    } else {
        element.next('label').removeClass('active');
    }
}
