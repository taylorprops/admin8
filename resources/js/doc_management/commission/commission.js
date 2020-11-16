if (document.URL.match(/commission/) || document.URL.match(/transaction_details/)) {

    let page = 'commission';
    if(document.URL.match(/details/)) {
        page = 'details';
    }

    $(function() {

        form_elements();
        global_format_money();
        get_check_info();
        get_checks();

        $('#save_add_check_in_button').off('click').on('click', save_add_check_in);


    });

    function get_checks() {
        axios.get('/doc_management/commission/get_checks', {
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {
            console.log(response);
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.get_check_info = function() {
        // get check info when adding a check
        $('#check_in_upload').off('change').on('change', function () {

            if($(this).val() != '') {

                $('#check_in_date').val('');
                $('#check_in_amount').val('');
                $('#check_in_number').val('');

                global_loading_on('', '<div class="h5 text-white">Scanning Check</div>');
                let form = $('#add_check_in_form');
                let formData = new FormData(form[0]);
                axios.post('/agents/doc_management/transactions/get_check_details', formData, axios_options)
                .then(function (response) {
                    if(response.data.check_date) {
                        $('#check_in_date').val(response.data.check_date);
                        $('#check_in_amount').val(response.data.check_amount);
                        $('#check_in_number').val(response.data.check_number);
                    }
                    $('.check-in-preview-div').html('<div class="border border-primary mt-2 check-preview"><img src="'+response.data.check_location+'" class="w-100"></div>');
                    global_loading_off();

                })
                .catch(function (error) {
                    console.log(error);
                });
            }

        });
    }


    window.save_add_check_in = function() {

        let Commission_ID = $('#Commission_ID').val();
        let form = $('#add_check_in_form');
        let validate = validate_form(form);

        if(validate == 'yes') {

            $('#save_add_check_in_button').prop('disabled', true).html('<i class="fas fa-spinner fa-pulse mr-2"></i> Adding Check...');
            let formData = new FormData(form[0]);
            formData.append('page', page);
            if(page == 'details') {
                formData.append('Commission_ID', Commission_ID);
            }

            axios.post('/agents/doc_management/transactions/save_add_check_in', formData, axios_options)
            .then(function (response) {

                toastr['success']('Check Successfully Added');

                if(page == 'details') {
                    get_checks_in(Commission_ID);
                    $('#add_check_in_modal').modal('hide');
                } else {
                    $('#add_check_div').collapse('hide');
                    clear_check_form();
                    get_checks();
                }

                $('#save_add_check_in_button').prop('disabled', false).html('<i class="fad fa-check mr-2"></i> Save');

            })
            .catch(function (error) {
                console.log(error);
            });

        }

    }


    function clear_check_form() {
        $('#add_check_in_form').find('.custom-form-element').val('');
        $('.check-in-preview-div').html('');
        select_refresh();
    }

}


