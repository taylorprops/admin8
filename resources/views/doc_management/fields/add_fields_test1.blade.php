@extends('layouts.main')
@section('title', 'Edit File')
@section('content')
<style type="text/css">

    .draggable, .dragged {
        background-color: #6633FF;
        width: 175px;
        height: 25px;
    }

    .dropzone {
        position: relative;
        background-color: #FF6699;
        width: 350px;
        height: 350px;
        margin: 5px;
        clear: both;
    }


</style>
<div class="draggable" draggable="true">
        <p>Drag Me</p>
    </div>
    <div class="dropzone"></div>


@section('js')
<script type="text/javascript">

</script>
@endsection
@endsection