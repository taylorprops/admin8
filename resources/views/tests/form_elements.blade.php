@extends('layouts.main')
@section('title', 'title here')

@section('content')

<style>
    .sample-form-div {
        border: 1px solid #ccc;
        padding: 10px;
    }
</style>
<div class="container mt-5">
    <div class="row">
        <div class="col-4">
            <div class="my-3 mx-2 sample-form-div">
                <div class="text-primary font-weight-bold mt-2 mb-2">Input</div>
                <input type="text" class="custom-form-element form-input" data-label="Input">
                <div class="mt-3 border p-2">
                    <div class="text-primary font-weight-bold mt-2 mb-2">Code</div>
                    <code></code>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="my-3 mx-2 sample-form-div">
                <div class="text-primary font-weight-bold mt-2 mb-2">File Upload</div>
                <input type="file" id="test_file" class="custom-form-element form-input-file custom-file-input" data-label="File Upload">
                <div class="mt-3 border p-2">
                    <div class="text-primary font-weight-bold mt-2 mb-2">Code</div>
                    <code></code>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="my-3 mx-2 sample-form-div">
                <div class="text-primary font-weight-bold mt-2 mb-2">Select - Disabled</div>
                <select class="custom-form-element form-select form-select-no-search" disabled data-label="Select - Disabled">
                    <option value=""></option>
                    <option value="Pizza">Pizza</option>
                    <option value="Crabs" selected>Crabs</option>
                    <option value="Burgers">Burgers</option>
                    <option value="Noodles">Noodles</option>
                    <option value="Beer">Beer</option>
                    <option value="Booze">Booze</option>
                    <option value="Crackers">Crackers</option>
                    <option value="Cereal">Cereal</option>
                    <option value="Milk">Milk</option>
                    <option value="Soda">Soda</option>
                    <option value="Eggs">Eggs</option>
                    <option value="Soup">Soup</option>
                    <option value="Salad">Salad</option>
                </select>
                <div class="mt-3 border p-2">
                    <div class="text-primary font-weight-bold mt-2 mb-2">Code</div>
                    <code></code>
                </div>
            </div>
        </div>
    </div><!-- ./ .row -->
    <div class="row">
        <div class="col-4">
            <div class="my-3 mx-2 sample-form-div">
                <div class="text-primary font-weight-bold mt-2 mb-2">Select - Multiple</div>
                <select class="custom-form-element form-select" multiple data-label="Select - Multiple">
                    <option value=""></option>
                    <option value="Pizza">Pizza</option>
                    <option value="Crabs" selected>Crabs</option>
                    <option value="Burgers">Burgers</option>
                    <option value="Noodles" selected>Noodles</option>
                    <option value="Beer">Beer</option>
                    <option value="Booze">Booze</option>
                    <option value="Crackers">Crackers</option>
                    <option value="Cereal">Cereal</option>
                    <option value="Milk">Milk</option>
                    <option value="Soda">Soda</option>
                    <option value="Eggs">Eggs</option>
                    <option value="Soup">Soup</option>
                    <option value="Salad">Salad</option>
                </select>
                <div class="mt-3 border p-2">
                    <div class="text-primary font-weight-bold mt-2 mb-2">Code</div>
                    <code></code>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="my-3 mx-2 sample-form-div">
                <select class="custom-form-element form-select form-select-no-search" data-label="Select - No Search">
                    <option value=""></option>
                    <option value="Pizza">Pizza</option>
                    <option value="Crabs" selected>Crabs</option>
                    <option value="Burgers">Burgers</option>
                    <option value="Noodles">Noodles</option>
                    <option value="Beer">Beer</option>
                    <option value="Booze">Booze</option>
                    <option value="Crackers">Crackers</option>
                    <option value="Cereal">Cereal</option>
                    <option value="Milk">Milk</option>
                    <option value="Soda">Soda</option>
                    <option value="Eggs">Eggs</option>
                    <option value="Soup">Soup</option>
                    <option value="Salad">Salad</option>
                </select>
                <div class="mt-3 border p-2">
                    <div class="text-primary font-weight-bold mt-2 mb-2">Code</div>
                    <code></code>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="my-3 mx-2 sample-form-div">
                <div class="text-primary font-weight-bold mt-2 mb-2">Select - No Cancel</div>
                <select class="custom-form-element form-select form-select-no-cancel" data-label="Select - No Cancel">
                    <option value=""></option>
                    <option value="Pizza">Pizza</option>
                    <option value="Crabs">Crabs</option>
                    <option value="Burgers">Burgers</option>
                    <option value="Noodles">Noodles</option>
                    <option value="Beer">Beer</option>
                    <option value="Booze">Booze</option>
                    <option value="Crackers">Crackers</option>
                    <option value="Cereal">Cereal</option>
                    <option value="Milk">Milk</option>
                    <option value="Soda">Soda</option>
                    <option value="Eggs">Eggs</option>
                    <option value="Soup">Soup</option>
                    <option value="Salad">Salad</option>
                </select>
                <div class="mt-3 border p-2">
                    <div class="text-primary font-weight-bold mt-2 mb-2">Code</div>
                    <code></code>
                </div>
            </div>
        </div>
        <div class="col-4">

        </div>
    </div><!-- ./ .row -->
    <div class="row">
        <div class="col-4">
            <div class="my-3 mx-2 sample-form-div">
                <div class="text-primary font-weight-bold mt-2 mb-2">Checkbox - Checked</div>
                <input type="checkbox" class="custom-form-element form-checkbox" id="checked_check" value="abcde" data-label="Checked" checked>
                <div class="mt-3 border p-2">
                    <div class="text-primary font-weight-bold mt-2 mb-2">Code</div>
                    <code></code>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="my-3 mx-2 sample-form-div">
                <div class="text-primary font-weight-bold mt-2 mb-2">Checkbox - Disabled</div>
                <input type="checkbox" class="custom-form-element form-checkbox" id="first_check" value="abcde" disabled data-label="Disabled">
                <div class="mt-3 border p-2">
                    <div class="text-primary font-weight-bold mt-2 mb-2">Code</div>
                    <code></code>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="my-3 mx-2 sample-form-div">
                <div class="text-primary font-weight-bold mt-2 mb-2">radio - Checked</div>
                <input type="radio" class="custom-form-element form-radio" id="checked_radio" name="radio_group" value="abcde" data-label="Checked" checked>
                <input type="radio" class="custom-form-element form-radio" id="unchecked_radio" name="radio_group" value="abcdef" data-label="Un Checked">
                <div class="mt-3 border p-2">
                    <div class="text-primary font-weight-bold mt-2 mb-2">Code</div>
                    <code></code>
                </div>
            </div>
        </div>
    </div>
</div><!-- ./ .container -->

@endsection
@section('js')
<script>
    $(function() {

        $('.custom-form-element').each(function() {
            $(this).wrap('<div class="form-element-input"></div>');
            let html = $(this).parent('.form-element-input').html();
            let replace = new RegExp('<', 'g');
            html = html.replace(replace, '&lt;');
            $(this).closest('.sample-form-div').find('code').html(html);
        });

        setTimeout(function() {
            form_elements();
        }, 500);



    });
</script>
@endsection
