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
Route::get('/articles/view/{status_name?}/{category_name?}/{field_name?}',
	'ArticlesController@view');

// routes accessibles onl to logged-in and enabled users
Route::group(array('before' => 'auth|enabled'), function()
{
	//users
	Route::get('/users/view/{user_id}', 'UsersController@view');
	//article
	Route::post('/articles/borrow/{status_name}/{category_name}/{field_name}', 'HistoryController@handle_borrow');
	Route::post('/articles/view/{user_id}', 'HistoryController@handle_return');
});

// routes accessibles onl to logged-in and enabled administrators users
Route::group(array('before' => 'auth|enabled|admin'), function()
{
	Route::get('/admin', function()
	{
		return View::make('admin_index');
	});
	
	// articles
	Route::get('/articles/add/{category_name?}', 'ArticlesController@add');
	Route::post('/articles/add/{category_name}', 'ArticlesController@handle_add');
	Route::get('/articles/edit/{article_id}', 'ArticlesController@edit');
	Route::post('/articles/edit/{article_id}', 'ArticlesController@handle_edit');
	Route::get('/articles/delete/{article_id}', 'ArticlesController@delete');

	// fields
	Route::get('/fields', 'FieldsController@index');
	Route::get('/fields/add', 'FieldsController@add');
	Route::post('/fields/add', 'FieldsController@handle_add');
	
	// categories
	Route::get('/categories', 'CategoriesController@index');
	Route::get('/categories/add', 'CategoriesController@add');

	// users	
	Route::get('/users/add', 'UsersController@add');
	Route::post('/users/add', 'UsersController@handle_add');
	Route::get('/users/edit', 'UsersController@edit');
	Route::get('/users/delete', 'UsersController@delete');
});

// users
Route::get('/users', 'UsersController@index');

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


Route::get('/login', array(
	'before' => 'guest',
	function()
	{
		return View::make('login');
	}
));

Route::get('/logout', function()
{
	Auth::logout();
	return Redirect::to(url('/'))
		->with('flash_notice', 'You are successfully logged out.');
});

Route::post('/login', function()
{
	$credentials = Input::only('email', 'password');
	$remember = true;

	if (Auth::attempt($credentials,$remember))
	{
		if(Auth::user()->enabled)
		{
			return Redirect::intended('/')
			->with('flash_notice', 'You are successfully logged in.');
		}
		else
		{
			return Auth::user()->errorDisabled();
		}	
	}
	else	
		return Redirect::to('login')
		->with('flash_error', 'Your username/password combination is incorrect.');
});


Validator::extend('alpha_spaces', function($attribute, $value)
{
    return preg_match('/^[\pL\s]+$/u', $value);
});

