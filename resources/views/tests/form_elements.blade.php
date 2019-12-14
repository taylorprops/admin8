@extends('layouts.main')
@section('title', 'title here')

@section('content')


<div class="container mt-5">
    <div class="row">
        <div class="col-4 my-3">
            <input type="text" class="form-input" data-label="Input">
        </div>
        <div class="col-4 my-3">
                <input type="file" id="test_file" class="form-input-file" data-label="File Upload">
        </div>
        <div class="col-4 my-3">
            <select class="form-select form-select-no-search" disabled data-label="Select - Disabled">
                <option value=""></option>
                <option value="pizza">Pizza</option>
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
        </div>
    </div><!-- ./ .row -->
    <div class="row">
        <div class="col-4 my-3">
            <select class="form-select" multiple data-label="Select - Multiple">
                <option value=""></option>
                <option value="pizza">Pizza</option>
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
        </div>
        <div class="col-4 my-3">
            <select class="form-select form-select-no-search" data-label="Select - No Search">
                <option value=""></option>
                <option value="pizza">Pizza</option>
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
        </div>
        <div class="col-4 my-3">
            <select class="form-select form-select-no-cancel" data-label="Select - No Cancel">
                <option value=""></option>
                <option value="pizza">Pizza</option>
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
        </div>
        <div class="col-4 my-3">

        </div>
    </div><!-- ./ .row -->
    <div class="row">
        <div class="col-4 my-3">
            <input type="checkbox" class="form-checkbox" id="first_check" value="abcde" data-label="Checked" checked>
            <input type="checkbox" class="form-checkbox"  id="second_check" value="fghij" data-label="Not Checked">
            <input type="checkbox" class="form-checkbox"  id="third_check" value="klmno" data-label="Disabled" disabled>
        </div>
        <div class="col-4 my-3">

        </div>
        <div class="col-4 my-3">

        </div>
        <div class="col-4 my-3">

        </div>
    </div>
</div><!-- ./ .container -->

@endsection
