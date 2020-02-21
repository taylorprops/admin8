if (document.URL.match(/add_listing/)) {

    $(document).ready(function () {

        form_elements();

        // search address in google
        search_address();

        $('#enter_state').change(function () {
            update_county_select($(this).val());
        });

        $('#enter_zip').keyup(function() {
            fill_location($(this).val());
        });

        $('.required').bind('change keyup', function () {
            setTimeout(function () {
                enter_address_continue();
            }, 100);
        });
    });

    function create_listing() {
        let street_number = $('#enter_street_number').val();
        let street_name = $('#enter_street_name').val();
        let street_dir = $('#enter_street_dir').val();
        let unit_number = $('#enter_unit').val();
        let city = $('#enter_city').val();
        let state = $('#enter_state').val();
        let zip = $('#enter_zip').val();
        let county = $('#enter_county').val();
        alert('creating');
    }

    function found_listing(results_bright_type, results_bright_id, results_tax_id) {
        alert('found');
    }

    function show_property(response, street_number, street_name, city, state, zip, county, type) {

        // results_bright_type = db_active|db_closed|bright, results_bright_id, results_tax_id

        if(response.data != '') {
            // clear fields that may not contain data
            $('#property_details_owner1, #property_details_owner2, #property_details_beds, #property_details_baths').text('');
            $('.owner-div, .beds-baths-div, .active-listing-div').hide();

            // show always
            let ListPictureURL = response.data.ListPictureURL;
            if(ListPictureURL == undefined || ListPictureURL == '') {
                ListPictureURL = '/images/agents/doc_management/add_transaction/house.png';
            }
            let FullStreetAddress = response.data.FullStreetAddress;
            let City = response.data.City;
            let StateOrProvince = response.data.StateOrProvince;
            let County = response.data.County;
            if(StateOrProvince == 'MD') {
                County += ' County';
            }
            let PostalCode = response.data.PostalCode;
            let YearBuilt = response.data.YearBuilt;
            let Owner1 = response.data.Owner1;
            let Owner2 = response.data.Owner2;
            let BathroomsTotalInteger = response.data.BathroomsTotalInteger;
            let BedroomsTotal = response.data.BedroomsTotal;


            $('#property_details_photo').prop('src', ListPictureURL);
            $('#property_details_address').html(FullStreetAddress+'<br>'+City+', '+StateOrProvince+' '+PostalCode+'<br><span class="h5 mt-1 text-secondary">'+County.toUpperCase()+'</span>');
            $('#property_details_year_built').text(YearBuilt);
            if(Owner1) {
                $('.owner-div').show();
                $('#property_details_owner1').text(Owner1);
                $('#property_details_owner2').text(Owner2);
            }
            if(BedroomsTotal) {
                $('.beds-baths').show();
                $('#property_details_beds').text(BedroomsTotal);
                $('#property_details_baths').text(BathroomsTotalInteger);
            }


            // show only if active in mls
            let MlsStatus = response.data.MlsStatus;
            let ListingId = response.data.ListingId;
            let ListPrice = response.data.ListPrice;
            let PropertyType = response.data.PropertyType;
            let ListOfficeName = response.data.ListOfficeName;
            let MLSListDate = response.data.MLSListDate;
            let ListAgentFirstName = response.data.ListAgentFirstName;
            let ListAgentLastName = response.data.ListAgentLastName;

            if(response.data.results_bright_type == 'db_active' || response.data.results_bright_type == 'bright') {
                $('.active-listing-div').show();
                $('#property_details_status').text(MlsStatus);
                $('#property_details_mls_id').text(ListingId);
                $('#property_details_list_price').text('$'+global_format_number(ListPrice));
                $('#property_details_property_type').text(PropertyType);
                $('#property_details_listing_office').text(ListOfficeName);
                $('#property_details_list_date').text(MLSListDate);
                $('#property_details_listing_agent').text(ListAgentFirstName + ' ' + ListAgentLastName);
            }

            $('.property-loading-div').hide();
            global_loading_off();
            $('.property-results-container').fadeIn();

            $('#found_property_submit_button').off('click').on('click', function() {
                // send directly to add details page
                let results_bright_type = response.data.results_bright_type;
                let results_bright_id = response.data.results_bright_id;
                let results_tax_id = response.data.results_tax_id;

                found_listing(results_bright_type, results_bright_id, results_tax_id);

                //results_bright_type = db_active|db_closed|bright

            });

        } else {
            $('.property-loading-div').hide();
            global_loading_off();

            if(type == 'search') {
                // redirect to manually enter data
                $('#modal_danger').modal().find('.modal-body').html('No matches found. Please enter the property information manually.');
                $('#mls_match_container').collapse('hide');
                $('#address_container').collapse('show');
                $('#address_search_container').collapse('hide');
                $('#address_enter_container').collapse('show');
                autofill_manual_entry(street_number, street_name, city, state, zip, county);
                // next time address_enter_continue clicked go straight to enter details page.
                $('#address_enter_continue').data('go-to-details', 'yes');
            } else if(type == 'enter') {
                // if no results from manual entry go right to details page
                create_listing();
            }
        }
    }

    function fill_location(zip) {
        if(zip.length == 5) {
            axios.get('/agents/doc_management/global_functions/get_location_details', {
                params: {
                    zip: zip
                },
            })
            .then(function (response) {
                let data = response.data;
                $('#enter_city').val(data.city).trigger('change');
                $('#enter_state').val(data.state).trigger('change');
                update_county_select(data.state);
                setTimeout(function() {
                    $('#enter_county').val(data.county);
                    select_refresh();
                    enter_address_continue();
                }, 500);
            })
            .catch(function (error) {
                console.log(error);
            });
        }
    }

    function show_loader() {
        // clear current data and show loader
        $('.property-loading-div').fadeIn();
        global_loading_on($('.property-loading-div'), '<div class="h3 text-primary"><i class="fad fa-home-lg-alt mr-3"></i> Searching Properties</div>');
        $('.property-results-container').hide();
        $('#property_details_photo').prop('src', '');
        $('#property_details_address').text('');
    }

    function enter_address_continue() {
        let form = $('#enter_address_form');
        let cont = 'yes';
        form.find('.required').each(function () {
            if ($(this).val() == '') {
                cont = 'no';
            }
        });
        if (cont == 'yes') {
            $('#address_enter_continue').prop('disabled', false);
            $('#address_enter_continue').off('click').on('click', function () {
                let go_to_details = $(this).data('go-to-details');
                if(go_to_details == 'yes') {
                    // go to enter details page
                    create_listing();
                } else {
                    // search mls and tax records
                    show_loader();

                    let street_number = $('#enter_street_number').val();
                    let street_name = $('#enter_street_name').val();
                    let street_dir = $('#enter_street_dir').val();
                    let unit_number = $('#enter_unit').val();
                    let city = $('#enter_city').val();
                    let state = $('#enter_state').val();
                    let zip = $('#enter_zip').val();
                    let county = $('#enter_county').val();

                    axios.get('/agents/doc_management/transactions/get_property_info', {
                        params: {
                            street_number: street_number,
                            street_name: street_name,
                            street_dir: street_dir,
                            unit: unit_number,
                            city: city,
                            state: state,
                            zip: zip,
                            county: county
                        }
                    })
                    .then(function (response) {
                        show_property(response, street_number, street_name, city, state, zip, county, 'enter');
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

                }

            });
        } else {
            $('#address_enter_continue').prop('disabled', true);
        }
    }

    function update_county_select(state) {
        axios.get('/agents/doc_management/transactions/update_county_select', {
            params: {
                state: state
            },
        })
            .then(function (response) {
                let counties = response.data;
                $('#enter_county').html('').prop('disabled', false);
                $('#enter_county').append('<option value=""></option>');
                $.each(counties, function (k, v) {
                    $('#enter_county').append('<option value="' + v.county.toUpperCase() + '">' + v.county + '</option>');
                });
                setTimeout(function () {
                    select_refresh();
                }, 500);
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    function search_address() {

        // search input
        let address_search_street = document.getElementById('address_search_street');
        // select all text on focus
        $(address_search_street).focus(function () { $(this).select(); });
        // google address search
        let places = new google.maps.places.Autocomplete(address_search_street);
        google.maps.event.addListener(places, 'place_changed', function () {

            //$('#address_search_unit').val('');
            let address_details = places.getPlace();
            let street_number = street_name = city = county = state = zip = '';

            address_details.address_components.forEach(function (address) {
                if (address.types.includes('street_number')) {
                    street_number = address.long_name;
                } else if (address.types.includes('route')) {
                    street_name = address.long_name;
                } else if (address.types.includes('locality')) {
                    city = address.long_name;
                } else if (address.types.includes('administrative_area_level_2')) {
                    county = address.long_name.replace(/'/, '');
                    county = county.replace(/\sCounty/, '');
                } else if (address.types.includes('administrative_area_level_1')) {
                    state = address.short_name;
                } else if (address.types.includes('postal_code')) {
                    zip = address.long_name;
                }
            });


            $('#enter_manually_button').off('click').on('click', function () {
                autofill_manual_entry(street_number, street_name, city, state, zip, county);

            });

            // show continue button once address selected
            $('.address-search-error').hide();
            if (street_number != '') {
                $('.address-search-continue-div').show();
            } else {
                $('.address-search-continue-div').hide();
                $('.address-search-error').show();
                let search_val = $(address_search_street).val().replace(/^[0-9]+\s/, '');
                $(address_search_street).val(search_val)
            }

            $('#address_search_street').bind('keyup change', function () {
                if ($(this).val() == '') {
                    $('.address-search-continue-div').hide();
                }
            });
            // show results container
            $('#address_search_continue').off('click').on('click', function () {
                show_loader();
                //$('.address-container').collapse('hide');
                let unit_number = $('#address_search_unit').val();
                axios.get('/agents/doc_management/transactions/get_property_info', {
                    params: {
                        street_number: street_number,
                        street_name: street_name,
                        unit: unit_number,
                        city: city,
                        state: state,
                        zip: zip,
                        county: county
                    }
                })
                .then(function (response) {
                    show_property(response, street_number, street_name, city, state, zip, county, 'search');
                })
                .catch(function (error) {
                    console.log(error);
                });

            });

        });

    }

    function autofill_manual_entry(street_number, street_name, city, state, zip, county) {
        let unit = $('#address_search_unit').val();
        $('#enter_street_number').val(street_number).trigger('change');
        $('#enter_street_name').val(street_name).trigger('change');
        $('#enter_zip').val(zip).trigger('change');
        $('#enter_city').val(city).trigger('change');
        $('#enter_state').val(state).trigger('change');
        $('#enter_unit').val(unit).trigger('change');
        //update_county_select(state);
        setTimeout(function() {
            $('#enter_county').val(county.toUpperCase()).trigger('change');
            select_refresh();
            enter_address_continue();
        }, 500);
    }

}


