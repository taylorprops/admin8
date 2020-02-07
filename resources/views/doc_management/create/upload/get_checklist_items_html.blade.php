@php
$in_checklist = $checklist_items -> ifFormInChecklist($checklist_id, $file_id);
@endphp
@if($in_checklist === false)
    <li class="list-group-item order-checklist-item order-checklist-item-sortable p-1 d-flex justify-content-start bg-orange-light">
        <i class="fad fa-arrows-v text-primary order-checklist-item-sortable-handle mx-3 my-auto"></i>
        <span class="my-auto order-checklist-item-sortable-handle">{{ $uploaded_file -> file_name_display }}</span>
    </li>
@endif
@foreach($checklist_groups as $checklist_group)

    <li class="list-group-header py-2 pl-2 mb-0 font-weight-bold" data-form-group-id="{{ $checklist_group -> resource_id }}">{{ $checklist_group -> resource_name }}</li>

    @php
    $checklist_group_items = $checklist_items -> getChecklistItemsByGroup($checklist_id, $checklist_group -> resource_id);
    @endphp

    @if($checklist_group_items -> count() > 0)

        @foreach($checklist_group_items as $checklist_item)

            @php
            $handle = '';
            $classes = '';
            $fa = '';
            $text_color = '';
            $mr = 'mr-4';
            if($checklist_item -> checklist_form_id == $file_id) {
                $fa = 'fa-arrows-v';
                $handle = 'order-checklist-item-sortable-handle';
                $classes = 'order-checklist-item-sortable bg-orange-light';
                $text_color = 'text-primary';
                $mr = 'mr-3';
            }
            @endphp
            <li class="list-group-item order-checklist-item p-1 d-flex justify-content-start {{ $classes }}">
                <i class="fad {{ $fa }} {{ $text_color }} {{ $handle }} {{ $mr }} my-auto ml-3"></i>
                <span class="{{ $handle }} my-auto">{{ $upload -> getFormName($checklist_item -> checklist_form_id) }}</span>
            </li>
        @endforeach

    @endif

@endforeach



