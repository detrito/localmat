<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::guest('login')
		->with('flash_error', 'You have to log in in order to view this page.');
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

// check if the user is enabled
Route::filter('enabled', function()
{
	if( ! Auth::guest() )
	{
		if (Auth::user()->enabled != 1)
		{
			Auth::user()->errorDisabled();
		}
	}
});

// check user is able to switch back to a previous user
Route::filter('was_admin', function()
{
	$user_id = Session::get('main_user_id');
	if($user_id == NULL)
	{
		return Redirect::to('/')
			->with('flash_error', 'Only administrators can view this page!');
	}
});

// check if the user is an administrator
Route::filter('admin', function()
{
	if( ! Auth::guest())
	{
		if (Auth::user()->admin != 1 )
		{
			return Redirect::to('/')
			->with('flash_error', 'Only administrators can view this page!');
		}
	}
});

// check if id of the logged-in user correspond to $user_id
Route::filter('owner', function($route) {
    if ($route->getParameter('user_id') !=  Auth::user()->id)
    {
        return Redirect::to('/')
			->with('flash_error', 'Only owners can view this page!');
    }
});


// check if admin or owner
Route::filter('admin_or_owner', function($route)
{
	if( ! Auth::guest())
	{
		// if user is not admin AND is not owner
		if (Auth::user()->admin != 1 && $route->getParameter('user_id') !=  Auth::user()->id)
		{
			return Redirect::to('/')
			->with('flash_error', 'Only owners and administrators can view this page!');
		}
	}
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) 
		return Redirect::to('/')
			->with('flash_error', 'You are already logged in!');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});
