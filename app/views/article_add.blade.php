@extends('layout')

@section('title')

<a href="{{ action('ArticlesController@index') }}">Articles</a> >
@if($category_name != 'all')
	<a href="{{ action('ArticlesController@add') }}">Add new article</a>
	> {{ $category_name }}
@else
	Add new article
@endif

@stop

@section('content')

<p>Category
<select name="dropdown" onChange="document.location = this.value" value="GO">

@foreach($category_names as $name)
	<option value="{{ action('ArticlesController@add',
		array('category_name'=>$name) )}}"
	@if($name == $category_name)
		selected
	@endif
	>{{ strtolower($name) }}</option>
@endforeach
</select>
</p>

@if($category_name != 'all')
	<form action="{{ action('ArticlesController@handle_add',
		array('category'=>$category_name)) }}" method="post">

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
@endif

@stop
