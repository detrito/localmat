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
					
					{{-- Loop througt article's attributes --}}
						@foreach ($article->proprieties->attributes as $attribute)
							<td>{{ $attribute->value }}</td>
						@endforeach
                </tr>
            </tbody>
        </table>

<h3>Status</h3>
	@if ($article->borrowed)
		Borrowed
	@else
		Available
	@endif	

<h3>History</h3>
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
@stop

