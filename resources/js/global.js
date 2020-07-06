import datepicker from 'js-datepicker';

$(document).ready(function () {

    global_page_transition();

    inactivityTime();

    toastr.options = {
        "timeOut": 3000
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

    window.axios_headers_html = {
        'Accept-Version': 1,
        'Accept': 'text/html',
        'Content-Type': 'text/html'
    }

    // Add a response interceptor
    axios.interceptors.response.use(function (response) {
        //console.log(response);
        // log out user on axios calls if session expired
        if (response.data.dismiss || response.data.error) {
            window.parent.location = '/';
        }
        return response;
    }, function (error) {
        console.log('error = '+error);
        //location.reload();
        /* if(error.response.status === 404 || error.response.match(/404/) || error.response.status === 500 || error.response.match(/500/)) {
            window.location = '/';
        } */
        //return Promise.reject('error '+error);
    });



    $('.draggable').draggable({
        handle: '.draggable-handle'
    });

    $(document).on('keyup change', '.phone', function () {
        global_format_phone(this);
        $(this).attr('maxlength', 14);
    });

    setInterval(function() {
        datepicker_custom();
    }, 1000);


    global_tooltip();




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
        $('.modal').addClass('disable-scrollbars');
        // increase modal and backdrop z-index accordingly
        var zIndex = 1040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(function() {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
        // make all but modal-xl draggable
        if(!$(this).find('modal-dialog').hasClass('modal-xl')) {
            $(this).addClass('draggable');
        }

        // modal-open gets stuck in the body class so have to remove it manually
        /* let remove_modal_open = setInterval(function() {
            if($('.modal.show').length == 0) {
                $('body').removeClass('modal-open');
                clearInterval(remove_modal_open);
            } else {
                $('body').addClass('modal-open');
            }
        }, 1000); */

    });


});


window.datepicker_custom = function() {
    $('.datepicker').not('.datepicker-added').each(function() {
        $(this).addClass('datepicker-added');
        let id = $(this).prop('id');
        if(!id) {
            id = new Date();
        }
        window.picker = datepicker('#'+id, {
            onSelect: (instance, date) => {

            },
            onHide: instance => {

            },
            formatter: (input, date, instance) => {
                const value = date.toJSON().slice(0, 10);
                input.value = value;
                $('#'+id).focus().trigger('click');
            },
            showAllDates: true,
        });
    });
}

// session timeout
window.inactivityTime = function () {
    var time;
    window.onload = resetTimer;
    // DOM Events
    document.onmousemove = resetTimer;
    document.onkeypress = resetTimer;

    function logout() {
        location.href = '/'
    }

    function resetTimer() {
        clearTimeout(time);
        time = setTimeout(logout, 1000 * 60 * 60 * 2);
    }
};

// page transitions
window.global_page_transition = function() {

    if (document.URL.match(/(upload\/files)/)) {
        $('.loader').show();
        var tl = new TimelineMax();
        tl.to(CSSRulePlugin.getRule('body:before'), 0, { cssRule: { top: '50%' }, ease: Power2.easeOut }, 'close')
            .to(CSSRulePlugin.getRule('body:after'), 0, { cssRule: { bottom: '50%' }, ease: Power2.easeOut }, 'close')
            .to($('.loader'), 0, { opacity: 1 })
            .to(CSSRulePlugin.getRule('body:before'), 0.2, { cssRule: { top: '0%' }, ease: Power2.easeOut }, '+=0.5', 'open')
            .to(CSSRulePlugin.getRule('body:after'), 0.2, { cssRule: { bottom: '0%' }, ease: Power2.easeOut }, '-=0.2', 'open')
            .to($('.loader'), 0.2, { opacity: 0, display: 'none' }, '-=0.2');

    } else {
        $('.loader').hide();
    }

}

/**************************  STANDARD USE FUNCTIONS ***********************************/

window.scrollToAnchor = function(id) {
    var anchor_position = $('#'+id).offset().top;
    $('html,body').animate({
        scrollTop: anchor_position
    }, 1500);
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

window.global_loading_on = function(ele, html) {
    // ele used if containing loading frame in an element, otherwise leave blank
    let spinner_html = ' \
    <div class="loading-spinner"> \
        <div class="spinner-grow text-success" role="status"> \
            <span class="sr-only">Loading...</span> \
        </div> \
        <div class="spinner-grow text-danger" role="status"> \
            <span class="sr-only">Loading...</span> \
        </div> \
        <div class="spinner-grow text-warning" role="status"> \
            <span class="sr-only">Loading...</span> \
        </div> \
        <div class="spinner-grow text-info" role="status"> \
            <span class="sr-only">Loading...</span> \
        </div> \
    </div> \
    <div class="loading-spinner-html mt-0 mx-auto">'+html+'</div> \
    ';

    if(ele != '') {
        $(ele).html(spinner_html);
    } else {
        $('body').append('<div class="loading-bg">'+spinner_html+'</div>');
    }
}
window.global_loading_off = function() {
    $('.loading-spinner, .loading-spinner-html, .loading-bg').remove();
}

window.global_tooltip = function() {
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover();
}



window.global_get_url_parameters = function(key) {
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    if (urlParams.has(key)) {
        return urlParams.get(key);
    }
    return false;
}

// Format Phone
window.global_format_phone = function (obj) {
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
window.global_fmtssn = function (socInput) {
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
group_ids = group_ids.filter(global_filter_array);
*/

window.global_filter_array = function (value, index, self) {
    return self.indexOf(value) === index;
}

// Format Money
window.global_format_number = function (num) {
    const formatter = new Intl.NumberFormat('en-US', {
        style: 'decimal',
        minimumFractionDigits: 0
    });

    num = num.toString().replace(/[,\$]/g, '');
    return formatter.format(num);
}

// Date Difference JS
window.global_date_diff = function (s, e) {
    let start = new Date(s);
    let end = new Date(e);
    let diff = new Date(end - start);
    let days = Math.ceil(diff / (1000 * 60 * 60 * 24));

    return days;
}


// get location details from zip code
/* window.get_location_details = function(zip) {
    if(zip.length == 5) {
        let location_details = [];
        let city = state = county = '';
        axios.get('/agents/doc_management/global_functions/get_location_details, {
            params: {
                zip: zip
            },
        })
        .then(function (response) {
            let data = response.data;
            city = data.city;
            state = data.state;
            county = data.county;
            location_details.push(city);
            location_details.push(state);
            location_details.push(county);
        })
        .catch(function (error) {
            console.log(error);
        });

        return location_details;
    }
} */
