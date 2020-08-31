<div class="container p-0 p-sm-1 p-md-2">
    <div class="row">
        <div class="col-12">
            <div class="h5-responsive text-primary mb-3">Drag Documents to The Checklist Items</div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-sm-5">

            <div class="h5-responsive text-orange">Documents</div>

            <div class="p-2 border">

                <div id="add_to_checklist_documents_wrapper">

                    <div id="add_to_checklist_documents_div" class="list-group">

                        @foreach($documents as $document)
                            <div class="add-to-checklist-document-div list-group-item my-1 border rounded" data-document-id="{{ $document -> id}}" data-file-name="{{ $document -> file_name_display }}">
                                <div class="d-flex justify-content-start align-items-center text-gray">
                                    <div>
                                        <i class="fal fa-bars fa-lg mr-3"></i>
                                    </div>
                                    <div>
                                        {{ $document -> file_name_display }}
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>

                </div>

            </div>

        </div>

        <div class="col-12 col-sm-7">

            <div class="h5-responsive text-orange mt-3 mt-sm-0">Checklist Items</div>

            <div class="p-2 border">

                <div id="add_to_checklist_items_wrapper">

                    <div id="add_to_checklist_items_div" class="list-group">

                        @foreach($checklist_groups as $checklist_group)

                            <div class="h5-responsive text-primary mt-1 mb-0">~~~~~~~~~ {{ $checklist_group -> resource_name }} ~~~~~~~~~</div>

                            @if(count($checklist_items -> where('checklist_item_group_id', $checklist_group -> resource_id)) > 0)

                                @foreach($checklist_items -> where('checklist_item_group_id', $checklist_group -> resource_id) as $checklist_item)

                                    @php
                                    if($checklist_item -> checklist_form_id > 0) {
                                        $checklist_item_name = $checklist_items_model -> GetFormName($checklist_item -> checklist_form_id);
                                    } else {
                                        $checklist_item_name = $checklist_item -> checklist_item_added_name;
                                    }
                                    $status_details = $transaction_checklist_items_modal -> GetStatus($checklist_item ->  id);
                                    $docs_count = $transaction_checklist_item_documents -> where('checklist_item_id', $checklist_item ->  id) -> count();
                                    $status = $status_details -> status;
                                    $agent_classes = $status_details -> agent_classes;
                                    $fa = str_replace('mr-2', 'mr-1', $status_details -> fa);
                                    $helper_text = $status_details -> helper_text;
                                    @endphp

                                    <div class="{{-- bg-primary-light --}} m-1">

                                        <div class="font-weight-bold drop-div-title text-gray mb-1 d-flex justify-content-start">
                                            <div class="d-none">
                                                <span class="badge checklist-item-badge {{ $agent_classes }} p-1 mr-2" title="{{ $helper_text }}">{!! $fa !!} {{ $status }}</span>
                                            </div>
                                            <div class="d-none">
                                                <span class="badge badge-primary p-1 mr-2" title="Count of documents already submitted for this item">{{ $docs_count }}</span>
                                            </div>
                                            <div>
                                                {{ $checklist_item_name }}
                                            </div>
                                        </div>
                                        <div class="add-to-checklist-item-div" data-checklist-id="{{ $checklist_item -> checklist_id }}" data-checklist-item-id="{{ $checklist_item -> id }}"data-file-name="{{ $checklist_item_name }}">
                                            <div class="checklist-item-droparea rounded p-0">
                                            </div>
                                        </div>

                                    </div>
                                @endforeach

                            @endif

                        @endforeach

                    </div>

                </div>

            </div>

        </div>
    </div>
</div>
