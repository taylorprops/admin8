if(document.URL.match(/document_review/)) {

    $(document).ready(function() {
        $('#test_button').click(function() {
            $('.checklist-items-container').animate({
                width: '100%'
            });
        });
    });

}
