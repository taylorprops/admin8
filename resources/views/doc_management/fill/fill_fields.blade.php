@extends('layouts.main')
@section('title', 'Fill Fields')
@section('content')

<div class="container-fluid page-fill-fields file-view-container p-0">

        <div class="container-fluid">
            <div class="row">
                <div class="col-12 field-toolbar">

                </div>
            </div>
        </div>

    <div class="container-90">
        <div class="row">

            <?php $total_pages = count($files); ?>

            <div class="col-11 pr-0">

                <div class="container-fluid p-0">
                    <div class="file-viewer-container mx-auto">
                        <div class="file-view" id="file_viewer">

                            @foreach($files as $file)

                                <?php $c = $file['page_number']; ?>

                                <div class="file-view-page-info bg-primary text-white p-2" id="page_{{ $c }}">
                                    Page <?php echo $c.' of '.$total_pages; ?>
                                </div>
                                <div class="file-view-page-container border border-primary w-100" data-id="{{ $c }}">
                                    <div class="field-container w-100 h-100">
                                        <img class="w-100 h-100" src="{{ $file['file_location'] }}">

                                        @foreach($fields as $field)
                                            @if($field['page'] == $c)
                                                @include('doc_management.fill.field', [$field, $c, $field_inputs])
                                            @endif
                                        @endforeach

                                    </div> <!-- end field-container -->
                                </div> <!-- end file-view-page-container -->
                                <div class="file-view-page-info bg-primary text-white p-2 mb-4">
                                    Page {{ $c.' of '.$total_pages }}
                                </div>

                            @endforeach

                        </div> <!-- ende file_viewer -->
                    </div> <!-- end file-viewer-container -->

                </div> <!-- end container-fluid p-0 -->
            </div> <!-- col-11 -->
            <div class="col-1 pl-0">
                <div class="file-view p-2" id="thumb_viewer">
                    @foreach($files as $file)
                        <?php $c = $file['page_number']; ?>
                        <div class="file-view-thumb-container w-100 <?php echo($c == 1) ? 'active' : ''; ?>" id="thumb_{{ $c }}" data-id="{{ $c }}">
                            <div class="file-view-thumb">
                                <a href="javascript: void(0)"><img class="file-thumb w-100 h-100" src="{{ $file['file_location'] }}"></a>
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
@endsection


