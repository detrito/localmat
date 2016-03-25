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

// index
Route::get('/', 'IndexController@index');

// routes accessibles only to guests (i.e. not yet logged-in users)
Route::group(array('before' => 'guest'), function()
{
	Route::get('/login', 'UsersController@login');
	Route::post('/login', 'UsersController@handle_login');
});

// routes accessibles only to logged-in and enabled users
Route::group(array('before' => 'auth|enabled'), function()
{
	//logout
	Route::get('/logout', 'UsersController@logout');

	//users
	Route::get('/users', 'UsersController@index');
	Route::get('/users/view/{user_id}', 'UsersController@view');

	//article
	Route::get('/articles', 'ArticlesController@index');
	Route::get('/articles/lists/{status_name?}/{category_id?}/{field_id?}/{order?}','ArticlesController@lists');
	Route::get('/articles/view/{article_id}', 'ArticlesController@view');
	Route::post('/articles/borrow/', 'HistoryController@handle_borrow_post');
	Route::get('/articles/borrow/{article_id}', 'HistoryController@handle_borrow_get');
	Route::post('/articles/view/{user_id}', 'HistoryController@handle_return');
	
	// history
	Route::get('/history/', 'HistoryController@index');	
	Route::get('/history/{status_name?}', 'HistoryController@lists');
});

// routes accessibles only to logged-in and enabled administrators users
Route::group(array('before' => 'auth|enabled|admin'), function()
{
	Route::get('/admin', 'AdminController@index');
	Route::get('/admin/export/logs', 'AdminController@export_logs');
	Route::get('/admin/export/articles', 'AdminController@export_articles');
	Route::get('/admin/export/histories', 'AdminController@export_histories');
	Route::get('/admin/export/users', 'AdminController@export_users');
	Route::get('/admin/export/db', 'AdminController@export_db');
	
	// articles
	Route::get('/articles/add/{category_id?}', 'ArticlesController@add');
	Route::post('/articles/add/{category_id}', 'ArticlesController@handle_add');
	Route::get('/articles/edit/{article_id}', 'ArticlesController@edit');
	Route::post('/articles/edit/{article_id}', 'ArticlesController@handle_edit');
	Route::get('/articles/delete/{article_id}', 'ArticlesController@delete');

	// fields
	Route::get('/fields', 'FieldsController@index');
	Route::get('/fields/add', 'FieldsController@add');
	Route::post('/fields/add', 'FieldsController@handle_add');
	Route::get('/fields/edit/{field_id}', 'FieldsController@edit');
	Route::post('/fields/edit/{field_id}', 'FieldsController@handle_edit');
	Route::get('/fields/delete/{field_id}', 'FieldsController@delete');
	
	// categories
	Route::get('/categories', 'CategoriesController@index');
	Route::get('/categories/add/{article_class?}', 'CategoriesController@add');
	Route::post('/categories/add/{article_class}', 'CategoriesController@handle_add');
	Route::get('/categories/edit/{user_id}', 'CategoriesController@edit');
	Route::post('/categories/edit/{user_id}', 'CategoriesController@handle_edit');
	Route::get('/categories/delete/{user_id}', 'CategoriesController@delete');

	// users	
	Route::get('/users/add', 'UsersController@add');
	Route::post('/users/add', 'UsersController@handle_add');
	Route::get('/users/login/{user_id}', 'UsersController@login_as');	
	Route::get('/users/trash/{user_id}', 'UsersController@trash');	
	Route::get('/users/restore/{user_id}', 'UsersController@restore');
	Route::get('/users/edit/permissions/{user_id}', 'UsersController@edit_permissions');
	Route::post('/users/edit/permissions/{user_id}', 'UsersController@handle_edit_permissions');
});

// routes accessibles only to logged-in and enabled owner users
Route::group(array('before' => 'auth|enabled|owner'), function()
{
	Route::get('/users/edit/profile/{user_id}', 'UsersController@edit_profile');
	Route::post('/users/edit/profile/{user_id}', 'UsersController@handle_edit_profile');
	Route::get('/users/edit/password/{user_id}', 'UsersController@edit_password');
	Route::post('/users/edit/password/{user_id}', 'UsersController@handle_edit_password');
});

// some extra validator types
Validator::extend('alpha_spaces', function($attribute, $value)
{
    return preg_match('/^[\pL\s]+$/u', $value);
});

Validator::extend('alpha_num_dash_spaces', function($attribute, $value)
{
	return preg_match('/^[\w ]+$/u', $value);
});

