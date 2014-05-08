@extends('layout')

@section('title')

<a href="{{ action('ArticlesController@index') }}">Articles</a> >
@if(!empty($category))
	<a href="{{ action('ArticlesController@add') }}">Add new article</a>
	> {{ $category->name }}
@else
	Add new article
@endif

@stop

@section('content')

<p>Category
<select name="dropdown" onChange="document.location = this.value" value="GO">
<option value=""></option>
@foreach($categories as $category_item)
	<option value="{{ action('ArticlesController@add',
		array('category_id'=>$category_item->id) )}}"
	@if( !(empty($category)) && $category_item->id == $category->id )
		selected
	@endif
	>{{ strtolower($category_item->name) }}</option>
@endforeach
</select>
</p>

@if(!empty($category))
	<form action="{{ action('ArticlesController@handle_add',
		array('category_id'=>$category->id)) }}" method="post">

	<input type="hidden"  name="fields"  value='{{ $fields->toJson() }}'>

	<table>

	@foreach ($fields as $field)	
		@if ($field->type == 'boolean')
			<tr>
			<td> {{ Form::label($field->name, $field->name) }} </td>
			<td> {{ Form::checkbox($field->name, 1, true) }} </td>
			</tr>
		@else
			<tr>
			<td> {{ Form::label($field->name, $field->name) }} </td>
			<td> {{ Form::text($field->name) }} </td>
			</tr>
		@endif
	@endforeach
	</table>
	<p><input type="submit" value="Submit"></p>
	</form>
@endif

@stop
