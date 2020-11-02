if (document.URL.match(/transaction_details/)) {

    $(function() {

        $(document).on('click', '.show-view-add-button', popout);

        $(document).on('keyup', '.total', total_commission);

    });

    window.total_commission = function() {

        let fields_filled = true;
        $('.total').each(function() {
            if($(this).val() == '') {
                fields_filled = false;
            }
        });

        if(fields_filled == true) {

            let total = 0;

            let checks_in = parseFloat($('#checks_in_total_value').val().replace(/[,\$]/g, ''));
            let money_in_escrow = parseFloat($('#money_in_escrow').val().replace(/[,\$]/g, ''));
            let admin_fee_from_title = parseFloat($('#admin_fee_from_title').val().replace(/[,\$]/g, ''));
            let income_deductions = parseFloat($('#income_deductions_total_value').val().replace(/[,\$]/g, ''));
            let admin_fee_from_client = parseFloat($('#admin_fee_from_client').val().replace(/[,\$]/g, ''));

            let total_income = (checks_in + money_in_escrow + admin_fee_from_title) - income_deductions - admin_fee_from_client;

            $('#total_income').html(global_format_number_with_decimals(total_income.toString()));
            $('#total_income_value').val(total_income);

            let agent_commission_percent = parseInt($('#agent_commission_percent').val()) / 100;
            let agent_commission_amount = total_income * agent_commission_percent;
            $('#agent_commission_amount').val(global_format_number_with_decimals(Math.floor(agent_commission_amount).toFixed(2)));

        }



    }

    window.get_check_deductions = function(Commission_ID) {

        axios.get('/agents/doc_management/transactions/get_check_deductions', {
            params: {
                Commission_ID: Commission_ID
            }
        })
        .then(function (response) {

            $('.check-deductions-div').html('');

            let deductions = response.data.deductions;
            let deductions_count = deductions.length;
            let deductions_total = 0;

            if(deductions_count > 0) {

                deductions.forEach(function(deduction) {

                    deductions_total += parseFloat(deduction['amount']);

                    let list_item = ' \
                    <div class="list-group-item d-flex justify-content-between align-items-center"> \
                        <div>'+deduction['description']+'</div> \
                        <div class="d-flex justify-content-end align-items-center"> \
                            <div class="pr-5">'+global_format_number_with_decimals(deduction['amount'])+'</div> \
                            <div><a href="javascript: void(0)" class="btn btn-sm btn-danger delete-deduction-button" data-deduction-id="'+deduction['id']+'"><i class="fal fa-times"></i></a></div> \
                        </div> \
                    </div> \
                    ';
                    $('.check-deductions-div').append(list_item);
                });

            }

            $('.delete-deduction-button').off('click').on('click', delete_deduction);
            deductions_total = global_format_number_with_decimals(deductions_total.toString());
            $('#deductions_total_value').val(deductions_total);
            $('#income_deductions_total_value').val(deductions_total);
            $('#deductions_total').text(deductions_total);
            $('#deductions_count').text(deductions_count);

            total_commission();

        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.delete_deduction = function() {
        let Commission_ID = $('#Commission_ID').val();
        let button = $(this);
        let deduction_id = button.data('deduction-id');
        let formData = new FormData();
        formData.append('deduction_id', deduction_id);
        axios.post('/agents/doc_management/transactions/delete_check_deduction', formData, axios_options)
        .then(function (response) {
            get_check_deductions(Commission_ID);
            toastr['success']('Deduction Successfully Deleted');
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.save_add_check_deduction = function() {

        let form = $('#add_check_deduction_div');
        let validate = validate_form(form);

        if(validate == 'yes') {

            let Commission_ID = $('#Commission_ID').val();
            let description = $('#check_deduction_description').val();
            let amount = $('#check_deduction_amount').val();

            let formData = new FormData();
            formData.append('Commission_ID', Commission_ID);
            formData.append('description', description);
            formData.append('amount', amount);

            axios.post('/agents/doc_management/transactions/save_add_check_deduction', formData, axios_options)
            .then(function (response) {
                $('#add_check_deduction_div').collapse('hide');

                toastr['success']('Deduction Successfully Added');
                get_check_deductions(Commission_ID);
            })
            .catch(function (error) {
                console.log(error);
            });

        }

    }

    window.get_checks_in = function(Commission_ID) {

        axios.get('/agents/doc_management/transactions/get_checks_in', {
            params: {
                Commission_ID: Commission_ID
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {
            $('.checks-in-div').html(response.data);
            $('#checks_in_total').text(global_format_number_with_decimals($('#checks_total').val().toString()));
            $('#checks_in_total_value').val($('#checks_total').val().toString());
            $('#checks_in_count').text($('#checks_count').val());
            $('.delete-check-in-button').off('click').on('click', show_delete_check_in);
            $('.edit-check-in-button').off('click').on('click', show_edit_check_in);
            $('#save_edit_check_in_button').off('click').on('click', save_edit_check_in);
            $('.undo-delete-check-in-button').off('click').on('click', undo_delete_check_in)
            $('.show-deleted-button').off('click').on('click', function() {
                $('.inactive').toggleClass('hidden');
            });
            total_commission();
        })
        .catch(function (error) {
            console.log(error);
        });

    }

    window.undo_delete_check_in = function() {

        let Commission_ID = $('#Commission_ID').val();
        let check_id = $(this).data('check-id');
        let formData = new FormData();
        formData.append('check_id', check_id);
        axios.post('/agents/doc_management/transactions/undo_delete_check_in', formData, axios_options)
        .then(function (response) {
            get_checks_in(Commission_ID);
            toastr['success']('Check Successfully Reactivated');
        })
        .catch(function (error) {
            console.log(error);
        });

    }

    window.show_add_check_in = function() {

        $('#add_check_in_modal').modal();

        $('#check_upload').off('change').on('change', function () {

            if($(this).val() != '') {

                global_loading_on('', '<div class="h5 text-white">Scanning Check</div>');
                let form = $('#add_check_in_form');
                let formData = new FormData(form[0]);
                axios.post('/agents/doc_management/transactions/get_check_in_details', formData, axios_options)
                .then(function (response) {
                    if(response.data.check_date) {
                        $('#check_date').val(response.data.check_date).trigger('change');
                        $('#check_amount').val(response.data.check_amount).trigger('change');
                        $('#check_number').val(response.data.check_number).trigger('change');
                    }
                    $('.check-preview-div').html('<div class="border border-primary mt-2 check-preview"><img src="'+response.data.check_location+'" class="w-100"></div>');
                    global_loading_off();

                })
                .catch(function (error) {
                    console.log(error);
                });
            }

        });

    }

    window.save_add_check_in = function() {

        let form = $('#add_check_in_form');
        let validate = validate_form(form);

        if(validate == 'yes') {

            $('#save_add_check_in_button').prop('disabled', true).html('<i class="fas fa-spinner fa-pulse mr-2"></i> Adding Check...');
            let formData = new FormData(form[0]);
            let Commission_ID = $('#Commission_ID').val();
            formData.append('Commission_ID', Commission_ID);

            axios.post('/agents/doc_management/transactions/save_add_check_in', formData, axios_options)
            .then(function (response) {
                $('#add_check_in_modal').modal('hide');
                toastr['success']('Check Successfully Added');
                get_checks_in(Commission_ID);
                clear_add_check_form();
                $('#save_add_check_in_button').prop('disabled', false).html('<i class="fad fa-check mr-2"></i> Save');
            })
            .catch(function (error) {
                console.log(error);
            });

        }

    }

    window.show_edit_check_in = function() {

        $('#edit_check_in_modal').modal();
        $('.edit-check-preview-div').html('<div class="border border-primary mt-2 check-preview"><img src="'+$(this).data('image-location')+'" class="w-100"></div>');
        $('#edit_check_id').val($(this).data('check-id'));
        $('#edit_check_date').val($(this).data('check-date'));
        $('#edit_check_number').val($(this).data('check-number'));
        $('#edit_check_amount').val($(this).data('check-amount'));
        $('#edit_date_received').val($(this).data('date-received'));
        $('#edit_date_deposited').val($(this).data('date-deposited'));
        $('input').trigger('change');

        $('#save_edit_check_in_button').off('click').on('click', save_edit_check_in);

    }

    window.save_edit_check_in = function() {

        let Commission_ID = $('#Commission_ID').val();
        let form = $('#edit_check_in_form');
        let formData = new FormData(form[0]);

        axios.post('/agents/doc_management/transactions/save_edit_check_in', formData, axios_options)
        .then(function (response) {
            get_checks_in(Commission_ID);
            toastr['success']('Check Successfully Edited');
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.show_delete_check_in = function() {
        let check_id = $(this).data('check-id');
        $('#confirm_modal').modal().find('.modal-body').html('<div class="d-flex justify-content-start align-items-center"><div class="mr-3"><i class="fad fa-exclamation-circle fa-2x text-danger"></i></div><div class="text-center">Are you sure you want to delete this check?</div></div>');
        $('#confirm_modal').modal().find('.modal-title').html('Delete Check');
        $('#confirm_button').off('click').on('click', function() {
            save_delete_check_in(check_id);
        });
    }

    window.save_delete_check_in = function(check_id) {

        let Commission_ID = $('#Commission_ID').val();
        let formData = new FormData();
        formData.append('check_id', check_id);
        axios.post('/agents/doc_management/transactions/save_delete_check_in', formData, axios_options)
        .then(function (response) {
            get_checks_in(Commission_ID);

        })
        .catch(function (error) {
            console.log(error);
        });
    }


    window.clear_add_check_form = function() {
        $('#add_check_in_form').find('input').not('#date_received').val('').trigger('change');
        $('.check-preview-div').html('');
    }


    window.get_commission_notes = function(Commission_ID) {

    }

    function popout() {

        if(!$(this).closest('.popout-action').hasClass('bg-blue-light')) {

            let popout = $(this).closest('.popout-row').find('.popout');
            /*
            flipOutX flipInX
            slideInLeft slideOutRight
            */
            let anime_in = 'flipInX';
            let anime_out = 'flipOutX';
            $('.popout-action, .popout').removeClass('active bg-blue-light '+ anime_in+' '+anime_out);
            $(this).closest('.popout-action').addClass('bg-blue-light');

            $('.popout').not(popout).addClass(anime_out).hide();

            popout.addClass('active bg-blue-light '+ anime_in).fadeIn();
            if($(window).width() > 992) {
                $('.popout.middle').css({ top: '-'+ ((popout.height() / 2) - 30) + 'px' });
            }

            setTimeout(function() {
                //$('.popout').removeClass('');
            }, 100);

        }

    }

}
