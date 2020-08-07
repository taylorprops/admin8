<hr>
<h3 class="text-orange" style="margin-top: 25px; margin-bottom: 0px;">Checklist Details</h3>
<table width="600">

    @foreach($checklist_groups as $checklist_group)
        <tr>
            <td colspan="2">
                <h4 class="@if(!$loop -> first) mt-3 @endif">{{ $checklist_group -> resource_name }}</h4>
            </td>
        </tr>

        @foreach($transaction_checklist_items -> where('checklist_item_group_id', $checklist_group -> resource_id) as $transaction_checklist_item)

            @php
            $transaction_checklist_item_name = $transaction_checklist_item -> checklist_item_added_name;
            if($transaction_checklist_item -> checklist_form_id > 0) {
                $transaction_checklist_item_name = $checklist_items_model -> GetFormName($transaction_checklist_item -> checklist_form_id);
            }
            $transaction_checklist_item_id = $transaction_checklist_item -> id;
            $status_details = $transaction_checklist_items_model -> GetStatus($transaction_checklist_item_id);
            $status = $status_details -> status;
            $badge_class = $status_details -> badge_class;

            $notes = null;
            if($status != 'Complete' && $status != 'Pending') {
                $notes = $transaction_checklist_item_notes -> where('checklist_item_id', $transaction_checklist_item_id) -> where('note_status', 'unread') -> orderBy('created_at', 'DESC') -> first();
            }
            @endphp
            @if($status != 'If Applicable')
                <tr>
                    <td width="15%">
                        <div class="{{ $badge_class }}">
                            {{ $status }}
                        </div>
                    </td>
                    <td style="padding-left: 10px;">
                        {{ $transaction_checklist_item_name }}
                    </td>
                </tr>
                <tr>
                    <td width="15%" style="border-bottom: 1px dotted #ccc;"></td>
                    <td style="border-bottom: 1px dotted #ccc; padding-left: 10px;">
                        @if($notes)
                            <div style="color: rgb(163, 80, 80); font-size: 13px;"> {!! $notes -> notes !!} </div>
                        @endif
                    </td>
                </tr>
            @endif
        @endforeach
    @endforeach
</table>

