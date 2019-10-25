@extends('layouts.main')
@section('title', 'Upload Files')
@section('content')
<div class="container page-upload">
    <div class="row">
        <div class="col-12 mt-5">

            <form method="post" id="upload_file_form" action="/upload_file" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="file_upload">Select File(s)</label>
                    <input type="file" class="form-control-file" name="file_upload[]" id="file_upload" multiple>
                </div>
                <button type="submit" class="btn btn-primary" id="upload_file_button">
                    Upload
                </button>
            </form>

        </div>
    </div>
</div>
@endsection

