@extends('layout')

@section('title')
Users

@stop


@section('content')

@if ($users->isEmpty())
        <p>There are no users! :(</p>
    @else
		<table>
            <thead>
                <tr>
                    <th>Name</th>
					@if (Auth::check() && Auth::user()->admin)
						<th>Enabled</th>
						<th>Admin</th>
						<th>Actions</th>
					@endif
					</tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td><a href="{{
						action('UsersController@view',
							array('user_id'=>$user->id) )}}">
						{{ $user->given_name }} {{ $user->family_name }}</a>
					</td>
					@if (Auth::check() && Auth::user()->admin)
						<td>{{ $user->enabled }}</td>
						<td>{{ $user->admin }}</td>
                    	<td>
                        	<a href="{{
							action('UsersController@login',
							array('user_id'=>$user->id)) }}">Log-in</a>                    	
                        	<a href="{{
							action('UsersController@edit',
							array('user_id'=>$user->id)) }}">Edit</a>
                        	<a href="{{
							action('UsersController@delete',
							array($user->id)) }}">Delete</a>
                    	</td>
					@endif
                </tr>
                @endforeach
            </tbody>
        </table>
@endif


@stop
