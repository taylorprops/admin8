if (document.URL.match(/listing_details/)) {

    window.save_details = function() {
        let form = $('#listing_details_form');
        let formData = new FormData(form[0]);
        formData.append('Listing_ID', $('#Listing_ID').val());
        axios.post('/agents/doc_management/transactions/listings/save_details', formData, axios_options)
        .then(function (response) {
            if(response.data.status == 'ok') {
                load_tabs('details');
                load_details_header($('#Listing_ID').val());
                toastr['success']('Listing Details Saved!');
            }
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.save_search_mls = function() {

        $('#confirm_import_modal').modal('hide');

        let ListingId = $('#ListingId').val();
        let Listing_ID = $('#Listing_ID').val();
        axios.get('/agents/doc_management/transactions/listings/save_mls_search', {
            params: {
                ListingId: ListingId,
                Listing_ID: Listing_ID
            }
        })
        .then(function (response) {
            if(response.data.status == 'ok') {

                $('#modal_success').modal().find('.modal-body').html('BrightMLS Data Successfully Imported');
                load_tabs('details');
                load_tabs('checklist');
                load_details_header(Listing_ID);

            }
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.search_mls = function() {
        let ListingId = $('#ListingId').val();
        let Listing_ID = $('#Listing_ID').val();
        axios.get('/agents/doc_management/transactions/listings/mls_search', {
            params: {
                ListingId: ListingId,
                Listing_ID: Listing_ID
            }
        })
        .then(function (response) {

            if(response.data.status == 'ok') {

                search_response = 'We have located the property in BrightMLS!<br><br><i class="fad fa-exclamation-triangle mr-2"></i> The checklist will be replaced to include the forms required for the new listing. Any relevant forms will be kept in the checklist but some may need to be added or replaced<hr>';

                let listing_details = ' \
                    <div class="row"> \
                        <div class="col-12 col-md-5"> \
                            <img src="'+response.data.picture_url+'" class="confirm-import-image"> \
                        </div> \
                        <div class="col-12 col-md-7"> \
                            Listed By '+response.data.list_company+'<br> \
                            '+response.data.address+'<br> \
                            '+response.data.city+', '+response.data.state+' '+response.data.zip+'<br> \
                        </div> \
                    </div> \
                ';

                $('#confirm_import_modal').modal().find('.modal-body').html(search_response + listing_details);

                $('#confirm_import_button').off('click').on('click', save_search_mls);

            } else if(response.data.status == 'not found') {

                $('#modal_danger').modal().find('.modal-body').html('<div class="h4 text-danger w-100 text-center"><i class="fad fa-exclamation-triangle mr-2"></i> Listing not found in BrightMLS</div>');

            }
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.update_county_select = function() {
        let state = $('#StateOrProvince').val();
        axios.get('/agents/doc_management/transactions/update_county_select', {
            params: {
                state: state
            },
        })
            .then(function (response) {
                let counties = response.data;
                $('#County').html('').prop('disabled', false);
                $('#County').append('<option value=""></option>');
                $.each(counties, function (k, v) {
                    $('#County').append('<option value="' + v.county.toUpperCase() + '">' + v.county + '</option>');
                });
                setTimeout(function () {
                    select_refresh();
                    if (state == 'DC') {
                        $('#County').val('DISTRICT OF COLUMBIA');
                        select_refresh();
                    }
                }, 500);
            })
            .catch(function (error) {
                console.log(error);
            });
    }

}
