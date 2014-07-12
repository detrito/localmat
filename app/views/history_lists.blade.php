@extends('layout')

@section('title')
Borrowing history
@stop

@section('content')

<p>Status
<select name="dropdown" onChange="document.location = this.value" value="GO">
@foreach($status_names as $status)
<option value="{{ action('HistoryController@lists', array(
		'status_name'=>$status)) }}"
	@if($status_name == $status)
		selected
	@endif
	>{{ strtolower($status) }}</option>	
@endforeach
</select>
</p>


@if ( empty($history->first()) )
	<p>No articles habe been borrowed yet.</p>
@else
	<table>
		<thead>
			<tr>
				<th>Article</th>
				<th>Items</th>
				<th>User</th>
				@if($status_name == 'returned')
					<th>Returned date</th>
				@else
					<th>Borrowed date</th>
				@endif
				<th>Time span</th>
			</tr>
		</thead>
		<tbody>
			@foreach($history as $history_item)
			
				@if(isset($history_item->user))
					<?php $user = $history_item->user ?>
					<tr>
				@else
					{{-- Load the attributes of the softDeleted user --}}
					<?php $user = User::withTrashed()->find($history_item->user_id) ?>
					<tr class="inactive">
				@endif
				
                    <td><a href="{{
						action('ArticlesController@view',
							array('article_id'=>$history_item->article_id) )}}">
						{{ $history_item->article->category->name }}</a>
					</td>
					<td>
						@if($history_item->amount_items == 0)
							1
						@else
							{{ $history_item->amount_items }}
						@endif
					</td>
					<td>
						<a href="{{
							action('UsersController@view',
								array('user_id'=>$history_item->user_id) )}}">
							{{ $user->given_name }}
							{{ $user->family_name }}
						</a>
					</td>
					@if($status_name == 'returned')
						<td>{{$history_item->getReturnedDate()}}</td>
					@else
						<td>{{$history_item->getBorrowedDate()}}</td>
					@endif
					<td>{{$history_item->getTimeSpan()}}</td>
				</tr>		
			@endforeach
		</tbody>
	</table>
	
	<?php echo $history->links(); ?>
@endif

@stop

