@extends('layout')


@section('title')

Laravel logs

@stop

@section('content')

<textarea rows="20" cols="80">
{{ $logs }}
</textarea>

@stop
