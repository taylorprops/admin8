
    @php
    $in_checklist = $checklist_items -> ifFormInChecklist($checklist_id, $file_id);
    $add = 0;
    @endphp
    @if($in_checklist === false)
        @php $add = 1;
        @endphp
        <li class="list-group-item order-checklist-item order-checklist-item-sortable d-flex justify-content-start bg-orange-light">
            <i class="fad fa-arrows-v fa-lg text-primary order-checklist-item-sortable-handle mr-3 mt-2"></i>
            <span class="chip blue text-white checklist-item-order order-checklist-item-sortable-handle">1</span>
            <span class="mt-2 order-checklist-item-sortable-handle">{{ $uploaded_file -> file_name_display }}</span>
        </li>
    @endif
    @foreach($items as $item)
        @php
        $handle = '';
        $classes = '';
        $fa = '';
        $text_color = '';
        $mr = 'mr-4';
        if($item -> checklist_form_id == $file_id) {
            $fa = 'fa-arrows-v';
            $handle = 'order-checklist-item-sortable-handle';
            $classes = 'order-checklist-item-sortable bg-orange-light';
            $text_color = 'text-primary';
            $mr = 'mr-3';
        }
        @endphp
        <li class="list-group-item order-checklist-item d-flex justify-content-start {{ $classes }}">
            <i class="fad {{ $fa }} fa-lg {{ $text_color }} {{ $handle }} {{ $mr }} mt-2"></i>
            <span class="chip blue text-white checklist-item-order {{ $handle }}">{{ $loop -> iteration + $add }}</span>
            <span class="mt-2 {{ $handle }}">{{ $upload -> getFormName($item -> checklist_form_id) }}</span>
        </li>
    @endforeach

