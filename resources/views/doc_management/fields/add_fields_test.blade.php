@extends('layouts.main')
@section('title', 'Edit File')
@section('content')
<style type="text/css">
    #container {
        width: 720px;
        height: auto;
    }

    .item {
        touch-action: none;
        user-select: none;
        padding: 5px;
        background: #090;
        color: #fff;
        width: 200px;
    }

    .dropzone {
        position: relative;
        width: 100%;
        height: 300px;
        background: ##fff;
        border: 1px solid #900;
    }

</style>
<div id="container">
    <div class="container">
        <div class="row" style="height: 50px">
            <div class="col-4">
                <div class="item">Draggable 1</div>
            </div>
            <div class="col-4">
                <div class="item">Draggable 2</div>
            </div>
            <div class="col-4">
                <div class="item">Draggable 3</div>
            </div>
        </div><!-- ./ .row -->
    </div><!-- ./ .container -->
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="dropzone"></div>
            </div>
        </div><!-- ./ .row -->
    </div><!-- ./ .container -->
</div>
@section('js')
<script type="text/javascript">
$('.dropzone').click(function (e) { //Relative ( to its parent) mouse position
    var posX = $(this).position().left,
        posY = $(this).position().top;
    console.log((e.pageX - posX) + ' , ' + (e.pageY - posY));
});

</script>
@endsection
@endsection
