<div class="row animated fadeIn">

    <div class="col-12">

        <div class="list-group">

            @foreach($checklist_groups as $checklist_group)

                <div class="list-group-item d-flex justify-content-start align-items-center border-left-0">
                    <div>
                        <div class="h5-responsive text-orange">{{ $checklist_group -> resource_name }}</div>
                    </div>
                    <div class="ml-3">
                        <button type="button" class="btn btn-success btn-sm add-checklist-item-button" data-toggle="tooltip" data-group-id="{{ $checklist_group -> resource_id }}" title="Add Checklist Item"><i class="fal fa-plus"></i></button>
                    </div>
                </div>

                @foreach($checklist_items -> where('checklist_item_group_id', $checklist_group -> resource_id) as $checklist_item)

                    @php
                    if($checklist_item -> checklist_form_id > 0) {
                        $checklist_item_name = $files -> GetFormName($checklist_item -> checklist_form_id);
                    } else {
                        $checklist_item_name = $checklist_item -> checklist_item_added_name;
                    }

                    $status_details = $transaction_checklist_items -> GetStatus($checklist_item -> id);
                    $status = $status_details -> status;
                    $admin_classes = $status_details -> admin_classes;
                    $fa = $status_details -> fa;

                    $show_mark_required = $status_details -> show_mark_required;
                    $show_mark_not_required = $status_details -> show_mark_not_required;

                    $checklist_item_id = $checklist_item -> id;

                    $notes = $transaction_checklist_item_notes -> where('checklist_item_id', $checklist_item -> id);
                    if(count($notes) == 0) {
                        $notes = null;
                    }
                    @endphp

                    <div class="list-group-item p-1 checklist-item-link @if($status == 'Pending') pending @elseif($status == 'Required') required @endif" data-checklist-item-id="{{ $checklist_item_id }}" data-checklist-item-name="{{ $checklist_item_name }}">

                        <div class="d-flex justify-content-between align-items-center checklist-item-div">

                            <div class="d-flex justify-content-between align-items-center">

                                <div class="dropdown">

                                    <button class="btn btn-primary dropdown-toggle checklist-item-dropdown py-0 px-2" type="button" id="checklist_item_options_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>

                                    <div class="dropdown-menu dropdown-primary">

                                        <a class="dropdown-item mark-required no @if(!$show_mark_not_required) d-none @else d-block @endif" href="javascript: void(0)" data-checklist-item-id="{{ $checklist_item_id }}" data-required="no">Make If Applicable</a>

                                        <a class="dropdown-item mark-required yes @if(!$show_mark_required) d-none @else d-block @endif" href="javascript: void(0)" data-checklist-item-id="{{ $checklist_item_id }}" data-required="yes">Make Required</a>

                                        <a class="dropdown-item remove-checklist-item" href="javascript: void(0)" data-checklist-item-id="{{ $checklist_item_id }}">Remove</a>

                                    </div>

                                </div>

                                <div class="mx-2">
                                    <a href="javascript:void(0)" class="checklist-item-name text-gray" data-checklist-item-id="{{ $checklist_item_id }}" data-checklist-item-name="{{ $checklist_item_name }}">{{ $checklist_item_name }}</a>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="mr-2 relative">
                                    @if($notes)
                                    <span class="badge badge-pill bg-orange text-white unread-messages-badge" data-toggle="tooltip" title="{{ count($notes) }} Unread Messages">{{ count($notes) }}</span>
                                    @endif
                                </div>
                                <div class="status-badge badge {{ $admin_classes }} p-2">
                                    {!! $status !!}
                                </div>
                            </div>
                        </div>

                    </div>

                @endforeach


            @endforeach

        </div>

    </div>

</div>

<input type="hidden" id="Listing_ID" value="{{ $property -> Listing_ID }}">
<input type="hidden" id="Contract_ID" value="{{ $property -> Contract_ID }}">
<input type="hidden" id="Referral_ID" value="{{ $property -> Referral_ID }}">
<input type="hidden" id="Agent_ID" value="{{ $property -> Agent_ID }}">
<input type="hidden" id="transaction_type" value="{{ $transaction_type }}">
<input type="hidden" id="property_id">
<input type="hidden" id="property_type">


@include('/agents/doc_management/transactions/details/shared/checklist_review_modals')
