@extends('layout')

@section('title')



@if(isset($category))
	<a href="{{ url('/articles/list') }}">Articles</a>
	> {{ $category }}
@else
	Articles
@endif


@stop

@section('content')

<p>Category
<select name="dropdown" onChange="document.location = this.value" value="GO">
<option value=""></option>
@foreach($categories as $category_item)
<option value="{{ action('ArticlesController@lists',
		array('status'=>$status_name,'category_id'=>$category_item->id) )}}"
	>{{ strtolower($category_item->name) }}</option>
@endforeach
</select>
</p>

{{-- Loop througt categories --}}
@foreach($categories as $category)
	<h3><a href="{{ action('ArticlesController@lists',
	array('status'=>'all','category_id'=>$category->id) ) }}">
		{{ $category->name }}</a></h3>

	@if (empty($category->articles->first()))
		<p>There are no articles in this category.</p>
	@else
	Here some infos about the article avaibility...
	@endif
@endforeach

@stop

