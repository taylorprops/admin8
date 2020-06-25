if (document.URL.match(/transaction_add_details_/)) {

    $(document).ready(function () {

        $('.stepper').mdbStepper();

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



        // add value next to title on change
        show_values();
        $('.steps-container').find('input').change(function() {
            show_values();
        });
        // show hide disclosure questions - only required for standard and short sales
        disclosure_options();
        // show hide sub property types if rental
        sale_rental_options();
        $('[name=listing_type]').change(sale_rental_options);

        $('[name=property_sub_type]').change(disclosure_options);

        form_elements();

        $('#steps_submit').off('click').on('click', function() {
            save_add_transaction();
        });

    });

    function save_add_transaction() {

        //global_loading_on('', '<div class="h3 text-white">Creating Checklist</div>');
        let form = $('#steps_form');
        let transaction_type = $('#transaction_type').val();
        let formData = new FormData(form[0]);
        axios.post('/agents/doc_management/transactions/save_add_transaction', formData, axios_options)
        .then(function (response) {
            //global_loading_off();
            window.location = '/agents/doc_management/transactions/add/transaction_required_details/'+response.data.id+'/'+transaction_type;
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
        let sale_type = $('[name=property_sub_type]:checked').val();
        let listing_type = $('[name=listing_type]:checked').val();
        if(listing_type == 'rental') {
            $('.property-sub-type').hide().find('[name=property_sub_type][value=Standard]').prop('checked', true);
            $('.year-built, .hoa').hide();
        } else {
            if(sale_type == 'Standard' || sale_type == 'Short Sale') {
                $('.property-sub-type, .year-built, .hoa').show();
            } else {
                $('.property-sub-type, .year-built').show();
            }
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

    function format_money(ele) {

        ele.val('$'+global_format_number(ele.val().replace('/\$/', '')));

    }

}