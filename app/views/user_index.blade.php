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
                    <th>Given name</th>
                    <th>Family name</th>
					<th>E-mail</th>
					<th>Enabled</th>
					<th>Admin</th>
					<th>Actions</th>                    
					</tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->given_name }}</td>
					<td>{{ $user->family_name }}</td>                    
					<td>{{ $user->email }}</td>
					<td>{{ $user->enabled }}</td>
					<td>{{ $user->admin }}</td>
                    <td>
                        <a href="#" class="btn btn-default">Edit</a>
                        <a href="#" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
@endif


@stop
