@extends('layout')

@section('title')

<a href="{{ action('ArticlesController@index') }}">Articles</a>
> <a href="{{ action('ArticlesController@add') }}">Add new article</a>
> {{ $category }}

@stop

@section('content')

<form action="{{ action('ArticlesController@handle_add',
	array('category'=>$category)) }}" method="post">

<input type="hidden"  name="fields"  value='{{ $fields->toJson() }}'>

<table>

@foreach ($fields as $field)	
	@if ($field->type == 'boolean')
		<tr>
		<td> {{ Form::label(rawurlencode($field->name), $field->name) }} </td>
		<td> {{ Form::checkbox(rawurlencode($field->name), 1, true); }} </td>
		</tr>
	@else
		<tr>
		<td> {{ Form::label(rawurlencode($field->name), $field->name) }} </td>
		<td> {{ Form::text(rawurlencode($field->name)) }} </td>
		</tr>
    @endif
@endforeach

</table>

<p><input type="submit" value="Submit"></p>
</form>

@stop
