@extends('layout')

@section('title')
<a href="{{ action('UsersController@index')}}">Users</a>
> {{$user->given_name}} {{$user->family_name}}
@stop

@section('content')

<h3>Actions:</h3>
View
@if(Auth::check() && Auth::user()->id == $user->id)
<a href="{{ action( 'UsersController@handle_edit_profile',
	array('user_id'=>$user->id) ) }}">Edit profile</a>
<a href="{{ action( 'UsersController@handle_edit_password',
	array('user_id'=>$user->id) ) }}">Edit password</a>
@endif

@if (Auth::check() && Auth::user()->admin)
	<a href="{{ action( 'UsersController@handle_edit_permissions',
		array('user_id'=>$user->id) ) }}">Edit permissions</a>
	
	{{-- allow to switch user if not already logged in --}}
	@if (Auth::user()->id != $user->id)
		<a href="{{	action('UsersController@login_as',
			array('user_id'=>$user->id)) }}">Log-in</a>
	@endif
	
	@if (! isset($user->deleted_at))
		<a href="{{ action('UsersController@trash',
			array($user->id)) }}">Trash</a>
	@else
		<a href="{{ action('UsersController@restore',
			array($user->id)) }}">Restore</a>
	@endif
@endif

<h3>User data:</h3>

<table class="vertical">
	<tr>
		<th>Given Name</th>
		<td>{{$user->given_name}}</td>
	</tr>
	<tr>
		<th>Family Name</th>
		<td>{{$user->family_name}}</td>
	</tr>
		<tr>
		<th>E-mail</th>
		<td><a href="mailto:{{$user->email}}">{{$user->email}}</a></td>
	</tr>
</table>

<h3>Status:</h3>

@if($user->enabled)
	Enabled
@else
	Disabled
@endif

@if(isset($user->deleted_at))
	, <font color="#f00">TRASHED</font>
@endif

@if($user->admin)
	, Admin
@endif

<h3>Currently borrowed articles:</h3>

@if ( empty($history_borrowed->first()) )
	<p>There are no articles borrowed by this user.</p>
@else
	@if (Auth::check() && Auth::user()->enabled && Auth::user()->id == $user->id)
		<form action="{{ action('HistoryController@handle_return',
			array('user_id'=>$user->id) ) }}"
			method="post">
	@endif
	
	<table>
		<thead>
			<tr>
				@if (Auth::check() && Auth::user()->enabled && Auth::user()->id == $user->id)
				<th></th>				
				@endif
				<th>Article</th>
				<th>{{ $main_field_name }}</th>
				<th>Items</th>				
				<th>Borrowed date</th>
			</tr>
		</thead>
		<tbody>
			@foreach($history_borrowed as $history_item)
				<tr>
					@if (Auth::check() && Auth::user()->enabled && Auth::user()->id == $user->id)
						<th><input name="{{$history_item->id}}"
							type="checkbox"
							value="{{$history_item->id}}"></th>
					@endif
                    <td><a href="{{
						action('ArticlesController@view',
							array('article_id'=>$history_item->article->id) )}}">
						{{ $history_item->article->category->name }}</a>
					</td>
					
					<td>
						{{ $history_item->article->getMainField() }}
					</td>
					
					<td>
						@if($history_item->amount_items != 0)
							{{ $history_item->amount_items }}
						@endif
					</td>
					
					<td>{{$history_item->getBorrowedDate()}}</td>
				</tr>		
			@endforeach
		</tbody>
	</table>

	@if (Auth::check() && Auth::user()->enabled && Auth::user()->id == $user->id)
		<p><input type="submit" value="Return articles"></p>
		</form>
	@endif

@endif

<h3>Borrowing history:</h3>

@if ( empty($history_all->first()) )
	<p>This user did not borrowed articles yet.</p>
@else
	<table>
		<thead>
			<tr>
				<th>Article</th>
				<th>{{ $main_field_name }}</th>
				<th>Items</th>
				<th>Borrowed date</th>
				<th>Time span</th>
			</tr>
		</thead>
		<tbody>
			@foreach($history_all as $history_item)
				<tr>
                    <td><a href="{{
						action('ArticlesController@view',
							array('article_id'=>$history_item->article->id) )}}">
						{{ $history_item->article->category->name }}</a>
					</td>
					
					<td>
						{{ $history_item->article->getMainField() }}
					</td>
					
					<td>
						@if($history_item->amount_items != 0)
							{{ $history_item->amount_items }}
						@endif
					</td>
					
					<td>{{$history_item->getBorrowedDate()}}</td>
					<td>{{$history_item->getTimeSpan()}}</td>
				</tr>		
			@endforeach
		</tbody>
	</table>
@endif

@stop

