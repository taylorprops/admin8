@extends('layouts.main')
@section('title', 'Upload Files')
@section('content')
<div class="container page-upload">
    <div class="row">
        <div class="col-12 mt-5">

            <form method="post" id="upload_file_form" enctype="multipart/form-data">
                @csrf
                <div class="container">
                    <div class="row">
                        <div class="col-6 my-2">
                            <input type="text" class="form-input" name="form_name" id="form_name" data-label="Form Name">
                        </div>
                        <div class="col-6 my-2">
                            <input type="file" class="form-input-file" name="file_upload" id="file_upload" data-label="Select File">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 my-2">
                            <select name="state" id="state" class="form-select" data-label="Select State">
                                <option value=""></option>
                                @foreach($states as $state)
                                <option value="{{ $state }}">{{ $state }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 my-2">
                            <select name="association" id="association" class="form-select" data-label="Select Association">
                                <option value=""></option>
                                @foreach($states as $state)
                                <option value="{{ $state }}">{{ $state }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mx-auto my-2">
                            <button type="submit" class="btn btn-primary" id="upload_file_button">
                                Upload
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

