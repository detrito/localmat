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
			<li><a href="#">Home</a></li>
    		<li><a href="{{ action('ArticlesController@index') }}">Articles</a>
				<ul>
					<li><a href="#">Availables</a></li>
					<li><a href="#">Tous</a></li>
					<li><a href="#">Add new article</a></li>
            	</ul>
			</li>

			<li><a href="{{ action('UsersController@index') }}">Users</a>
				<ul>
            		<li><a href="{{ action('UsersController@add') }}" class="navbar-brand">Add new user</a></li>
            	</ul>
			</li>

    		<li><a href="#">Me</a></li>
		</ul>
		
		<div class="clear"></div>
		
		<!-- check for flash notification message -->
        @if(Session::has('flash_notice'))
        <div id="flash_notice">{{ Session::get('flash_notice') }}</div>
        @endif

	    <!-- check for login error flash var -->
    	@if (Session::has('flash_error'))
   	    <div id="flash_error">{{ Session::get('flash_error') }}</div>
    	@endif

		<div>
        @yield('content')
		</div>

    </div>
</body>
</html>
