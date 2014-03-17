@extends('layout')

@section('title')

<a href="{{ action('ArticlesController@index') }}">Articles</a>
> Add new article

@stop

@section('content')

<ul>
@foreach($categories as $category)
<li><a href=" {{url('/articles/add/'.$category->name)}} ">
	{{ $category->name }}</a>
</li>
@endforeach
</ul>

@stop
