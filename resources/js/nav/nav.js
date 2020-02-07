$(document).ready(function() {
    $('.nav-toggle').off('click').on('click', nav);
    $(window).resize(resizeNav);
    resizeNav();
});

function nav() {
    $('.nav-toggle, .nav-layer, .menu').toggleClass('open');
    if($('.nav-toggle').hasClass('open')) {
        // show menu sections on tab
        $(document).off('keyup').on('keyup', function(event) {
            if (event.keyCode == 9) {
                event.preventDefault();
                if($('#sub_nav_tabs').find('.nav-link.active').parent('li').next('li').length > 0) {
                    $('#sub_nav_tabs').find('.nav-link.active').parent('li').next('li').find('.nav-link').tab('show');
                } else {
                    $('#sub_nav_tabs').find('.nav-link').eq(0).tab('show');
                }
            }
        });
        setTimeout(function() {
            $('#main_search').focus();
        }, 400);
    }
}

// show search on ctrl + s and ctrl + m
window.addEventListener("keydown", function (event) {
    if ((window.navigator.platform.match("Mac") ? event.metaKey : event.ctrlKey) && (event.keyCode == 83 || event.keyCode == 77)) {
        event.preventDefault();
        nav();
    }
}, false);

window.resizeNav = function() {
    $('.menu').css({'height': window.innerHeight});
    var radius = Math.sqrt(Math.pow(window.innerHeight, 2) + Math.pow(window.innerWidth, 2));
    var diameter = radius * 2;
    $('.nav-layer').width(diameter);
    $('.nav-layer').height(diameter);
    $('.nav-layer').css({'margin-top': -radius, 'margin-left': -radius});
}
