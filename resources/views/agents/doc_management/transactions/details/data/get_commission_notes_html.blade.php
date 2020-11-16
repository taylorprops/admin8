@foreach($commission_notes as $commission_note)
    @php
    $user = $users -> where('id', $commission_note -> user_id) -> first();
    $username = $user -> name;
    @endphp
    <li class="list-group-item my-1 mr-2 p-1 bg-blue-light text-primary text-white rounded">
        <div class="small border-bottom">
            {{ date('m/d/Y', strtotime($commission_note -> created_at)) }} - <span class="font-italic">{{ $username }}</span>
        </div>
        <div class="p-2">
            {!! nl2br($commission_note -> notes) !!}
        </div>
    </li>

@endforeach
