@extends('layout')

@section('title')

<a href="{{ action('FieldsController@index') }}">Fields</a>

@if($action == 'add')
	> Add new field
@elseif($action == 'edit')
	> Edit field {{$field->id}}
@endif

@stop

@section('content')

@if($action == 'add')
	<form action="{{ action('FieldsController@handle_add') }}" method="post">
@elseif($action == 'edit')
	<form action="{{ action( 'FieldsController@handle_edit',
	array('field_id'=>$field->id) ) }}" method="post">
@endif

<table>

<tr>
<td> {{ Form::label('name', 'Name') }}</td>
<td>
@if($action == 'add')
	{{ Form::text('name') }}
@elseif($action == 'edit')
	{{ Form::text('name', $field->name) }}
@endif
</td>
</tr>
<tr/>

<tr>
{{ $errors->first('type', '<span class="error">:message</span>') }}
<td> {{ Form::label('type', 'Type') }} </td>
<td> 
@if($action == 'add')
	{{ Form::select('type', $field_types) }}
@elseif($action == 'edit')
	{{ Form::select('type', $field_types, $field->type) }}
@endif
</td>
</tr>

</table>

<p><input type="submit" value="Submit"></p>
</form>

@stop
