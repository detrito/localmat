@extends('layout')


@section('title')

Login

@stop

@section('content')
    
<form action="{{ url('login') }}" method="post">
<table>
<tr>
<td><label for="email">E-mail</label></td>
<td><input type="text" name="email"/></td>
</tr>
<tr>
<td><label for="password">Password:</label></td>
<td><input type="password" name="password"/></td>
</tr>
</table>
<p><input type="submit" value="Login"></p>
</form>


@stop
