@extends('layout')

@section('title')

<a href="{{ action('UsersController@index') }}">Users</a>

@if($action == 'add')
	> Add new user
@elseif($action == 'edit')
	> Edit user {{$user->id}}
@endif

@stop

@section('content')

@if($action == 'add')
	<form action="{{ action('UsersController@handle_add') }}" method="post">
@elseif($action == 'edit')
	<form action="{{ action( 'UsersController@handle_edit',
	array('user_id'=>$user->id) ) }}" method="post">
@endif

<table>

<tr>
<td> {{ Form::label('given_name', 'Given name') }} </td>
<td>
@if($action == 'add')
	{{ Form::text('given_name') }}
@elseif($action == 'edit')
	{{ Form::text('given_name',$user->given_name) }}
@endif
</td>
</tr>

<tr>
<td> {{ Form::label('family_name', 'Family name') }} </td>
<td>
@if($action == 'add')
	{{ Form::text('family_name') }}
@elseif($action == 'edit')
	{{ Form::text('family_name',$user->family_name) }}
@endif
</td>
</tr>

<tr>
<td> {{ Form::label('email', 'E-mail') }} </td>
<td>
@if($action == 'add')
	{{ Form::text('email') }}
@elseif($action == 'edit')
	{{ Form::text('email',$user->email) }}
@endif
</td>
</tr>

<tr>
<td> {{ Form::label('password', 'Password') }} </td>
<td>
@if($action == 'add')
	{{ Form::password('password') }}
@elseif($action == 'edit')
	{{ Form::password('password') }}
	Only enter a password if you want to change it!
@endif
</td>
</tr>

<tr>
<td> {{ Form::label('password_confirmation', 'Password confirmation') }} </td>
<td>
@if($action == 'add')
	{{ Form::password('password_confirmation') }}
@elseif($action == 'edit')
	{{ Form::password('password_confirmation') }}
	Only enter a password if you want to change it!
@endif
</td>
</tr>

<tr>
<td> {{ Form::label('enabled', 'Active') }} </td>
<td>
@if($action == 'add')
	{{ Form::checkbox('enabled', 1, true); }}
@elseif($action == 'edit')
	{{ Form::checkbox('enabled', 1, $user->enabled); }}
@endif
</td>
</tr>

{{-- FIXME allow admin and active checkboboxes only for admins in a cleaner way--}}
@if (Auth::check() && Auth::user()->admin)	
<tr>
<td> {{ Form::label('admin', 'Administrator') }} </td>
<td>
@if($action == 'add')
	{{ Form::checkbox('admin', 1, false); }}
@elseif($action == 'edit')
	{{ Form::checkbox('admin', 1, $user->admin); }}
@endif
</td>
</tr>
@endif

</table>

<p><input type="submit" value="Submit"></p>
</form>

@stop
