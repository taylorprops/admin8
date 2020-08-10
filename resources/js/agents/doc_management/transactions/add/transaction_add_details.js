if (document.URL.match(/transaction_add_details_/)) {

    $(document).ready(function () {

        // STEPS
        //$('.stepper').mdbStepper();

        // format list and contract price
        if($('#list_price').length > 0) {
            format_money($('#list_price'));
            $('#list_price').keyup(function() {
                format_money($(this));
            });
        } else {
            format_money($('#contract_price'));
            $('#contract_price').keyup(function() {
                format_money($(this));
            });
        }


        if($('#contract_price').val() == '$0') {
            $('#contract_price').val('');
        }


        // show hide disclosure questions - only required for standard and short sales
        disclosure_options();
        // show hide sub property types if rental
        sale_rental_options();
        $('#listing_type').change(sale_rental_options);

        $('#property_sub_type').change(disclosure_options);

        form_elements();

        $('#submit_details_form_button').off('click').on('click', function(e) {
            e.preventDefault();
            save_add_transaction();
        });


    });

    function save_add_transaction() {

        let form = $('#details_form');

        let validate = validate_form(form);

        if(validate == 'yes') {
            let transaction_type = $('#transaction_type').val();
            let formData = new FormData(form[0]);

            axios.post('/agents/doc_management/transactions/save_add_transaction', formData, axios_options)
            .then(function (response) {
                window.location = '/agents/doc_management/transactions/add/transaction_required_details/'+response.data.id+'/'+transaction_type;
            })
            .catch(function (error) {
                console.log(error);
            });
        }

    }

    function disclosure_options() {

        let sale_type = $('#property_sub_type').val();

        if(sale_type == 'Standard' || sale_type == 'Short Sale') {
            $('.disclosures').show();
            //$('#hoa_condo').val('').trigger('change');
        } else {
            $('.disclosures').hide();
            $('#hoa_condo').val('none').trigger('change');
        }

    }

    function sale_rental_options() {
        let sale_type = $('#property_sub_type').val();
        let listing_type = $('#listing_type').val();
        if(listing_type == 'rental') {
            $('.property-sub-type').hide().find('#property_sub_type').val('Standard').trigger('change');
            $('.year-built, .hoa').hide();
        } else {
            if(sale_type == 'Standard' || sale_type == 'Short Sale') {
                $('.property-sub-type, .year-built, .hoa').show();
            } else {
                $('.property-sub-type, .year-built').show();
            }
        }

    }




}
