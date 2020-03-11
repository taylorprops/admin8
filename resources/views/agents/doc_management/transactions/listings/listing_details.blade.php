@extends('layouts.main')
@section('title', 'title here')

@section('content')
<div class="container page-listing-details">

    <div class="row mt-1 mt-md-4">
        <div class="col-md-2">
            <span class="badge badge-pill orange mx-md-3 mt-1"><span class="transaction-type text-white">Listing</span></span>
        </div>
        <div class="col-md-8 text-md-center">
            <div class="h3-responsive text-primary mb-3 mt-1">{{ $listing -> FullStreetAddress.' '.$listing -> Street.' '.$listing -> City.' '.$listing -> StateOrProvince.' '.$listing -> PostalCode }}</div>
        </div>
        <div class="col-md-2 d-none d-md-block">
            @if($listing -> ListPictureURL)
            <div class="property-image-div float-right">
                <img src="{{ $listing -> ListPictureURL }}" class="img-fluid z-depth-2">
            </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col">

            {{-- status
            agent and info
            seller
            year built --}}
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <ul id="tabs" class="nav nav-tabs">
                <li class="nav-item"><a href="javascript: void(0)" data-target="#details_tab" data-toggle="tab" class="nav-link active"><i class="fad fa-home-lg-alt mr-2 d-none d-md-inline-block"></i> Details</a></li>
                <li class="nav-item"><a href="javascript: void(0)" data-target="#contacts_tab" data-toggle="tab" class="nav-link"><i class="fad fa-user-friends mr-2 d-none d-md-inline-block"></i> Contacts</a></li>
                <li class="nav-item"><a href="javascript: void(0)" data-target="#checklist_tab" data-toggle="tab" class="nav-link"><i class="fad fa-tasks mr-2 d-none d-md-inline-block"></i> Checklist</a></li>
                <li class="nav-item"><a href="javascript: void(0)" data-target="#docs_tab" data-toggle="tab" class="nav-link"><i class="fad fa-folder-open mr-2 d-none d-md-inline-block"></i> Documents</a></li>
                <li class="nav-item"><a href="javascript: void(0)" data-target="#contracts_tab" data-toggle="tab" class="nav-link"><i class="fad fa-file-signature mr-2 d-none d-md-inline-block"></i> Contracts</a></li>
            </ul>
            <br>
            <div id="tabsContent" class="tab-content">
                <div id="details_tab" class="tab-pane fade">
                    <div class="list-group"><a href="" class="list-group-item d-inline-block"><span class="float-right badge badge-pill badge-dark">51</span> Home Link</a> <a href="" class="list-group-item d-inline-block"><span class="float-right badge badge-pill badge-dark">8</span> Link 2</a> <a href="" class="list-group-item d-inline-block"><span class="float-right badge badge-pill badge-dark">23</span> Link 3</a> <a href="" class="list-group-item d-inline-block text-muted">Link n..</a></div>
                </div>
                <div id="contacts_tab" class="tab-pane fade active show">
                    <div class="row pb-2">
                        <div class="col-md-7">
                            <p>Tabs can be used to contain a variety of content &amp; elements. They are a good way to group <a href="" class="link">relevant content</a>. Display initial content in context to the user. Enable the user to flow through <a href="" class="link">more</a> information as needed. </p>
                        </div>
                        <div class="col-md-5"><img src="//dummyimage.com/1005x559.png/5fa2dd/ffffff" class="float-right img-fluid img-rounded"></div>
                    </div>
                </div>
                <div id="checklist_tab" class="tab-pane fade">
                    <div class="list-group"><a href="" class="list-group-item d-inline-block"><span class="float-right badge badge-pill badge-dark">44</span> Message 1</a> <a href="" class="list-group-item d-inline-block"><span class="float-right badge badge-pill badge-dark">8</span> Message 2</a> <a href="" class="list-group-item d-inline-block"><span class="float-right badge badge-pill badge-dark">23</span> Message 3</a> <a href="" class="list-group-item d-inline-block text-muted">Message n..</a></div>
                </div>
                <div id="docs_tab" class="tab-pane fade">
                    <div class="list-group"><a href="" class="list-group-item d-inline-block"><span class="float-right badge badge-pill badge-dark">44</span> Message 1</a> <a href="" class="list-group-item d-inline-block"><span class="float-right badge badge-pill badge-dark">8</span> Message 2</a> <a href="" class="list-group-item d-inline-block"><span class="float-right badge badge-pill badge-dark">23</span> Message 3</a> <a href="" class="list-group-item d-inline-block text-muted">Message n..</a></div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
