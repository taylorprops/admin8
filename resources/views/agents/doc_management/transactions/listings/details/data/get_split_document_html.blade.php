<div class="container-fluid p-0 p-sm-2 pb-4">

    <div class="row p-0 p-sm-2">
        <div class="col-12 col-lg-8">
            <div class="row">
                <div class="col-12">
                    <div class="h3 text-orange">Step 1</div>
                    <div class="h5 text-gray">Split combined documents by adding pages from the "Available Documents" section to the "Selected Documents" section.</div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="h5 text-primary my-2">Available Documents</div>
                    <div class="image-container border p-2">
                        <div class="image-slider">
                            @foreach($document_images as $document_image)
                                <div class="image-holder m-2 border z-depth-1" data-index="{{ $loop -> index }}" data-document-image-id="{{ $document_image -> id }}" data-file-type="{{ $file_type }}">
                                    <div class="image-options-div w-100">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <button type="button" class="btn btn-sm btn-primary add-to-selected-button">Add</button>
                                                <button type="button" class="btn btn-sm btn-danger remove-from-selected-button"><i class="fa fa-times text-white"></i></button>
                                            </div>
                                            <span class="badge badge-primary image-order mr-2 p-2"></span>
                                        </div>
                                        <div class="image-zoom-div">
                                            <a href="javascript:void(0)" class="btn btn-sm bg-white image-zoom-button" data-image-src="{{ $document_image -> file_location }}"><i class="far fa-search-plus"></i></a>
                                        </div>
                                    </div>
                                    <img src="{{ $document_image -> file_location }}" class="document-image">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="h5 text-primary my-2">Selected Documents</div>
                    <div class="selected-images-container">
                        <div class="selected-images-slider"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="row">
                <div class="col-12">
                    <div class="h3 text-orange">Step 2</div>
                    <div class="h5 text-gray">Add the "Selected Documents" to Checklist Item or Save to Documents</div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="mt-3">

                        <ul class="nav nav-tabs nav-justified md-tabs bg-primary px-3" id="document_options_tabs_nav" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="add_to_checklist_item_button" data-toggle="tab" href="#add_to_checklist_item_div" role="tab" aria-controls="add_to_checklist_item_div" aria-selected="true">Add To Checklist</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="save_to_documents_button" data-toggle="tab" href="#save_to_documents_div" role="tab" aria-controls="save_to_documents_div" aria-selected="false">Save To Documents</a>
                            </li>
                        </ul>
                        <div class="tab-content card p-2 pt-5 mt-n3" id="document_options_tabs">

                            <div class="tab-pane fade show active" id="add_to_checklist_item_div" role="tabpanel" aria-labelledby="add_to_checklist_item_button">

                                <div id="add_to_checklist_item_div">

                                    <div class="h5 text-orange">
                                        Add To Checklist Item
                                        <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Add To Checklist Item" data-content="Click 'Add' to add the 'Selected Documents' to that checklist item. This will merge the 'Selected Documents' into one document, save them to your Documents and assign the merged document to the checklist item for review"><i class="fad fa-question-circle ml-2"></i></a>
                                    </div>

                                    <div class="list-group add-to-checklist-item-list-group">

                                        @foreach($checklist_groups as $checklist_group)

                                            <div class="h5 responsive text-gray mt-3">{{ $checklist_group -> resource_name }}</div>

                                            @if(count($checklist_items -> where('checklist_item_group_id', $checklist_group -> resource_id)) > 0)

                                                @foreach($checklist_items -> where('checklist_item_group_id', $checklist_group -> resource_id) as $checklist_item)

                                                    @php
                                                    $checklist_item_name = $checklist_items_model -> GetFormName($checklist_item -> checklist_form_id);
                                                    $status_details = $transaction_checklist_items_modal -> GetStatus($checklist_item ->  id);
                                                    $docs_count = $transaction_checklist_item_documents -> where('checklist_item_id', $checklist_item ->  id) -> count();
                                                    $status = $status_details -> status;
                                                    $classes = $status_details -> classes;
                                                    $fa = str_replace('mr-2', 'mr-1', $status_details -> fa);
                                                    $helper_text = $status_details -> helper_text;
                                                    @endphp
                                                    <div class="list-group-item p-1 mb-2 d-flex justify-content-start align-items-center" data-checklist-item-id="{{ $checklist_item -> id }}">
                                                        <div class="mr-2">
                                                            <button class="btn btn-success add-docs-to-checklist-item-button" data-checklist-item-id="{{ $checklist_item -> id }}" disabled><i class="fa fa-plus mr-2"></i> Add</button>
                                                        </div>
                                                        <div class="mr-2">
                                                            <span class="badge checklist-item-badge {{ $classes }} p-1 mr-2" title="{{ $helper_text }}">{!! $fa !!} {{ $status }}</span>
                                                        </div>
                                                        <div class="mr-2">
                                                            <span class="badge badge-primary p-1 mr-2" title="Count of documents already submitted for this item">{{ $docs_count }}</span>
                                                        </div>
                                                        <div class="font-weight-bold text-gray">
                                                            {{ $checklist_item_name }}
                                                        </div>

                                                    </div>
                                                @endforeach

                                            @endif

                                        @endforeach

                                    </div>

                                </div>

                            </div>
                            <div class="tab-pane fade" id="save_to_documents_div" role="tabpanel" aria-labelledby="save_to_documents_button">

                                <div id="save_to_documents_div">

                                    <div class="h5 text-orange">
                                        Save To Documents
                                        <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Save To Documents" data-content="This will save the 'Selected Documents' as a PDF in your Documents"><i class="fad fa-question-circle ml-2"></i></a>
                                    </div>

                                    <div class="mt-3 px-4">
                                        <div style="font-weight-bold text-primary">Enter Document Name</div>
                                        <form id="document_name_form">
                                            <input type="text" class="custom-form-element form-input required" id="document_name" data-label="Document Name">
                                            <div class="w-100 text-center">
                                                <button type="button" class="btn btn-success" id="save_document_name_button" disabled><i class="fa fa-save mr-2"></i> Save Document</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>




