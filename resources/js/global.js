$(document).ready(function () {

    // send csrf with every ajax request
    var _token = $('meta[name="csrf-token"]').attr('content');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': _token
        }
    });


    // Form Validation
    (function () {
        'use strict';
        window.addEventListener('load', function () {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('needs-validation');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function (form) {
                form.addEventListener('submit', function (event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();

    setTimeout(function () {

        // set datepicker
        $('.datepicker').pickadate({
            format: 'mm/dd/yyyy',
            formatSubmit: 'yyyy-mm-dd',
            hiddenName: true
        });

        // mdb selects
        $('.mdb-select').materialSelect();



    }, 1500);

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



