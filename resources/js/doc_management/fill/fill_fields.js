const numWords = require('num-words');

if (document.URL.match(/create\/fill_fields/)) {

    $(document).ready(function () {

        //var amountInWords = numWords(12345);
        //console.log(amountInWords);

        $('.field-div').not('.field-date').click(function () {
            $('.edit-properties-div').hide();
            $(this).find('.edit-properties-div').show();
        });

        // on page click hide all focused els
        $(document).on('click', '.field-container', function (e) {

            if (!$(e.target).is('.field-div')) {

                $('.edit-properties-div').hide();
                /*
                $('.field-div').removeClass('active');
                // reset name fields
                $('.form-div').each(function () {
                    $(this).find('select, input').each(function () {
                        $(this).val($(this).data('defaultvalue'));
                    });
                });
                */
            }
        });

        // highlight active thumb when clicked and scroll into view
        $('.file-view-thumb-container').click(function () {
            $('.file-view-thumb-container').removeClass('active');
            $(this).addClass('active');
            var id = $(this).data('id');
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