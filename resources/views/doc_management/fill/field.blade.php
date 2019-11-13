@php
$class= 'fillable-field-container';

if($field['field_type'] == 'number') {
    // hide written field since automatically filled in after numeric typed in
    if($field['number_type'] == 'written') {
        $class = 'fillable-field-container-hidden';
    }
} else if($field['field_type'] == 'date') {
    $class = 'fillable-field-container field-date';
}
@endphp
<div class="field-div {{ $class }}"
    style="position: absolute;
    top: {{ $field['top'] }}px;
    left: {{ $field['left'] }}px;
    height: {{ $field['height'] }}px;
    width: {{ $field['width'] }}px;"
    id="field_{{ $field['field_id'] }}"
    data-fieldid="{{ $field['field_id'] }}"
    data-groupid="{{ $field['group_id'] }}"
    data-type="{{ $field['field_type'] }}"
    data-page="{{ $field['page'] }}"
    data-x="{{ $field['left'] }}"
    data-y="{{ $field['top'] }}"
    data-h="{{ $field['height'] }}"
    data-w="{{ $field['width'] }}"
    data-xp="{{ $field['left_perc'] }}"
    data-yp="{{ $field['top_perc'] }}"
    data-hp="{{ $field['height_perc'] }}"
    data-wp="{{ $field['width_perc'] }}">


    @if($field['field_type'] == 'date')

    <input type="text" class="datepicker pl-2" name="field_{{ $field['field_id'] }}" style="height: {{ $field['height'] }}px;">

    @elseif($field['field_type'] == 'radio')

    @elseif($field['field_type'] == 'checkbox')

    @else

    <div class="edit-properties-div shadow card">

        <div class="form-div">

            <div class="card-header bg-secondary text-white h4 p-2">{{ strtoupper($field['field_name_display']) }}</div>
            <div class="bg-secondary-light text-orange p-2">{{ $field['helper_text'] }}</div>
            <div class="card-body p-1">
                <div class="container">

                    @if($field['field_type'] == 'textline')



                    @elseif($field['field_type'] == 'address')



                    @elseif($field['field_type'] == 'name')

                    <div class="row p-3 border border-secondary mb-1">

                        @foreach($field_inputs as $input)

                        @if($field['field_id'] == $input['field_id'])

                        <div class="col-12">
                            <div class="font-weight-bold text-secondary">{{ $input['input_helper_text'] }}</div>
                            <div class="md-form my-1">
                                <input type="text" class="form-control" id="input_{{ $input['id'] }}">
                                <label for="input_{{ $input['id'] }}">{{ $input['input_name'] }}</label>
                            </div>
                        </div>

                        @endif

                        @endforeach

                    </div>

                    @elseif($field['field_type'] == 'number')



                    @endif

                </div> <!-- end container -->
            </div> <!-- end card-body -->
            <div class="card-footer d-flex justify-content-around bg-white">
                <button href="javascript: void(0);" class="btn btn-success btn-sm shadow field-save-properties" data-groupid="{{ $field['group_id'] }}" data-type="{{ $field['field_type'] }}">Save</button>
                <button href="javascript:void(0);" class="btn btn-danger btn-sm field-close-properties">Cancel</button>
            </div>

        </div> <!-- end form-div -->
    </div> <!-- end edit-properties-div -->

    @endif

</div>
