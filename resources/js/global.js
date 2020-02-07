$(document).ready(function () {


    toastr.options = {
        "timeOut": 2000
    }

    // send csrf with every ajax request
    window._token = $('meta[name="csrf-token"]').attr('content');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': _token
        }
    });
    // axios headersObj
    window.axios_options = {
        headers: { 'X-CSRF-TOKEN': _token }
    };

    // Add a response interceptor
    axios.interceptors.response.use(function (response) {
        // log out user on axios calls if session expired
        if (response.data.dismiss) {
            window.location = '/';
        }
        return response;
    }, function (error) {
        if(error.response.status === 404) {
            window.location = '/';
        }
        return Promise.reject('error '+error);
    });

    // go to dashboard on ctrl + d
    window.addEventListener("keydown", function (event) {
        if ((window.navigator.platform.match("Mac") ? event.metaKey : event.ctrlKey) && event.keyCode == 68) {
            event.preventDefault();
            window.location = '/dashboard';
        }
    }, false);

    $('.draggable').draggable({
        handle: '.draggable-handle'
    });

    $('.phone').keyup(function () {
        format_phone(this);
    });

    tooltip();




    // confirm modals on enter | requires .modal-confirm and .modal-confirm-button
    /* $('.modal-confirm').on('show.bs.modal', function () {
        $('body').off('keyup').on('keyup', function(event) {
            if (event.keyCode === 13) {
                $(this).find('.modal-confirm-button').trigger('click');
            }
        });
    }); */
    // multiple modal stacking
    $(document).on('show.bs.modal', '.modal', function () {
        var zIndex = 1040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(function() {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
    });


    page_transition();


});

// page transitions
window.page_transition = function() {

    if (document.location.pathname !== '/') {


        var tl = new TimelineMax();

        tl.to(CSSRulePlugin.getRule('body:before'), 0, { cssRule: { top: '50%' }, ease: Power2.easeOut }, 'close')
            .to(CSSRulePlugin.getRule('body:after'), 0, { cssRule: { bottom: '50%' }, ease: Power2.easeOut }, 'close')
            .to($('.loader'), 0, { opacity: 1 })
            .to(CSSRulePlugin.getRule('body:before'), 0.2, { cssRule: { top: '0%' }, ease: Power2.easeOut }, '+=1.5', 'open')
            .to(CSSRulePlugin.getRule('body:after'), 0.2, { cssRule: { bottom: '0%' }, ease: Power2.easeOut }, '-=0.2', 'open')
            .to($('.loader'), 0.2, { opacity: 0, display: 'none' }, '-=0.2');

    }

}

/**************************  STANDARD USE FUNCTIONS ***********************************/


// Numbers Only
$(document).on('keydown', '.numbers-only', function (event) {
    // Allow special chars + arrows
    if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 190 || event.keyCode == 110
        || event.keyCode == 27 || event.keyCode == 13
        || ((event.keyCode == 65 || event.keyCode == 86 || event.keyCode == 90) && event.ctrlKey == true)
        || (event.keyCode >= 35 && event.keyCode <= 39)) {
        return;
    } else {
        // If it's not a number stop the keypress
        if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105)) {
            event.preventDefault();
        }
    }
});

window.tooltip = function() {
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover();
}


window.get_url_parameters = function(key) {
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    if (urlParams.has(key)) {
        return urlParams.get(key);
    }
    return false;
}

// Format Phone
window.format_phone = function (obj) {
    //function format_phone(obj) {
    let numbers = obj.value.replace(/\D/g, ''),
        char = { 0: '(', 3: ') ', 6: '-' };
    obj.value = '';
    for (let i = 0; i < numbers.length; i++) {
        if (i > 13) {
            return false;
        }
        obj.value += (char[i] || '') + numbers[i];
    }
}

// FORMAT SOCIAL SECURITY
window.fmtssn = function (socInput) {
    //function fmtssn(socInput) {
    re = /\D/g; // remove any characters that are not numbers
    socnum = socInput.value.replace(re, "");
    sslen = socnum.length;
    if (sslen > 3 && sslen < 6) {
        ssa = socnum.slice(0, 3);
        ssb = socnum.slice(3, 5);
        socInput.value = ssa + "-" + ssb;
    } else {
        if (sslen > 5) {
            ssa = socnum.slice(0, 3);
            ssb = socnum.slice(3, 5);
            ssc = socnum.slice(5, 9);
            socInput.value = ssa + "-" + ssb + "-" + ssc;
        } else {
            socInput.value = socnum;
        }
    }
}

/*
PURPOSE: remove duplicates from array
USAGE:
group_ids = ['a', 'b', 'c', 'c'];
group_ids = group_ids.filter(filter_array);
*/

window.filter_array = function (value, index, self) {
    //export function filter_array(value, index, self) {
    return self.indexOf(value) === index;
}

// Format Money
window.format_number = function (num) {
    //export function format_number(num) {
    const formatter = new Intl.NumberFormat('en-US', {
        style: 'decimal',
        minimumFractionDigits: 0
    });

    num = num.toString().replace(/[,\$]/g, '');
    return formatter.format(num);
}

// Date Difference JS
window.date_diff = function (s, e) {
    //export function date_diff(s, e) {

    let start = new Date(s);
    let end = new Date(e);
    let diff = new Date(end - start);
    let days = Math.ceil(diff / (1000 * 60 * 60 * 24));

    return days;
}
