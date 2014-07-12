@extends('layout')

@section('title')

<a href="{{ action('UsersController@index') }}">Users</a>
> <a href="{{ action('UsersController@view',
	array('user_id'=>$user->id) )}}">
	{{$user->given_name}} {{$user->family_name}}</a>
	> Edit user permissions

@stop

@section('content')

<form action="{{ action( 'UsersController@handle_edit_permissions',
	array('user_id'=>$user->id) ) }}" method="post">

<table>

<tr>
<td> {{ Form::label('enabled', 'Active') }} </td>
<td> {{ Form::checkbox('enabled', 1, $user->enabled); }} </td>
</tr>

<tr>
<td> {{ Form::label('admin', 'Administrator') }} </td>
<td> {{ Form::checkbox('admin', 1, $user->admin); }} </td>
</tr>

</table>

<p><input type="submit" value="Submit"></p>
</form>

@stop
