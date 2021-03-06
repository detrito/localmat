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
					@endif
					</tr>
            </thead>
            <tbody>
            	@foreach($users as $user)
		        	
		        	@if(isset($user->deleted_at))
		        		<tr class="inactive">
		        	@else
		        		<tr>
                	@endif
                	
		            	<td><a href="{{
								action('UsersController@view',
									array('user_id'=>$user->id) )}}">
								{{ $user->given_name }} {{ $user->family_name }}</a>
						</td>
					
						@if (Auth::check() && Auth::user()->admin)
							<td>{{ $user->enabled }}</td>
							<td>{{ $user->admin }}</td>
						@endif
		            </tr>
		            @endforeach
            </tbody>
        </table>
@endif


@stop
