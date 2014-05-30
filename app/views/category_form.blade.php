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
	<form action="{{ action('CategoriesController@handle_add',
		array('article_class'=>$article_class)) }}" method="post">
@elseif($action == 'edit')
	<form action="{{ action( 'CategoriesController@handle_edit',
		array('category_id'=>$category->id)) }}" method="post">
@endif


<table>
<tr>
	<td>Class type</td>
	<td>
		@if($action == 'add')
			<select name="dropdown" onChange="document.location = this.value" value="GO">			
			<option value=""></option>
			@foreach($article_classes as $article_class_item)
				<option value="{{ action('CategoriesController@add', array(
					'article_class'=>$article_class_item)) }}"
				@if($article_class_item == $article_class)
					selected
				@endif
				>{{ $article_class_item }}</option>	
			@endforeach
			</select>
		
		@elseif($action == 'edit')
			<input disabled="disabled" type="text" value="{{ $article_class }}" id="article_class" />
		@endif
	</td>
</tr>

@if( isset($article_class) )
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

	@if($article_class == 'ArticleSingle')
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
	@elseif($article_class == 'ArticleAmount')
		<tr>
			<td>{{ Form::label('available_items', 'Available items') }}</td>
			@if($action == 'add')
				<td>{{ Form::text('available_items') }}
			@elseif($action == 'edit')
				<td>{{ Form::text('available_items', $article_amount->available_items) }}
			@endif
		</tr>
		<tr>
			<td>{{ Form::label('total_items', 'Total items') }}</td>
			@if($action == 'add')
				<td>{{ Form::text('total_items') }}</td>
			@elseif($action == 'edit')
				<td>{{ Form::text('total_items', $article_amount->total_items) }}</td>
			@endif
		</tr>
	@endif

@endif

</table>

<p><input type="submit" value="Submit"></p>
</form>

@stop
