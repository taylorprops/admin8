if (document.URL.match(/add_listing_details_existing/)) {

    $(document).ready(function () {

        $('.stepper').mdbStepper();

        // format list price
        format_money();
        $('#list_price').keyup(function() {
            format_money();
        });
        // add value next to title on change
        show_values();
        $('.steps-container').find('input').change(function() {
            show_values();
        });
        // show hide sub property types if rental
        sale_rental_options();
        $('[name=listing_type]').change(sale_rental_options);
        // show hide disclosure questions - only required for standard and short sales
        disclosure_options();
        $('[name=property_sub_type]').change(disclosure_options);

        form_elements();

        $('#steps_submit').off('click').on('click', function() {
            save_add_listing();
        });

    });

    function save_add_listing() {

        let form = $('#steps_form');
        let formData = new FormData(form[0]);
        axios.post('/agents/doc_management/transactions/save_add_listing', formData, axios_options)
        .then(function (response) {
            window.location = '/transactions/listing/'+response.data;
        })
        .catch(function (error) {
            console.log(error);
        });

    }

    function disclosure_options() {

        let sale_type = $('[name=property_sub_type]:checked').val();
        if(sale_type == 'Standard' || sale_type == 'Short Sale') {
            $('.disclosures').show();
            $('[name=hoa_condo][value=none]').prop('checked', false);
        } else {
            $('.disclosures').hide();
            $('[name=hoa_condo][value=none]').prop('checked', true);
        }
        show_values();

    }

    function sale_rental_options() {

        let listing_type = $('[name=listing_type]:checked').val();
        if(listing_type == 'rental') {
            $('.property-sub-type').hide().find('[name=property_sub_type][value=Standard]').prop('checked', true);
            $('.year-built, .hoa').hide();
        } else {
            $('.property-sub-type, .year-built, .hoa').show();
        }
        show_values();

    }

    function show_values() {

        $('.steps-container').find('input').each(function() {
            let val = '';
            if($(this).attr('type') == 'radio') {
                let name = $(this).attr('name');
                val = $('[name='+name+']:checked').val();
            } else {
                val = $(this).val();
            }
            if(val) {
                $(this).closest('.step').find('.step-value').text(val.toUpperCase());
            } else {
                $(this).closest('.step').find('.step-value').text('');
            }
        });

    }

    function format_money() {

        $('#list_price').val('$'+global_format_number($('#list_price').val().replace('/\$/', '')));

    }

}
