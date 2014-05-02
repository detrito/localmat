@extends('layout')

@section('title')

<a href="{{ action('CategoriesController@index') }}">Categories</a>

@if($action == 'add')
	> Add new category
@elseif($action == 'edit')
	> Edit category {{$category->id}}
@endif

@stop

@section('content')

@if($action == 'add')
	<form action="{{ action('CategoriesController@handle_add') }}" method="post">
@elseif($action == 'edit')
	<form action="{{ action( 'CategoriesController@handle_edit',
	array('category_id'=>$category->id) ) }}" method="post">
@endif


<table>
<tr>
	<td> {{ Form::label('name', 'Category name') }} </td>
	<td>
		@if($action == 'add')
			{{ Form::text('name') }}
		@elseif($action == 'edit')
			{{ Form::text('name',$category->name) }}
		@endif
	</td>
</tr>

<tr>
	<td>Fields</td>
	<td>
		<table>
		@foreach($fields as $field)
		<tr>
			<td>{{ Form::label($field->name, $field->name) }}</td>
			<td>
				@if($action == 'add')
					{{ Form::checkbox($field->name, $field->id, false) }}
				@elseif($action == 'edit')
					{{ Form::checkbox($field->name, $field->id,
					$field_values[$field->id]) }}
				@endif
			</td>
		</tr>
		@endforeach
		</table>
	</td>
</tr>
</table>

<p><input type="submit" value="Submit"></p>
</form>

@stop
