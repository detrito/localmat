@extends('layout')

@section('title')

<a href="{{ action('UsersController@index') }}">Users</a>
> <a href="{{ action('UsersController@view',
	array('user_id'=>$user->id) )}}">
	{{$user->given_name}} {{$user->family_name}}</a>
	> Edit user password

@stop

@section('content')

<form action="{{ action( 'UsersController@handle_edit_password',
	array('user_id'=>$user->id) ) }}" method="post">

<table>

<tr>
<td> {{ Form::label('password', 'Password') }} </td>
<td> {{ Form::password('password') }} </td>
</tr>

<tr>
<td> {{ Form::label('password_confirmation', 'Password confirmation') }} </td>
<td> {{ Form::password('password_confirmation') }} </td>
</tr>

</table>

<p><input type="submit" value="Submit"></p>
</form>

@stop
