@extends('layout')

@section('title')

<a href="{{ action('ArticlesController@lists')}}">Articles</a>
	> Article {{ $article->id }}

@stop

@section('content')

<h3>Description</h3>

	<table>
            <thead>
                <tr>
                    <th>Id</th>
					{{-- Loop throught fields --}}
					@foreach ($field_names as $field_name)
						<th>{{ $field_name }}</th>
					@endforeach

                </tr>
            </thead>

            <tbody>				
				<tr>
                    <td>{{ $article->id }}</td>
					
					{{-- Loop througt article's field-data --}}
						@foreach ($article->proprieties->fieldData as $field_data)
							<td>{{ $field_data->value }}</td>
						@endforeach
                </tr>
            </tbody>
        </table>
        
@if (Auth::check())
	<h3>Actions</h3>
	<a href="#">Borrow</a> -

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
	@if ($article->borrowed)
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
				<th>Id</th>
				<th>User</th>
				<th>Borrowed date</th>
				<th>Time span</th>
			</tr>
		</thead>
		<tbody>
			@foreach($history as $history_item)
				<tr>
					<td>{{$history_item->id}}</td>
					<td><a href="{{
						action('UsersController@view',
							array('user_id'=>$history_item->user->id) )}}">
						{{$history_item->user->given_name}}
						{{$history_item->user->family_name}}
						</a>
					</td>
					<td>{{$history_item->getBorrowedDate()}}</td>
					<td>{{$history_item->getTimeSpan()}}</td>
				</tr>		
			@endforeach
		</tbody>
	</table>
@endif

@stop

