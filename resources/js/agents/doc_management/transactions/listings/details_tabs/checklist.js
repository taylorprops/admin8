const { active } = require("sortablejs");

$(document).ready(function() {


    $(document).on('click', '.add-document-button', show_add_document);



    $(document).on('click', '.view-docs-button', toggle_view_docs_button);

    $(document).on('click', '.view-notes-button', toggle_view_notes_button);

    $(document).on('click', '.delete-doc-button', show_delete_doc);

    $(document).on('click', '.add-notes-button', show_add_notes);

    $(document).on('click', '.save-notes-button', function() {
        save_add_notes($(this));
    });

    $(document).on('click', '.mark-read-button', mark_note_read);


});


function mark_note_read() {
    let note_id = $(this).data('note-id');
    let notes_collapse = $(this).data('notes-collapse');

    let formData = new FormData();
    formData.append('note_id', note_id);
    axios.post('/agents/doc_management/transactions/listings/mark_note_read', formData, axios_options)
    .then(function (response) {
        load_tabs('checklist');
        setTimeout(function() {
            $('#'+notes_collapse).collapse('show');
        }, 500);
    })
    .catch(function (error) {
        console.log(error);
    });
}

function show_add_notes() {
    let add_notes_div = $('#'+$(this).data('add-notes-div'));
    add_notes_div.find('textarea').focus().trigger('click');
}

window.save_add_notes = function(ele) {

    let textarea = $('#'+ele.data('textarea'));
    let notes_collapse = textarea.data('notes-collapse');
    let notes = textarea.val();
    if(notes == '') {
        $('#modal_danger').modal().find('.modal-body').html('You must enter comments before saving');
        return false;
    }
    let checklist_id = textarea.data('checklist-id');
    let checklist_item_id = textarea.data('checklist-item-id');


    let formData = new FormData();
    formData.append('notes', notes);
    formData.append('checklist_id', checklist_id);
    formData.append('checklist_item_id', checklist_item_id);
    formData.append('Listing_ID', $('#Listing_ID').val());
    formData.append('Agent_ID', $('#Agent_ID').val());
    axios.post('/agents/doc_management/transactions/listings/add_notes_to_checklist_item', formData, axios_options)
    .then(function (response) {
        toastr['success']('Comments Successfully Added');
        load_tabs('checklist');
        setTimeout(function() {
            $('#'+notes_collapse).collapse('show');
        }, 500);

    })
    .catch(function (error) {
        console.log(error);
    });
}

function show_delete_doc() {
    let item = $(this);
    let document_id = item.data('document-id');
    let active_collapse = $(this).data('target');
    $('#confirm_delete_checklist_item_doc_modal').modal();
    $('#delete_checklist_item_doc_button').off('click').on('click', function () {
        delete_doc(document_id, active_collapse);
    });
}
function delete_doc(document_id, active_collapse) {
    let formData = new FormData();
    formData.append('document_id', document_id);
    axios.post('/agents/doc_management/transactions/listings/remove_document_from_checklist_item', formData, axios_options)
    .then(function (response) {
        toastr['success']('Document Removed From Checklist');
        load_tabs('checklist');
        setTimeout(function() {
            if($(active_collapse).find('.document-row').length > 0) {
                $(active_collapse).collapse('show');
            }
        }, 500);
    })
    .catch(function (error) {
        console.log(error);
    });
}

function toggle_view_docs_button() {
    $('.documents-collapse.show').not($(this).data('target')).collapse('hide');
}
function toggle_view_notes_button() {
    $('.notes-collapse.show').not($(this).data('target')).collapse('hide');
}

function show_add_document() {
    $('#add_document_checklist_id').val($(this).data('checklist-id'));
    $('#add_document_checklist_item_id').val($(this).data('checklist-item-id'));
    let active_collapse = $(this).data('target');
    $('.select-document-button').off('click').on('click', function( ){
        save_add_document($(this).data('document-id'), active_collapse);
    });
    $('#add_document_modal').modal();
}

function save_add_document(document_id, active_collapse) {

    let checklist_id = $('#add_document_checklist_id').val();
    let checklist_item_id = $('#add_document_checklist_item_id').val();
    let Listing_ID = $('#Listing_ID').val();
    let Agent_ID = $('#Agent_ID').val();
    let formData = new FormData();
    formData.append('document_id', document_id);
    formData.append('checklist_id', checklist_id);
    formData.append('checklist_item_id', checklist_item_id);
    formData.append('Listing_ID', Listing_ID);
    formData.append('Agent_ID', Agent_ID);
    axios.post('/agents/doc_management/transactions/listings/add_document_to_checklist_item', formData, axios_options)
    .then(function (response) {
        $('#add_document_modal').modal('hide');
        toastr['success']('Document Added To Checklist');
        load_tabs('checklist');
        setTimeout(function() {
            $('#'+active_collapse).collapse('show');
        }, 500);
    })
    .catch(function (error) {
        console.log(error);
    });

}
