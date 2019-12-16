$(document).ready(function() {

    $('#upload_file_form').submit(function (e) {
        e.preventDefault();
        $('#upload_file_button').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Uploading');
        var formData = new FormData($(this)[0]);
        $.ajax({
            url: '/upload_file',
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                window.location = '/create/upload/files';
            }
        });
    });
});