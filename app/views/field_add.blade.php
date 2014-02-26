@extends('layout')

@section('title')

<a href="{{ action('FieldsController@index') }}">Fields</a>
> Add new field

@stop

@section('content')

<form action="{{ action('FieldsController@handle_add') }}" method="post">

<table>
<tr><td></td></tr>
<tr>
<td> {{ Form::label('name', 'Name') }}</td>
<td> {{ Form::text('name') }} <br />
</td>
</tr>
<tr/>

<tr>
{{ $errors->first('type', '<span class="error">:message</span>') }}
<td> {{ Form::label('type', 'Type') }} </td>
<td> {{ Form::select('type', $field_types) }} </td>
</tr>
</table>

<p><input type="submit" value="Submit"></p>
</form>

@stop
