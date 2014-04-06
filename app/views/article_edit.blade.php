@extends('layout')

@section('title')

<a href="{{ action('ArticlesController@index') }}">Articles</a>
	> Edit article (id {{$article->id}})

@stop

@section('content')

<form action="{{ action('ArticlesController@handle_edit',
	array('article_id'=>$article_id)) }}" method="post">

<input type="hidden"  name="fields"  value='{{ $fields->toJson() }}'>

<table>

@for ($i = 0; $i < $article->attributes->count(); $i++)
	<?php
	$field = $fields[$i];
	$value = $article->attributes->get($i)->value
	?>

	@if ($field->type == 'boolean')
		<tr>
		<td> {{ Form::label($field->name, $field->name) }} </td>
		<td> {{ Form::checkbox($field->name, 1, $value); }} </td>
		</tr>
	@else
		<tr>
		<td> {{ Form::label($field->name, $field->name) }} </td>
		<td> {{ Form::text($field->name,$value) }} </td>
		</tr>
    @endif
@endfor

</table>

<p><input type="submit" value="Submit"></p>
</form>

@stop
