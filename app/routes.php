<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

// bind route parameters.
Route::model('user', 'User');

// index
Route::get('/', function()
{
	return View::make('index');
});

// articles
Route::get('/articles', 'ArticlesController@index');
Route::get('/articles/list/{order?}', 'ArticlesController@index');
Route::get('/articles/add', 'ArticlesController@add');
Route::post('/articles/add', 'ArticlesController@handle_add');
Route::get('/articles/delall', 'ArticlesController@delall');

// admin
Route::get('/admin', function()
{
	return View::make('admin_index');
});

// fields
Route::get('/fields', 'FieldsController@index');
Route::get('/fields/add', 'FieldsController@add');
Route::post('/fields/add', 'FieldsController@handle_add');

// categories
Route::get('/categories', 'CategoriesController@index');
Route::get('/categories/add', 'CategoriesController@add');

// users
Route::get('/users', 'UsersController@index');
Route::get('/users/add', 'UsersController@add');
Route::post('/users/add', 'UsersController@handle_add');
Route::get('/users/edit', 'UsersController@edit');
Route::get('/users/delete', 'UsersController@delete');

// secret content
Route::get('/secret', array(
	'before' => 'auth',
	function()
	{
		return Response::make('this is a very secret content!');
	}
));

// me
Route::get('/me', function()
{
	$me = Auth::user();
	print_r($me);

	if (Auth::check())
	{
    	return Response::make('You logged in!');
	}
	else
	{
		return Response::make('You are not logged in..');
	}
});

// login stuff

Route::get('/login', function()
{
	return View::make('login');
})
->before('guest');

Route::get('/logout', function()
{
	Auth::logout();
	return Response::make('You are now logged out. :(');
});

Route::post('/login', function()
{	
	$credentials = Input::only('email', 'password');
	$remember = true;

	if (Auth::attempt($credentials,$remember)) {
		return Redirect::intended('/');
	}
	
	$asd = print_r($credentials);
	return Response::make( "error" );	
	//return Redirect::to('login');
});

