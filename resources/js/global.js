import SimpleBar from 'simplebar';
// import 'simplebar/dist/simplebar.css'; included local file in app.scss so it can be edited
// import { form_elements, select_refresh } from '@/form_elements.js';

$(document).ready(function () {


    // send csrf with every ajax request
    window._token = $('meta[name="csrf-token"]').attr('content');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': _token
        }
    });
    // axios headersObj
    window.axios_options = {
        headers: {'X-CSRF-TOKEN': _token}
    };

    // show search on ctrl + s
    window.addEventListener("keydown", function (event) {
        if ((window.navigator.platform.match("Mac") ? event.metaKey : event.ctrlKey)  && event.keyCode == 83) {
            event.preventDefault();
            $('.dropdown-menu').removeClass('show');
            $('.nav-item.dropdown').removeClass('show active');
            $('#main_search').focus();
        }
    }, false);

    // show menu on ctrl + m
    window.addEventListener("keydown", function (event) {
        if ((window.navigator.platform.match("Mac") ? event.metaKey : event.ctrlKey)  && event.keyCode == 77) {
            event.preventDefault();
            $('#navbar_menu_link').trigger('click');
            $('#navbar_main_menu').find('.dropdown-menu a').first().focus();
        }
    }, false);

    // go to dashboard on ctrl + d
    window.addEventListener("keydown", function (event) {
        if ((window.navigator.platform.match("Mac") ? event.metaKey : event.ctrlKey)  && event.keyCode == 68) {
            event.preventDefault();
            window.location='/dashboard';
        }
    }, false);

    $('.draggable').draggable()

    $('.phone').keyup(function () {
        format_phone(this);
    });



    /**************************  STANDARD USE FUNCTIONS ***********************************/
    // Format Phone
    function format_phone(obj) {
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
    function fmtssn(socInput) {
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



});

/***************  EXPORTED FUNCTIONS - used with js files from /resources/js/ANY FOLDER ****************/



/*
PURPOSE: remove duplicates from array
USAGE:
group_ids = ['a', 'b', 'c', 'c'];
group_ids = group_ids.filter(filter_array);
*/
export function filter_array(value, index, self) {
    return self.indexOf(value) === index;
}

// Format Money
export function format_number(num) {
    const formatter = new Intl.NumberFormat('en-US', {
        style: 'decimal',
        minimumFractionDigits: 0
    });

    num = num.toString().replace(/[,\$]/g, '');
    return formatter.format(num);
}

// Date Difference JS
export function date_diff(s, e) {

    let start = new Date(s);
    let end = new Date(e);
    let diff = new Date(end - start);
    let days = Math.ceil(diff / (1000 * 60 * 60 * 24));

    return days;
}



