if (document.URL.match(/transaction_details/)) {

    $(document).ready(function() {

        $(document).on('click', '.show-view-add-button', function() {

            if(!$(this).closest('.popout-action').hasClass('bg-blue-light')) {

                let popout = $(this).closest('.popout-row').find('.popout');
                /*
                flipOutX flipInX
                slideInLeft slideOutRight
                */
                let anime_in = 'flipInX';
                let anime_out = 'flipOutX';
                $('.popout-action, .popout').removeClass('bg-blue-light '+ anime_in+' '+anime_out);
                $(this).closest('.popout-action').addClass('bg-blue-light');

                $('.popout').not(popout).addClass(anime_out).hide();

                popout.addClass('bg-blue-light '+ anime_in).fadeIn();
                if($(window).width() > 992) {
                    $('.popout.middle').css({ top: '-'+ ((popout.height() / 2) - 30) + 'px' });
                }

                setTimeout(function() {
                    //$('.popout').removeClass('');
                }, 100);

            }

        });

    });

}
