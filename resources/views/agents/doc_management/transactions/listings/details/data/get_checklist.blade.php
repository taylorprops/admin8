<div class="h4-responsive text-primary ml-3 mt-2 mb-3"><i class="fad fa-tasks mr-2"></i> Listing Checklist</div>
<table class="table table-bordered">
    <thead>
        <th></th>
        <th></th>
        <th>Document Name</th>
        <th>Status</th>
    </thead>
    <tbody>
    @foreach($items as $item)
        @php $docs = $checklist_docs -> GetDocs($item -> checklist_item_id); @endphp
        <tr>
            <td>{{ $loop -> index + 1 }}</td>
            <td>
                @if($docs)

                @endif
            </td>
            <td>{{ $checklist_items -> GetFormName($item -> checklist_form_id) }}</td>
            <td></td>
        </tr>
    @endforeach
    </tbody>
</table>
