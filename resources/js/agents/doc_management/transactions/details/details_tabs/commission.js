if (document.URL.match(/transaction_details/)) {

    $(document).ready(function() {

        $(document).on('click', '.show-view-add-button', popout);

    });

    window.get_checks_in = function(Commission_ID) {
        $('.checks-in-div').hide().html('');
        axios.get('/agents/doc_management/transactions/get_checks_in', {
            params: {
                Commission_ID: Commission_ID
            }
        })
        .then(function (response) {

            let checks_count = response.data.checks_in.length;
            let checks_total = 0;

            response.data.checks_in.forEach(function(check) {

                let check_amount = parseFloat(check.check_amount);
                checks_total += check_amount;

                let row = ' \
                <div class="p-2 mr-2 border bg-white mb-2 z-depth-1 rounded"> \
                    <div class="row"> \
                        <div class="col-12 col-md-3"> \
                            <div class="list-group"> \
                                <div class="list-group-item p-1"> \
                                    #'+check.check_number+' \
                                </div> \
                                <div class="list-group-item p-1"> \
                                    '+check.check_date+' \
                                </div> \
                                <div class="list-group-item p-1"> \
                                    $'+check.check_amount+' \
                                </div> \
                            </div> \
                        </div> \
                        <div class="col-9 col-md-6"> \
                            <div class="check-image-div"> \
                                <img src="'+check.image_location+'" class="w-100"> \
                            </div> \
                        </div> \
                        <div class="col-3"> \
                            <a href="'+check.file_location+'" target="_blank" class="btn btn-block btn-sm btn-primary"><i class="fad fa-eye mr-1"></i> View</a> \
                            <a href="javascript: void(0)" \
                            class="btn btn-block btn-sm btn-default edit-check-in-button" \
                            data-check-id="'+check.id+'" \
                            data-date-received="'+check.date_received+'" \
                            data-date-deposited="'+check.date_deposited+'" \
                            data-check-date="'+check.check_date+'" \
                            data-check-number="'+check.check_number+'" \
                            data-check-amount="'+check.check_amount+'" \
                            data-image-location="'+check.image_location+' \
                            "><i class="fad fa-edit mr-1"></i> Edit</a> \
                            <a href="javascript: void(0)" class="btn btn-block btn-sm btn-danger delete-check-in-button" data-check-id="'+check.id+'"><i class="fad fa-trash mr-1"></i> Trash</a> \
                        </div> \
                    </div> \
                </div> \
                ';

                $('.checks-in-div').append(row).fadeIn('slow');

            });

            let total = global_format_number_with_decimals(checks_total.toString());
            $('#checks_in_total').text(total);
            $('#checks_in_count').text(checks_count);

            $('.delete-check-in-button').off('click').on('click', show_delete_check_in);

            $('.edit-check-in-button').off('click').on('click', show_edit_check_in);

            $('#save_edit_check_in_button').off('click').on('click', save_edit_check_in);

        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.show_add_check_in = function() {
        $('#add_check_in_modal').modal();
        $('#check_upload').on('change', function () {
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
        $('#edit_check_date').val($(this).data('check-date'));
        $('#edit_check_number').val($(this).data('check-number'));
        $('#edit_check_amount').val($(this).data('check-amount'));
        $('#edit_date_received').val($(this).data('date-received'));
        $('#edit_date_deposited').val($(this).data('date-deposited'));
        $('input').trigger('change');
    }

    window.save_edit_check_in = function() {

    }

    window.show_delete_check_in = function() {
        let check_id = $(this).data('check-id');
        $('#confirm_modal').modal().find('.modal-body').html('<div class="d-flex justify-content-start align-items-center"><div class="mr-3"><i class="fad fa-exclamation-circle fa-2x text-danger"></i></div><div class="text-center">Are you sure you want to delete this check?</div></div>');
        $('#confirm_modal').modal().find('.modal-title').html('Delete Check');
        $('#confirm_button').on('click', function() {
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



    window.save_add_check_deduction = function() {

    }

    window.show_add_check_deduction = function() {

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
