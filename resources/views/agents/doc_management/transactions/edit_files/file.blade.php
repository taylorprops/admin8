@extends('layouts.main')
@section('title', 'Edit File')
@section('content')

<div class="container page-fill-fields file-view-container p-0 mx-auto">

    <div class="container-fluid">
        <div class="row">
            <div class="col-12 p-0">
                <nav class="navbar navbar-expand-lg navbar-light navbar-edit-file-options p-0">

                    <!-- Collapse button -->
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#basicExampleNav" aria-controls="basicExampleNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Collapsible content -->
                    <div class="collapse navbar-collapse" id="basicExampleNav">

                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item border-right">
                                <a class="nav-link text-white bg-green px-5 py-2 text-center" href="javascript: void(0)" id="save_field_input_values"><i class="fad fa-save fa-lg"></i><br>Save</a>
                            </li>
                            <li class="nav-item border-right">
                                <a class="nav-link text-primary-dark px-4 py-2 text-center" href="javascript: void(0)"><i class="fad fa-file-pdf fa-lg"></i><br>To PDF</a>
                            </li>
                            <li class="nav-item dropdown border-right">
                                <a class="nav-link dropdown-toggle text-primary-dark px-4 py-2 text-center" id="print_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fad fa-print fa-lg"></i><br>Print</a>
                                <div class="dropdown-menu dropdown-primary" aria-labelledby="print_dropdown">
                                    <a class="dropdown-item" href="#">Print this form</a>
                                    <a class="dropdown-item" href="#">Print multiple forms</a>
                                    <a class="dropdown-item" href="#">Print Blank</a>
                                </div>
                            </li>
                            <li class="nav-item border-right">
                                <a class="nav-link text-primary-dark px-4 py-2 text-center" href="javascript: void(0)"><i class="fad fa-envelope fa-lg"></i><br>Email</a>
                            </li>
                            <li class="nav-item border-right">
                                <a class="nav-link text-primary-dark px-4 py-2 text-center" href="javascript: void(0)"><i class="fad fa-rabbit-fast fa-lg"></i><br>Quick Fill</a>
                            </li>

                        </ul>

                    </div>
                    <!-- Collapsible content -->

                </nav>

            </div>
        </div>
    </div>

    <div class="container-fluid">

        <div class="row">

            @php $total_pages = count($images); @endphp

            <div class="col-2 p-0 edit-file-sidebar">
                <div class="file-view">
                    <div class="h5 text-white bg-primary-dark p-2"><i class="fal fa-align-left mr-3"></i> Fields</div>
                    <div class="field-list-container"></div>
                </div>
            </div>

            <div class="col-12 col-xl-8 p-0 mx-auto">

                <div class="container-fluid p-0">
                    <div class="file-viewer-container mx-auto">
                        <div class="file-view" id="file_viewer">

                            @foreach($images as $image)

                            <?php $c = $image -> page_number; ?>

                                <div class="h5 text-white bg-primary p-2 text-center mb-0" id="page_{{ $c }}">
                                    Page <?php echo $c.' of '.$total_pages; ?>
                                </div>
                                <div class="file-view-page-container border border-primary w-100" data-id="{{ $c }}">
                                    <div class="field-container w-100 h-100">
                                        <img class="file-image-bg w-100 h-100" src="{{ $image -> file_location }}">

                                        @foreach($fields as $field)
                                            @if($field -> page == $c)
                                                @include('/agents/doc_management/transactions/edit_files/field_system', [$field, $c, $field_inputs_system, $field_values, $Listing_ID, $Agent_ID, $common_fields])
                                            @endif
                                        @endforeach

                                    </div> <!-- end field-container -->
                                </div> <!-- end file-view-page-container -->
                                <div class="h5 text-white bg-primary p-2 text-center">
                                    Page {{ $c.' of '.$total_pages }}
                                </div>

                            @endforeach

                        </div> <!-- ende file_viewer -->
                    </div> <!-- end file-viewer-container -->

                </div> <!-- end container-fluid p-0 -->

            </div>

            <div class="col-2 p-0 edit-file-sidebar">
                <div class="file-view" id="thumb_viewer">
                    <div class="h5 text-white bg-primary-dark p-2"><i class="fad fa-send-backward mr-3"></i> Pages</div>
                    @foreach($images as $image)
                        @php $c = $image -> page_number; @endphp
                        <div class="file-view-thumb-container  w-50 mx-auto @if($c == 1) active @endif" id="thumb_{{ $c }}" data-id="{{ $c }}">
                            <div class="file-view-thumb">
                                <a href="javascript: void(0)"><img class="file-thumb w-100 h-100" src="{{ $image -> file_location }}"></a>
                            </div>
                            <div class="file-view-thumb-footer text-center mb-4">
                                Page {{ $c }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>

</div><!-- ./ .container -->
<input type="hidden" id="file_id" value="{{ $file_id }}">
<input type="hidden" id="file_type" value="{{ $file_type }}">
<input type="hidden" id="file_name" value="{{ $file -> file_name_display }}">
<input type="hidden" id="Listing_ID" value="{{ $Listing_ID }}">
<input type="hidden" id="Agent_ID" value="{{ $Agent_ID }}">
@endsection


