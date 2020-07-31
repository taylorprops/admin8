if(document.URL.match(/document_review/)) {

    $(document).ready(function() {


        $('.property-item').off('click').on('click', function() {
            $('.documents-div').children().addClass('animated bounceOutDown');
            $('.details-div').children().addClass('animated fadeOut');
            global_loading_on('', '<div class="h4-responsive text-white">Loading Checklist Documents...</div>');
            let id = $(this).data('id');
            let type = $(this).data('type');
            get_checklist(id, type);
            get_details(id, type);
            set_property_item_active($(this));
            show_hide_next();
        });

        $('#close_checklist_button').off('click').on('click', close_checklist);

        $('#next_button').off('click').on('click', function() {
            next_property();
        });
    });

    function set_property_item_active(ele) {
        $('.property-item').removeClass('active');
        ele.addClass('active');
    }

    function next_property() {
        let ele = $('.property-item.active');
        let index = ele.index();
        cancel = false;
        let last_index = null;
        $('.property-item').each(function() {
            if(cancel == false) {
                if($(this).index() > index) {
                    $(this).trigger('click');
                    cancel = true;
                    last_index = $(this).index();
                }
            }
        });

        show_hide_next();
    }

    function show_hide_next() {
        if($('.list-group-item.property-item.active').index() == $('.list-group-item.property-item').last().index()) {
            $('#next_button').hide();
        } else {
            $('#next_button').show();
        }
    }

    window.get_checklist = function(id, type) {

        $('#add_checklist_item_modal').remove();

        axios.get('/doc_management/get_checklist', {
            params: {
                id: id,
                type: type
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {

            $('.checklist-items-div').html(response.data);
            $('.checklist-items-container').removeClass('fadeOut').show();

            $('.checklist-item-link').off('click').on('click', function(e) {
                if(!$(e.target).hasClass('fa') && !$(e.target).hasClass('document-link') && !$(e.target).hasClass('active') && !$(e.target).hasClass('text-white')) {
                    $('.checklist-item-link').removeClass('active').find('.checklist-item-name').removeClass('text-white');
                    $(this).addClass('active').find('.checklist-item-name').addClass('text-white');
                    get_documents($(this).data('checklist-item-id'), $(this).data('checklist-item-name'));
                }
            });

            if($('.checklist-item-link.pending').length > 0) {
                $('.checklist-item-link.pending').eq(0).trigger('click');
            } else {
                $('.checklist-item-link').eq(0).trigger('click');
            }

            $('.modal').each(function() {
                $(this).appendTo('body');
            });

            $('.mark-required').off('click').on('click', function() {
                mark_required($(this), $(this).data('checklist-item-id'), $(this).data('required'));
            });

            $('.remove-checklist-item').off('click').on('click', function() {
                show_remove_checklist_item($(this), $(this).data('checklist-item-id'));
            });

            $('.add-checklist-item-button').off('click').on('click', show_add_checklist_item);



            // $('#email_checklist_to_agent_button').off('click').on('click', show_email_agent);



            $('#property_id').val(id);
            $('#property_type').val(type);

            setTimeout(function() {
                global_loading_off();
            }, 1500);

        })
        .catch(function (error) {
            console.log(error);
        });

    }

    function get_documents(checklist_item_id, checklist_item_name) {

        axios.get('/doc_management/get_documents', {
            params: {
                checklist_item_id: checklist_item_id,
                checklist_item_name: checklist_item_name
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {

            $('.checklist-item-docs-div').remove();
            $('.documents-div').html(response.data).show();
            // add documents to checklist item and open it
            $('.checklist-item-docs-div').appendTo('.list-group-item.checklist-item-link.active')
                .find('.document-link').off('click').on('click', function() {
                    let id = $(this).data('document-id');
                    $('.review-image-container').animate({
                        scrollTop: $('#document_' + id).offset().top - 70
                    },'fast');
                });

            $('.accept-checklist-item-button, .reject-checklist-item-button, .undo-accepted, .undo-rejected').data('checklist-item-id', checklist_item_id);

            $('.accept-checklist-item-button').off('click').on('click', function() {
                checklist_item_review_status($(this), 'accepted', null);
            });
            $('.reject-checklist-item-button').off('click').on('click', function() {
                show_checklist_item_review_status($(this), 'rejected');
            });

            $('.undo-accepted, .undo-rejected').off('click').on('click', function() {
                checklist_item_review_status($(this), 'not_reviewed', null);
            });

            $('#zoom').on('input change', zoom);

        })
        .catch(function (error) {
            console.log(error);
        });

    }


    function get_details(id, type) {

        axios.get('/doc_management/get_details', {
            params: {
                id: id,
                type: type
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {
            $('.details-div').html(response.data);
        })
        .catch(function (error) {
            console.log(error);
        });

    }

    function close_checklist() {
        $('.checklist-items-container').addClass('animated fadeOut').hide();
        $('.documents-div').children().addClass('animated bounceOutDown');
        $('.documents-div').html('<div class="h1-responsive text-primary w-100 text-center mt-5 pt-5"><i class="fa fa-arrow-left mr-2"></i> To Begin Select A Property</div>');
        $('.details-div').children().addClass('animated fadeOut');
    }

    function zoom() {
        let z = $(this).val();
        $('.review-image-div').css({ width: z+'%' });
        if(z > 100) {
            $('.document-options-div').css({ bottom: '12px' });
        } else {
            $('.document-options-div').css({ bottom: '0px' });
        }
    }

}
