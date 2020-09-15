<div class="container p-0 p-sm-1 p-md-2">
    <div class="row">
        <div class="col-12">
            <div class="h5-responsive text-primary mb-3">Select the Document from the left and click "Add Doc" on the corresponding Checklist Item</div>
        </div>
    </div>

    <div class="row">

        <div class="col-12 col-md-5">

            <div class="h5-responsive text-orange">Documents</div>

            <div class="border p-2 rounded">

                <div class="row">

                    <div class="col-12 col-md-10">

                        <div id="add_to_checklist_documents_wrapper" class="list-group">

                            @foreach($documents as $document)

                                <div class="add-to-checklist-document-div list-group-item list-group-item-action border rounded my-2 @if($loop -> first) active z-depth-2 p-4 @else p-2 @endif" data-document-id="{{ $document -> id}}" data-file-name="{{ $document -> file_name_display }}">
                                    <div>
                                        {{ $document -> file_name_display }}
                                    </div>
                                </div>

                            @endforeach


                            {{-- <div id="add_to_checklist_documents_div" class="list-group">

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

                            </div> --}}


                        </div>

                    </div>

                    <div class="d-none d-md-block col-md-2">

                        <div class="doc-list-arrows-div">
                            <button type="button" class="btn btn-primary doc-list-arrow mb-2" data-dir="up"><i class="fa fa-arrow-up fa-2x"></i></button>
                            <br>
                            <button type="button" class="btn btn-primary doc-list-arrow mt-2" data-dir="down"><i class="fa fa-arrow-down fa-2x"></i></button>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-12 col-md-7">

            <div class="h5-responsive text-orange mt-3 mt-sm-0">Checklist Items</div>



            <div id="add_to_checklist_items_wrapper">

                <div id="add_to_checklist_items_div" class="list-group">

                    @foreach($checklist_groups as $checklist_group)

                        {{-- <div class="h5-responsive text-primary mt-1 mb-0">~~~~~~~~~ {{ $checklist_group -> resource_name }} ~~~~~~~~~</div> --}}

                        @if(count($checklist_items -> where('checklist_item_group_id', $checklist_group -> resource_id)) > 0)

                            @foreach($checklist_items -> where('checklist_item_group_id', $checklist_group -> resource_id) as $checklist_item)

                                @php
                                if($checklist_item -> checklist_form_id > 0) {
                                    $checklist_item_name = $checklist_items_model -> GetFormName($checklist_item -> checklist_form_id);
                                } else {
                                    $checklist_item_name = $checklist_item -> checklist_item_added_name;
                                }
                                $status_details = $transaction_checklist_items_modal -> GetStatus($checklist_item ->  id);
                                $docs = $transaction_checklist_item_documents -> where('checklist_item_id', $checklist_item ->  id);
                                $docs_count = $docs -> count();
                                $status = $status_details -> status;
                                $agent_classes = $status_details -> agent_classes;

                                @endphp

                                <div class="list-group-item border rounded mb-3 p-1">

                                    <div class="text-gray d-flex justify-content-between">

                                        <div class="d-flex justify-content-start align-items-center">
                                            <div class="mr-2">
                                                <button type="button" class="btn btn-primary btn-sm assign-button" data-checklist-id="{{ $checklist_item -> checklist_id }}" data-checklist-item-id="{{ $checklist_item ->  id }}"><i class="fa fa-plus mr-2"></i> Add</button>
                                            </div>

                                            <div class="text-primary">
                                                {{ $checklist_item_name }}
                                            </div>
                                        </div>

                                        <div>
                                            <span class="badge checklist-item-badge p-1 {{ $agent_classes }} mr-2">{{ $status }}</span>
                                        </div>

                                    </div>


                                    <div class="submitted-docs-div bg-blue-light p-1 rounded m-1 mx-md-4 @if(count($docs) == 0) hidden @endif">
                                        <div class="d-flex justify-content-start align-items-center">
                                            <div class="mx-3"><i class="fad fa-file-alt fa-lg text-primary"></i></div>
                                            <div class="submitted-docs">
                                                @foreach ($docs as $doc)
                                                    @php
                                                    $document_details = $transaction_documents_model -> GetDocInfo($doc -> document_id);
                                                    $file_name = $document_details['file_name'];
                                                    @endphp
                                                    <div class="d-flex justify-content-start align-items-center small"><div><i class="fad fa-check-circle text-success mr-2"></i></div><div>{{ Str::limit($file_name, 85) }}</div></div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            @endforeach

                        @endif

                    @endforeach

                </div>







                {{-- <div id="add_to_checklist_items_div" class="list-group">

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

                                <div class=" m-1">

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

                </div> --}}

            </div>



        </div>
    </div>
</div>
