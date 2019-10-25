@extends('layouts.main')
@section('title', 'title here')
@section('content')

<div class="container page-files mt-5">
    <div class="row">
        <div class="col-12">
            @foreach ($files as $file)
            <div class="alert alert-primary" role="alert">
                {{ $file['file_name_orig'] }}
                <div class="float-right">
                    <a href="/add_fields/{{ $file['file_id']}}">View File</a>
                </div>
            </div>

            @endforeach
        </div>
    </div><!-- ./ .row -->
</div><!-- ./ .container -->

@endsection
