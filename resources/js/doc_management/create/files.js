const axios = require('axios');
$(document).ready(function () {
    if (document.URL.match(/upload\/files/)) {
        $('.delete-upload').click(function () {
            var file_id = $(this).data('file-id');
            axios.post('/delete_upload', {
                file_id: file_id
            })
                .then(function (response) {
                    $('.alert[data-file-id="' + file_id + '"]').fadeOut('slow');
                })
                .catch(function (error) {
                    console.log(error);
                });
        })

    }
});