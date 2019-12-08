@extends('layouts.main')
@section('title', 'Edit File')
@section('content')

<div class="container">

    <button type="button" id="pdfDownloader">Download</button>
    <div id="printDiv" style="width: 1200px; height: 1800px; padding-top: 14px;">
        <div>
            <h2>Hello World</h2>
            <p>
                this content will be printed.
            </p>
        </div>
    </div>

</div>

@endsection