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
                <input type="text" class="form-input" data-label="Input">
                <div class="mt-3 border p-2">
                    <div class="text-primary font-weight-bold mt-2 mb-2">Code</div>
                    <code>
                        &lt;input type="text" class="form-input" data-label="Input">
                    </code>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="my-3 mx-2 sample-form-div">
                <div class="text-primary font-weight-bold mt-2 mb-2">File Upload</div>
                <input type="file" id="test_file" class="form-input-file" data-label="File Upload">
                <div class="mt-3 border p-2">
                    <div class="text-primary font-weight-bold mt-2 mb-2">Code</div>
                    <code>
                        &lt;input type="file" id="test_file_id" class="form-input-file" data-label="File Upload">
                    </code>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="my-3 mx-2 sample-form-div">
                <div class="text-primary font-weight-bold mt-2 mb-2">Select - Disabled</div>
                <select class="form-select form-select-no-search" disabled data-label="Select - Disabled">
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
                    <code>
                        &lt;select class="form-select form-select-no-search" disabled data-label="Select - Disabled">
                            &lt;option value=""></option>
                            &lt;option value="Pizza">Pizza</option>
                            &lt;ption value="Crabs" selected>Crabs</option>
                            &lt;option value="Burgers">Burgers</option>
                            &lt;option value="Noodles">Noodles</option>
                        &lt;/select>
                    </code>
                </div>
            </div>
        </div>
    </div><!-- ./ .row -->
    <div class="row">
        <div class="col-4">
            <div class="my-3 mx-2 sample-form-div">
                <div class="text-primary font-weight-bold mt-2 mb-2">Select - Multiple</div>
                <select class="form-select" multiple data-label="Select - Multiple">
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
                    <code>
                        &lt;select class="form-select" multiple data-label="Select - Multiple">
                            &lt;option value="">&lt;/option>
                            &lt;option value="Pizza">Pizza&lt;/option>
                            &lt;option value="Crabs" selected>Crabs&lt;/option>
                            &lt;option value="Burgers">Burgers&lt;/option>
                            &lt;option value="Noodles" selected>Noodles&lt;/option>

                        &lt;/select>
                    </code>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="my-3 mx-2 sample-form-div">
                <select class="form-select form-select-no-search" data-label="Select - No Search">
                    <option value=""></option>
                    <option value="Pizza">Pizza als kdfjas lkdfas fal;s fjl;asd fl;as jfdlsk jfdlks dflks djf</option>
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
                    <code>
                        &lt;select class="form-select" multiple data-label="Select - Multiple">
                            &lt;option value="">&lt;/option>
                            &lt;option value="Pizza">Pizza&lt;/option>
                            &lt;option value="Crabs" selected>Crabs&lt;/option>
                            &lt;option value="Burgers">Burgers&lt;/option>
                            &lt;option value="Noodles" selected>Noodles&lt;/option>

                        &lt;/select>
                    </code>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="my-3 mx-2 sample-form-div">
                <div class="text-primary font-weight-bold mt-2 mb-2">Select - No Cancel</div>
                <select class="form-select form-select-no-cancel" data-label="Select - No Cancel">
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
                    <code>
                        &lt;select class="form-select form-select-no-cancel" data-label="Select - No Cancel">
                            &lt;option value="">&lt;/option>
                            &lt;option value="Pizza">Pizza&lt;/option>
                            &lt;option value="Crabs">Crabs&lt;/option>
                            &lt;option value="Burgers">Burgers&lt;/option>
                            &lt;option value="Noodles">Noodles&lt;/option>

                        &lt;/select>
                    </code>
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
                <input type="checkbox" class="form-checkbox" id="checked_check" value="abcde" data-label="Checked" checked>
                <div class="mt-3 border p-2">
                    <div class="text-primary font-weight-bold mt-2 mb-2">Code</div>
                    <code>
                        &lt;input type="checkbox" class="form-checkbox" id="not_checked_check" value="abcde" data-label="Checked" checked>
                    </code>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="my-3 mx-2 sample-form-div">
                <div class="text-primary font-weight-bold mt-2 mb-2">Checkbox - Disabled</div>
                <input type="checkbox" class="form-checkbox" id="first_check" value="abcde" data-label="Disabled">
                <div class="mt-3 border p-2">
                    <div class="text-primary font-weight-bold mt-2 mb-2">Code</div>
                    <code>
                        <input type="checkbox" class="form-checkbox" id="disabeled_check" value="abcde" data-label="Disabled" disabled>
                    </code>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="my-3 mx-2 sample-form-div">
                <div class="text-primary font-weight-bold mt-2 mb-2">Checkbox - Checked</div>
                <input type="checkbox" class="form-checkbox" id="first_check" value="abcde" data-label="Checked" checked>
                <div class="mt-3 border p-2">
                    <div class="text-primary font-weight-bold mt-2 mb-2">Code</div>
                    <code>
                        &lt;input type="checkbox" class="form-checkbox" id="first_check" value="abcde" data-label="Checked" checked>
                    </code>
                </div>
            </div>
        </div>
    </div>
</div><!-- ./ .container -->

@endsection
@section('js')
<script>
    $(document).ready(function() {

    });
</script>
@endsection
