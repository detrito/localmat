@extends('layout')

@section('title')
<a href="{{ action('UsersController@index')}}">Users</a>
> {{$user->given_name}} {{$user->family_name}}
@stop

@section('content')

<h3>User data:</h3>

<table>
	<tr>
		<td>Given Name</td>
		<td>{{$user->given_name}}</td>
	</tr>
	<tr>
		<td>Family Name</td>
		<td>{{$user->family_name}}</td>
	</tr>
		<tr>
		<td>E-mail</td>
		<td><a href="mailto:{{$user->email}}">{{$user->email}}</a></td>
	</tr>
</table>

<h3>Currently borrowed articles:</h3>

@if ( empty($history_borrowed->first()) )
	<p>There are no articles borrowed by this user.</p>
@else
	<table>
		<thead>
			<tr>
				<th>Id</th>
				<th>Category</th>
				<th>Borrowed date</th>
			</tr>
		</thead>
		</tbody>
			@foreach($history_borrowed as $history_item)
				<tr>
					<td>{{$history_item->id}}</td>
					<td>{{$history_item->article->category->name}}</td>
					<td>{{$history_item->getBorrowedDate()}}</td>
				</tr>		
			@endforeach
		</tbody>
	</table>
@endif

<h3>Borrowing history:</h3>

@if ( empty($history_all->first()) )
	<p>This user did not borrowed articles yet.</p>
@else
	<table>
		<thead>
			<tr>
				<th>Id</th>
				<th>Category</th>
				<th>Borrowed date</th>
				<th>Time span</th>
			</tr>
		</thead>
		</tbody>
			@foreach($history_all as $history_item)
				<tr>
					<td>{{$history_item->id}}</td>
					<td>{{$history_item->article->category->name}}</td>
					<td>{{$history_item->getBorrowedDate()}}</td>
					<td>{{$history_item->getTimeSpan()}}</td>
				</tr>		
			@endforeach
		</tbody>
	</table>
@endif

@stop

