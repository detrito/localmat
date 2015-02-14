@extends('layout')


@section('title')

Administration

@stop

@section('content')

Administration tasks:
<ul>
	<li>Articles</li>
		<ul>
			<li><a href="{{ action('ArticlesController@index') }}">Manage articles</a></li>
			<li><a href="{{ action('ArticlesController@add') }}">Add new article</a></li>
		</ul>
	<li>Fields</li>
		<ul>
			<li><a href="{{ action('FieldsController@index') }}">Manage fields</a></li>
			<li><a href="{{ action('FieldsController@add') }}">Add new field</a></li>
		</ul>
	<li>Categories</li>
		<ul>
			<li><a href="{{ action('CategoriesController@index') }}">Manage categories</a></li>
			<li><a href="{{ action('CategoriesController@add') }}">Add new category</a></li>
		</ul>
	<li>Users</li>
		<ul>
			<li><a href="{{ action('UsersController@index') }}" >Manage users</a></li>
			<li><a href="{{ action('UsersController@add') }}" >Add new user</a></li>
		</ul>
	<li>Export</li>
		<ul>
			<li><a href="{{ action('AdminController@export_logs') }}">Laravel logs (plaintext)</a></li>
			<li><a href="{{ action('AdminController@export_articles') }}">List of all articles (excel)</a></li>
			<li><a href="{{ action('AdminController@export_histories') }}">List of all histories (excel)</a></li>
			<li><a href="{{ action('AdminController@export_users') }}">List of all users (excel)</a></li>
		</ul>
</ul>

@stop
