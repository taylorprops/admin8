@php
$checks_count = count($checks_in -> where('active', 'yes'));
$checks_total = 0;
@endphp
@foreach($checks_in as $check)
    @php
    $classes = '';
    if($check -> active == 'yes') {
        $checks_total += $check -> check_amount;
    } else {
        $classes = $check -> active == 'no' ? 'inactive hidden' : '';
    }
    @endphp
    <div class="p-2 mr-2 border bg-white mb-3 z-depth-1 rounded check-image-container {{ $classes }}">
        <div class="row">
            <div class="col-12 col-md-3">
                <div class="list-group">
                    <div class="list-group-item p-1">
                        #{{ $check -> check_number }}
                    </div>
                    <div class="list-group-item p-1">
                        {{ $check -> check_date }}
                    </div>
                    <div class="list-group-item p-1">
                        ${{ $check -> check_amount }}
                    </div>
                </div>
            </div>
            <div class="col-9 col-md-6">
                <div class="check-image-div">
                    <img src="{{ $check -> image_location }}" class="w-100">
                </div>
            </div>
            @if($check -> active == 'yes')
                <div class="col-3">
                    <a href="{{ $check -> file_location }}" target="_blank" class="btn btn-block btn-sm btn-primary"><i class="fad fa-eye mr-1"></i> View</a>
                    <a href="javascript: void(0)"
                    class="btn btn-block btn-sm btn-default edit-check-in-button"
                    data-check-id="{{ $check -> id }}"
                    data-date-received="{{ $check -> date_received }}"
                    data-date-deposited="{{ $check -> date_deposited }}"
                    data-check-date="{{ $check -> check_date }}"
                    data-check-number="{{ $check -> check_number }}"
                    data-check-amount="{{ $check -> check_amount }}"
                    data-image-location="{{ $check -> image_location }}
                    "><i class="fad fa-edit mr-1"></i> Edit</a>
                    <a href="javascript: void(0)" class="btn btn-block btn-sm btn-danger delete-check-in-button" data-check-id="{{ $check -> id }}"><i class="fad fa-trash mr-1"></i> Delete</a>
                </div>
            @else
                <div class="col-3 text-center">
                    <span class="text-danger font-weight-bold">Deleted</span>
                    <a href="javascript: void(0)" class="btn btn-sm btn-default undo-delete-check-in-button" data-check-id="{{ $check -> id }}"><i class="fad fa-undo-alt mr-1"></i> Undo</a>
                </div>
            @endif
        </div>
    </div>

@endforeach

@if($checks_count > 0)
<a href="javascript: void(0)" class="btn btn-sm btn-primary show-deleted-button">Show Deleted Checks</a>
@endif

<input type="hidden" id="checks_total" value="{{ $checks_total }}">
<input type="hidden" id="checks_count" value="{{ $checks_count }}">
