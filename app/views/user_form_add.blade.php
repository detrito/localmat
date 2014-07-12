@extends('layout')

@section('title')

<a href="{{ action('UsersController@index') }}">Users</a>
	> Add new user

@stop

@section('content')

<form action="{{ action('UsersController@handle_add') }}" method="post">

<table>

<tr>
<td> {{ Form::label('given_name', 'Given name') }} </td>
<td> {{ Form::text('given_name') }} </td>
</tr>

<tr>
<td> {{ Form::label('family_name', 'Family name') }} </td>
<td> {{ Form::text('family_name') }} </td>
</tr>

<tr>
<td> {{ Form::label('email', 'E-mail') }} </td>
<td> {{ Form::text('email') }} </td>
</tr>

<tr>
<td> {{ Form::label('password', 'Password') }} </td>
<td> {{ Form::password('password') }} </td>
</tr>

<tr>
<td> {{ Form::label('password_confirmation', 'Password confirmation') }} </td>
<td> {{ Form::password('password_confirmation') }} </td>
</tr>

<tr>
<td> {{ Form::label('enabled', 'Active') }} </td>
<td> {{ Form::checkbox('enabled', 1, true) }} </td>
</tr>

<tr>
<td> {{ Form::label('admin', 'Administrator') }} </td>
<td> {{ Form::checkbox('admin', 1, false); }} </td>
</tr>

</table>

<p><input type="submit" value="Submit"></p>
</form>

@stop
