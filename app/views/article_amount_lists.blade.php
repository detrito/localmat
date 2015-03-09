@extends('layout')

@section('title')

@if(isset($article))
	<a href="{{ action('ArticlesController@lists')}}">Articles</a>
	> {{ $article->category->name }}
@else
	Articles
@endif

@stop

@section('content')

<p>Category
<select name="dropdown" onChange="document.location = this.value" value="GO">
<option value=""></option>
@foreach($categories as $category_item)
<option value="{{ action('ArticlesController@lists', array(
		'status_name'=>$status_name,
		'category_id'=>$category_item->id,
		'field_id'=>$field_id)) }}"
	@if($category_item->id == $category_id)
		selected
	@endif
	>{{ strtolower($category_item->name) }}</option>
@endforeach
</select>
</p>

<h3>Articles availability</h3>

<table>
	<thead>
		<tr>
			<th>Item</th>
			<th>Amount value</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Available items</td>
			<td>{{ $article->proprieties->available_items }}</td>
		</tr>
		<tr>
			<td>Total items</td>
			<td>{{ $article->proprieties->total_items }}</td>
		</tr>
	</tbody>
</table>


@if (Auth::check() && Auth::user()->enabled)
	<h3>Borrow articles</h3>
	<form action="{{ action('HistoryController@handle_borrow') }}" method="post">
		<p>
		<input type="hidden"  name="{{$article->id}}"  value="{{ $article->id }}">
		{{ Form::label('amount_items', 'Amount items') }}
		{{ Form::text('amount_items') }}
		</p>
		
		<p>
		<input type="submit" value="Borrow">
		</p>
	</form>
	
	@if (Auth::user()->admin)
		<h3>Actions</h3>
		<a href="{{
			action('ArticlesController@edit',
			array($article->id))
			}}">Edit</a> - 
		<a href="{{
			action('ArticlesController@delete',
			array($article->id))
			}}">Delete category and articles</a>
	@endif
@endif

<h3>History</h3>
@if ( empty($history->first()) )
	<p>This article has never be borrowed.</p>
@else
	<table>
		<thead>
			<tr>
				<th>User</th>
				<th>Items</th>
				<th>Borrowed date</th>
				<th>Time span</th>
			</tr>
		</thead>
		<tbody>
			@foreach($history as $history_item)
				<tr>
					<td><a href="{{
						action('UsersController@view',
							array('user_id'=>$history_item->user->id) )}}">
						{{$history_item->user->given_name}}
						{{$history_item->user->family_name}}
						</a>
					</td>
					<td>{{ $history_item->amount_items }}</td>
					<td>{{$history_item->getBorrowedDate()}}</td>
					<td>{{$history_item->getTimeSpan()}}</td>
				</tr>		
			@endforeach
		</tbody>
	</table>
@endif


@stop

