$(document).ready(function() {


    setTimeout(function() {
        $('.button-collapse').sideNav2({
            menuWidth: 250,
        });
    }, 500);
});


// focus search on ctrl + s
window.addEventListener("keydown", function (event) {
    if ((window.navigator.platform.match("Mac") ? event.metaKey : event.ctrlKey) && (event.keyCode == 83)) {
        event.preventDefault();
        $('#main_search_input').focus();
    }
}, false);

// show menu on ctrl + m
window.addEventListener("keydown", function (event) {
    if ((window.navigator.platform.match("Mac") ? event.metaKey : event.ctrlKey) && (event.keyCode == 77)) {
        event.preventDefault();
        $('.button-collapse').trigger('click');
    }
}, false);

