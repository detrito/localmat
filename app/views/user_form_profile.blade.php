@extends('layout')

@section('title')

<a href="{{ action('UsersController@index') }}">Users</a>
> <a href="{{ action('UsersController@view',
	array('user_id'=>$user->id) )}}">
	{{$user->given_name}} {{$user->family_name}}</a>
	> Edit user profile

@stop

@section('content')

<form action="{{ action( 'UsersController@handle_edit_profile',
	array('user_id'=>$user->id) ) }}" method="post">

<table>

<tr>
<td> {{ Form::label('given_name', 'Given name') }} </td>
<td>
	{{ Form::text('given_name',$user->given_name) }}
</td>
</tr>

<tr>
<td> {{ Form::label('family_name', 'Family name') }} </td>
<td>
	{{ Form::text('family_name',$user->family_name) }}
</td>
</tr>

<tr>
<td> {{ Form::label('email', 'E-mail') }} </td>
<td>
	{{ Form::text('email',$user->email) }}
</td>
</tr>

</table>

<p><input type="submit" value="Submit"></p>
</form>

@stop
