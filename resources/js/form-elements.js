let form_types = ['form-input', 'form-textarea', 'form-select', 'form-checkbox', 'form-radio'];

form_types.map(function (form_type) {

    let form_element = $('.' + form_type);
    // add container and label
    form_element.each(function () {
        let element = $(this);
        element.wrap('<div class="form-ele">').parent('.form-ele').append('<label class="' + form_type + '-label">' + element.data('label') + '</label>');
        element.change(function () {
            // set labels to active or not
            if (element.val() != '') {
                element.next('label').addClass('active');
            } else {
                element.next('label').removeClass('active');
            }
        });
    });
    // focus on el when label is clicked
    $('.form-input-label, .form-textarea-label, .form-select-label').click(function () {
        $(this).prev('.' + form_type).focus();
    });

});