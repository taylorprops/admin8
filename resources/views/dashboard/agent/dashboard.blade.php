@extends('layouts.main')
@section('title', 'title here')

@section('content')
Agents Dashboard
<br>
<div style="width: 500px; height: 300px; background: #333">
    <img src="{{ Session::get('logo_src') }}">
</div>
@if(Session::get('agent_details'))
{{ Session::get('agent_details') -> fullname }}
@endif
@endsection

@section('js')
@endsection
