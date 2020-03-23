@extends('layouts.main')
@section('title', 'title here')

@section('content')
Agents Dashboard
@if(Session::get('agent_details'))
{{ Session::get('agent_details') -> fullname }}
@endif
@endsection

@section('js')
@endsection
