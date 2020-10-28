@extends('layouts.main')
@section('title', 'Edit - '.$file_name)
@section('content')

<div class="container page-fill-fields file-view-container mx-auto p-0">

    <div class="container-fluid">

        <div class="row border-bottom">

            <div class="col-12 col-xl-8 mx-auto p-0">

                <div class="form-options-container w-100 d-flex justify-content-start align-items-center">

                    <div class="form-options-div border-right">
                        <a class="text-white bg-green fill-form-option" href="javascript: void(0)" id="save_field_input_values_button"><i class="fad fa-save fa-lg"></i><br>Save</a>
                    </div>

                    {{-- <div class="form-options-div border-right">
                        <div class="dropdown">
                            <a class="dropdown-toggle text-primary-dark fill-form-option" id="print_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fad fa-download fa-lg"></i><br>Download</a>
                            <div class="dropdown-menu dropdown-primary" aria-labelledby="print_dropdown">
                                <a class="dropdown-item" href="#">Download this form</a>
                                <a class="dropdown-item" href="#">Download multiple forms</a>
                                <a class="dropdown-item" href="#">Download Blank</a>
                            </div>
                        </div>
                    </div>

                    <div class="form-options-div border-right">
                        <div class="dropdown">
                            <a class="dropdown-toggle text-primary-dark fill-form-option" id="print_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fad fa-print fa-lg"></i><br>Print</a>
                            <div class="dropdown-menu dropdown-primary" aria-labelledby="print_dropdown">
                                <a class="dropdown-item" href="#">Print this form</a>
                                <a class="dropdown-item" href="#">Print multiple forms</a>
                                <a class="dropdown-item" href="#">Print Blank</a>
                            </div>
                        </div>
                    </div>

                    <div class="form-options-div border-right">
                        <a class="text-primary-dark fill-form-option" href="javascript: void(0)"><i class="fad fa-envelope fa-lg"></i><br>Email</a>
                    </div> --}}

                    @if($file_type == 'user')
                    <div class="form-options-div border-right">
                        <a class="text-primary-dark fill-form-option" href="javascript: void(0)" id="rotate_form_button"><i class="fad fa-sync-alt fa-lg"></i><br>Rotate</a>
                    </div>
                    @else
                    {{-- <div class="form-options-div border-right">
                        <a class="text-primary-dark fill-form-option" href="javascript: void(0)"><i class="fad fa-rabbit-fast fa-lg"></i><br>Quick Fill</a>
                    </div> --}}
                    @endif

                    <div class="form-options-div border-right">
                        <a class="text-primary-dark fill-form-option" id="show_edit_options_button" href="javascript: void(0)"><i class="fad fa-edit fa-lg"></i><br>Edit</a>
                    </div>

                    <div class="form-options-div border-left border-right edit-options">
                        <a class="text-primary-dark fill-form-option edit-form-action" href="javascript: void(0)" id="add_text_button" data-field-type="user_text"><i class="fal fa-rectangle-wide fa-lg"></i><br>Add Text</a>
                    </div>

                    <div class="form-options-div border-right edit-options">
                        <a class="text-primary-dark fill-form-option edit-form-action" href="javascript: void(0)" id="add_strikeout_button" data-field-type="strikeout"><i class="fal fa-horizontal-rule fa-lg"></i><br>Add Strikeout</a>
                    </div>

                    <div class="form-options-div border-right edit-options">
                        <a class="text-primary-dark fill-form-option edit-form-action" href="javascript: void(0)" id="add_highlight_button" data-field-type="highlight"><i class="fal fa-highlighter fa-lg"></i><br>Highlight</a>
                    </div>

                    <div class="form-options-div border-right edit-options"></div>

                    <div class="form-options-div border-right edit-options">
                        <a class="text-white bg-green fill-form-option" href="javascript: void(0)" id="save_edit_options_button"><i class="fad fa-save fa-lg"></i><br>Save Edits</a>
                    </div>

                    <div class="form-options-div border-right edit-options">
                        <a class="text-white bg-danger fill-form-option" href="javascript: void(0)" id="cancel_edit_options_button"><i class="fal fa-times fa-lg"></i><br>Cancel Edits</a>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="container-fluid">

        <div class="row">

            @php $total_pages = count($images); @endphp

            <div class="col-2 p-0 edit-file-sidebar">
                <div class="file-view">
                    <div class="h5-responsive text-white bg-primary-dark p-2"><i class="fal fa-align-left mr-3"></i> Fields</div>
                    <div class="field-list-container"></div>
                </div>
            </div>

            <div class="col-12 col-xl-8 p-0 mx-auto">

                <div class="container-fluid p-0">
                    <div class="file-viewer-container mx-auto">
                        <div class="file-view animated fadeIn" id="file_viewer">

                            @foreach($images as $image)

                            <?php $c = $image -> page_number; ?>

                                <div class="h5-responsive bg-primary p-2 text-center mb-0" id="page_{{ $c }}">
                                    <span class="badge">Page <?php echo $c.' of '.$total_pages; ?></span>
                                </div>
                                <div class="file-view-page-container border border-primary w-100 @if($loop -> first) active @endif" data-id="{{ $c }}">
                                    <div class="field-container w-100 h-100">
                                        <img class="file-image-bg w-100 h-100" src="{{ $image -> file_location }}">
                                        @foreach($fields_user as $field_user)
                                            @if($field_user -> page == $c)
                                                @include('/agents/doc_management/transactions/edit_files/field_html', [$field_user, $c, $field_values, $Listing_ID, $Contract_ID, $Referral_ID, $transaction_type, $Agent_ID, $common_fields, $fields_user_inputs])
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                <div class="h5-responsive text-white bg-primary p-2 mb-1 text-center">
                                    <span class="badge">End Page {{ $c }}</span>
                                </div>

                            @endforeach

                        </div> <!-- ende file_viewer -->
                    </div> <!-- end file-viewer-container -->

                </div> <!-- end container-fluid p-0 -->

            </div>

            <div class="col-2 p-0 edit-file-sidebar">
                <div class="file-view animated fadeIn" id="thumb_viewer">
                    <div class="h5-responsive text-white bg-primary-dark p-2"><i class="fad fa-send-backward mr-3"></i> Pages</div>
                    @foreach($images as $image)
                        @php $c = $image -> page_number; @endphp
                        <div class="file-view-thumb-container animated w-50 mb-2 mx-auto @if($c == 1) active @endif" id="thumb_{{ $c }}" data-id="{{ $c }}">
                            <div class="file-view-thumb">
                                <a href="javascript: void(0)"><img class="file-thumb w-100 h-100" src="{{ $image -> file_location }}"></a>
                            </div>
                            <div class="file-view-thumb-footer text-center mb-1">
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
<input type="hidden" id="document_id" value="{{ $document_id }}">
<input type="hidden" id="file_type" value="{{ $file_type }}">
<input type="hidden" id="file_name" value="{{ $file -> file_name_display }}">
<input type="hidden" id="Listing_ID" value="{{ $Listing_ID }}">
<input type="hidden" id="Contract_ID" value="{{ $Contract_ID }}">
<input type="hidden" id="Referral_ID" value="{{ $Referral_ID }}">
<input type="hidden" id="transaction_type" value="{{ $transaction_type }}">
<input type="hidden" id="Agent_ID" value="{{ $Agent_ID }}">
<input type="hidden" id="active_page" value="1">
@endsection


