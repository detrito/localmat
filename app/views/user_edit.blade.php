@extends('layout')

@section('title')

<a href="{{ action('UsersController@index')}}">Users</a>
> <a href="{{ action( 'UsersController@view',
	array('user_id'=>$user->id) ) }}">{{$user->given_name}} {{$user->family_name}} </a>
	> Edit

@stop

@section('content')

<p>Action
<select name="dropdown" onChange="document.location = this.value" value="GO">
<option value=""></option>
@foreach($edit_options as $current_edit_option)
<option value="{{ action('UsersController@edit', array(
		'user_id' => $user->id,
		'edit_option'=>$current_edit_option)) }}"
	@if($edit_option == $current_edit_option)
		selected
	@endif
	>{{ strtolower($current_edit_option) }}</option>	
@endforeach
</select>
</p>


@if($edit_option == "edit_profile")
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
@elseif($edit_option == "edit_password")
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
@elseif($edit_option == "edit_permissions")
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
@elseif($edit_option == "delete_restore")
	<p>
	Deleted user are no more listed in the user listed. Their history is horewer
	preserved in the database. Deleted user can always be restored back as normal
	users.
	</p>
	
	<p>
	@if (! isset($user->deleted_at))
		Current status: not trashed.<br />
		<a href="{{ action('UsersController@trash',
			array($user->id)) }}">Trash {{$user->given_name}} {{$user->family_name}}</a>
	@else
		Current status: trashed.<br />
		<a href="{{ action('UsersController@restore',
			array($user->id)) }}">Restore {{$user->given_name}} {{$user->family_name}}</a>
	@endif
	</p>
@endif
		
@stop

