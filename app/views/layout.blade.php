<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<title>LocalMat</title>
	<link rel="stylesheet" type="text/css" href="{{ asset('/css/style.css') }}">
</head>
<body>

    <div id="container">

		@if (Auth::check())
			{{ Auth::user()->email }}
			- <a href="{{ url('logout') }}">Logout</a>
		@else
			<a href="{{ url('login') }}">Login</a>
		@endif

		<div>
        <h1>LocalMat</h1>						
    	</div>

		<ul id="nav" class="drop">
			<li><a href="{{ url('/') }}">Home</a></li>
    		<li><a href="{{ action('ArticlesController@index') }}">Articles</a>
				<ul>
					<li><a href="#">Availables</a></li>
					<li><a href="#">Tous</a></li>
					<li><a href="{{ action('ArticlesController@add') }}">Add new article</a></li>
            	</ul>
			</li>

			<li><a href="{{ action('UsersController@index') }}">Users</a>
				<ul>
            		<li><a href="{{ action('UsersController@add') }}" class="navbar-brand">Add new user</a></li>
            	</ul>
			</li>

    		<li><a href="#">Me</a></li>

			<li><a href="{{ url('/admin') }}">Admin</a>
				<ul>
					<li><a href="{{ action('FieldsController@index') }}">Fields</a></li>
					<li><a href="{{ action('FieldsController@add') }}">Add new field</a></li>
					<li><a href="#">Categores</a></li>
					<li><a href="#">Add new category</a></li>
            	</ul>
			</li>

		</ul>
		
		<div class="clear"></div>

		<!-- check for validation error messages -->		
		@if($errors->has())
			@foreach ($errors->all() as $error)
				<div id="flash_error">{{ $error }}</div>
			@endforeach
		@endif

		<!-- check for flash notification message -->
        @if(Session::has('flash_notice'))
        <div id="flash_notice">{{ Session::get('flash_notice') }}</div>
        @endif

	    <!-- check for flash error messages -->
    	@if (Session::has('flash_error'))
   	    <div id="flash_error">{{ Session::get('flash_error') }}</div>
    	@endif

		<div>
		<h2>@yield('title')</h2>
		<hr />	
        @yield('content')
		</div>

    </div>
</body>
</html>
