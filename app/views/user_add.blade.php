@extends('layout')

@section('content')

<form action="{{ action('UsersController@handle_add') }}" method="post">
<table>

<tr>
<td> {{ Form::label('given_name', 'Given name') }} </td>
<td> {{ Form::text('given_name') }} </td>
</tr>
<tr/>
<tr>

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
<td> {{ Form::label('active', 'Active') }} </td>
<td> {{ Form::checkbox('active', 1, true); }} </td>
</tr>

<tr>
<td> {{ Form::label('admin', 'Administrator') }} </td>
<td> {{ Form::checkbox('admin', 1, false); }} </td>
</tr>

</table>

<p><input type="submit" value="Submit"></p>
</form>

@stop
