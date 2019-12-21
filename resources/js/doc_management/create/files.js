const axios = require('axios');
import { form_elements, select_refresh, validate_form } from '@/form_elements.js';

if (document.URL.match(/create\/upload\/files/)) {

    function get_forms(association_id, state) {
        let options = {
            params: {
                association_id: association_id,
                state: state
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        }

        axios.get('/doc_management/get_association_files', options)
            .then(function (response) {
                $('#association_' + association_id + '_files').html($(response.data));
                $('#association_' + association_id + '_file_count').text($('#files_count').val());

                setTimeout(function() {
                    $('.edit-upload').off('click').on('click', function () {
                        edit_upload($(this));
                    });

                    $('.duplicate-upload').off('click').on('click', function () {
                        duplicate_upload($(this));
                    });

                    $('.delete-upload').off('click').on('click', function () {
                        confirm_delete_upload($(this));
                    });
                }, 500);
            })
            .catch(function (error) {

            });
    }

    function duplicate_upload(ele) {
        let upload_id = ele.data('id');
        let association_id = ele.data('association-id');
        let state = ele.data('state');
        let formData = new FormData();
        formData.append('upload_id', upload_id);
        axios.post('/doc_management/duplicate_upload', formData)
            .then(function (response) {
                get_forms(association_id, state);
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    function confirm_delete_upload(ele) {

        let upload_id = ele.data('id');
        let association_id = ele.data('association-id');
        let state = ele.data('state');

        $('#confirm_delete_modal').modal();

        $('#confirm_delete').click(function () {
            delete_upload(upload_id, association_id, state);
        });
    }

    function delete_upload(upload_id, association_id, state) {
        let formData = new FormData();
        formData.append('upload_id', upload_id);
        axios.post('/doc_management/delete_upload', formData)
            .then(function (response) {
                $('#confirm_delete_modal').modal('hide');
                get_forms(association_id, state);
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    function edit_upload(ele) {

        let upload_id = ele.data('id');

        axios.get('/doc_management/get_upload_details', {
            params: {
                upload_id: upload_id
            },
        })
            .then(function (response) {

                let file_name = response.data.file_name_display;
                let association_id = response.data.association_id;
                let state = response.data.state;
                let sale_type = response.data.sale_type;

                $('#edit_file_name_display').val(file_name).trigger('change');
                $('#edit_association_id').val(association_id);
                $('#edit_state').val(state);
                sale_type = sale_type.split(',');
                $.each(sale_type, function (i, e) {
                    $('#edit_sale_type option[value="' + e + '"]').prop('selected', true);
                });
                $('#edit_sale_type').trigger('change');

                $('#edit_file_id').val(upload_id);
                setTimeout(function() {
                    select_refresh();
                }, 500);

                $('#edit_file_modal').modal();

                $('#save_edit_file_button').click(save_edit_file);

            })
            .catch(function (error) {
                console.log(error);
            });
    }

    function save_edit_file() {

        let form_check = validate_form($('#edit_file_form'));

        if (form_check == 'yes') {

            let association_id = $('#edit_association_id').val();
            let state = $('#edit_state').val();

            $('#save_edit_file_button').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving');

            let formData = new FormData($('#edit_file_form')[0]);

            axios.post('/doc_management/save_file_edit', formData)
                .then(function (response) {
                    $('#edit_file_modal').modal('hide');
                    get_forms(association_id, state);
                    $('#save_edit_file_button').prop('disabled', false).html('<i class="fad fa-upload mr-2"></i> Save Details');
                })
                .catch(function (error) {
                    //console.log(error);
                });

        }

    }

    function show_upload(ele) {

        $('#add_upload_modal').modal();

        let state = ele.data('state');
        let association_id = ele.data('association-id');
        $('#association_id').val(association_id);
        $('#state').val(state);

        select_refresh();

        setTimeout(function () {
            $('.file-path').bind('change', function () {
                let form_name = $('.file-path').val().replace(/\.pdf/, '');
                $('#file_name_display').val(form_name).trigger('change');
            });
            $('#association_id').change(function () {
                $('#state').val(ele.find('option:selected').data('state'));
                select_refresh();
            });
        }, 500);
    }

    function upload_file() {
        let form_check = validate_form($('#upload_file_form'));

        if (form_check == 'yes') {

            let association_id = $('#association_id').val();
            let state = $('#state').val();

            $('#upload_file_button').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Uploading');

            let formData = new FormData($('#upload_file_form')[0]);

            axios_options['header'] = { 'content-type': 'multipart/form-data' };
            axios.post('/doc_management/upload_file', formData, axios_options)
                .then(function (response) {
                    $('#add_upload_modal').modal('hide');
                    $('#file_name_display, #file_upload').val('').trigger('change');
                    get_forms(association_id, state);
                    $('#upload_file_button').prop('disabled', false).html('<i class="fad fa-upload mr-2"></i> Upload Form');
                })
                .catch(function (error) {
                    //console.log(error);
                });
        }
    }


    $(document).ready(function () {

        form_elements();

        // Add file modal
        $('.upload-file-button').click(function () {
            show_upload($(this));
        });

        $('.edit-upload').click(function () {
            edit_upload($(this));
        });

        $('.duplicate-upload').click(function () {
            duplicate_upload($(this));
        });

        $('.delete-upload').click(function () {
            confirm_delete_upload($(this));
        });


        $('#upload_file_button').click(upload_file);

    });

}
