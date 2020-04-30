
$(document).ready(function() {

    $(document).on('click', '.add-document-button', show_add_document);

    $(document).on('click', '.select-document-button', save_add_document);

});



function show_add_document() {
    $('#add_document_checklist_id').val($(this).data('checklist-id'));
    $('#add_document_checklist_item_id').val($(this).data('checklist-item-id'));
    $('#add_document_modal').modal();
}

function save_add_document() {
    let document_id = $(this).data('document-id');
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
    })
    .catch(function (error) {
        console.log(error);
    });

}
