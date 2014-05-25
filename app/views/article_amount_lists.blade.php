@extends('layout')

@section('title')

@if(isset($category_name))
	<a href="{{ action('ArticlesController@lists')}}">Articles</a>
	> {{ $category_name }} > {{ $status_name }}
@else
	Articles
@endif

@stop

@section('content')

<p>Category
<select name="dropdown" onChange="document.location = this.value" value="GO">
<option value=""></option>
@foreach($categories as $category_item)
<option value="{{ action('ArticlesController@lists', array(
		'status_name'=>$status_name,
		'category_id'=>$category_item->id,
		'field_id'=>$field_id)) }}"
	@if($category_item->id == $category_id)
		selected
	@endif
	>{{ strtolower($category_item->name) }}</option>
@endforeach
</select>
</p>

available items {{ $article->proprieties->available_items }} <br/>
total items {{ $article->proprieties->total_items }} <br/>

@stop

