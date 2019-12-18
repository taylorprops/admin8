@extends('layouts.main')
@section('title', 'Uploaded Files')
@section('content')
<div class="container page-files mt-5">
    <div class="row">
        <div class="col-12">
            @foreach ($files as $file)
            <div class="alert alert-primary" role="alert" data-file-id="{{ $file['file_id'] }}">
                <div class="container">
                    <div class="row">
                        <div class="col-8">
                            {{ $file['file_name_orig'] }}
                        </div>
                        <div class="col-2">
                            <div class="float-right">
                                <a class="btn btn-primary btn-sm" href="/create/add_fields/{{ $file['file_id'] }}">View File</a>
                            </div>
                        </div>
                        <div class="col-2">

                        </div>
                    </div><!-- ./ .row -->
                </div><!-- ./ .container -->
            </div>
            @endforeach
        </div>
    </div><!-- ./ .row -->
</div><!-- ./ .container -->
@endsection
