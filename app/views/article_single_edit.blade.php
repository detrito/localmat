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

{{-- Loop througt article's attributes using field-ids as keys --}}
@foreach($fields as $field)
	<?php
	$attributes = $article->proprieties->attributes;
	var_dump($attributes);
	$attribute = $attributes->filter(function($item) use($field) {
		return $item->field->id == $field->id;
		})->first();
	?>

	@if ($field->type == 'boolean')
		<tr>
		<td> {{ Form::label($field->name, $field->name) }} </td>
		<td> {{ Form::checkbox($field->name, 1,
			isset($attribute) ? $attribute->value : "") }} </td>
		</tr>
	@else
		<tr>
		<td> {{ Form::label($field->name, $field->name) }} </td>
		<td> {{ Form::text($field->name,
			isset($attribute) ? $attribute->value : "") }} </td>
		</tr>
    @endif

@endforeach

</table>

<p><input type="submit" value="Submit"></p>
</form>

@stop
