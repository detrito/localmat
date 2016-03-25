@extends('layout')

@section('title')

<a href="{{ action('ArticlesController@lists')}}">Articles</a>
	> <a href="{{ action('ArticlesController@lists', array(
		'status_name'=>'all',
		'category_id'=>$article->category->id) ) }}">
		{{ $article->category->name }}</a>
	> Article {{ $article->id }}

@stop

@section('content')

<h3>Description</h3>

	<table>
            <thead>
                <tr>
					{{-- Loop throught fields --}}
					@foreach ($field_names as $field_name)
						<th>{{ $field_name }}</th>
					@endforeach

                </tr>
            </thead>

            <tbody>				
				<tr>
					{{-- Loop througt article's field-data --}}
						@foreach ($article->proprieties->fieldData as $field_data)
							<td>{{ $field_data->value }}</td>
						@endforeach
                </tr>
            </tbody>
        </table>
        
@if (Auth::check() && Auth::user()->enabled)
	<h3>Actions</h3>
	@if(!$article->proprieties->borrowed)
    <a href="{{
		action('HistoryController@handle_borrow_get',
		array($article->id))
		}}">Borrow</a> - 
	@endif
	
	@if (Auth::user()->admin)
    <a href="{{
		action('ArticlesController@edit',
		array($article->id))
		}}">Edit</a> - 
	<a href="{{
		action('ArticlesController@delete',
		array($article->id))
		}}">Delete</a>
	@endif
	
@endif


<h3>Status</h3>
	@if ($article->proprieties->borrowed)
		Borrowed
	@else
		Available
	@endif	

<h3>History</h3>
@if ( empty($history->first()) )
	<p>This article has never be borrowed.</p>
@else
	<table>
		<thead>
			<tr>
				<th>User</th>
				<th>Borrowed date</th>
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
					<td>
						<a href="{{
							action('UsersController@view',
								array('user_id'=>$user->id) )}}">
							{{ $user->given_name }}
							{{ $user->family_name }}</a>
					</td>
					<td>{{$history_item->getBorrowedDate()}}</td>
					<td>{{$history_item->getTimeSpan()}}</td>
				</tr>		
			@endforeach
		</tbody>
	</table>
@endif

@stop

