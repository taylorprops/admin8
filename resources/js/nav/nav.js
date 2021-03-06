(function ($) {
    var defaults = {
        sm: 540,
        md: 720,
        lg: 960,
        xl: 1140,
        navbar_expand: 'lg',
        animation: true,
        animateIn: 'show',
    };
    $.fn.bootnavbar = function (options) {

        var screen_width = $(document).width();
        settings = $.extend(defaults, options);

        if (screen_width >= settings.lg) {

            $(this).find('.dropdown').each(function () {
                $(this).on('mouseenter', function() {
                    $(this).addClass('show');
                    $(this).find('.dropdown-menu').first().addClass('show');
                    if (settings.animation) {
                        $(this).find('.dropdown-menu').first().addClass('animate__animated animate__' + settings.animateIn);
                    }
                });
                $(this).on('mouseleave', function() {
                    $(this).removeClass('show');
                    $(this).find('.dropdown-menu').first().removeClass('show');
                });
            });

        }

        /* $('.dropdown-input').on('click', function() {
            console.log('clicked');
            $(this).addClass('show');
            $(this).find('.dropdown-menu').first().addClass('show');
            if (settings.animation) {
                $(this).find('.dropdown-menu').first().addClass('animate__animated animate__' + settings.animateIn);
            }
        }); */

        $('.dropdown-menu a.dropdown-toggle').on('click', function (e) {
            /* if (!$(this).next().hasClass('show')) {
                $(this).parents('.dropdown-menu').first().find('.show').removeClass('show');
            }
            var $subMenu = $(this).next('.dropdown-menu');
            $subMenu.toggleClass('show');

            $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function (e) {
                $('.dropdown-submenu .show').removeClass('show');
            }); */

            return false;
        });
    };
})(jQuery);
