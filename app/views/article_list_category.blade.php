@extends('layout')

@section('title')

@if(isset($category))
	<a href="{{ url('/articles/list') }}">Articles</a>
	> {{ $category }}
@else
	Articles
@endif

@stop

@section('content')

@if (empty($articles))
	<p>There are no articles! :(</p>
@else

	<table>
            <thead>
                <tr>
                    <th>Id</th>
					{{-- Loop throught fields --}}
					@foreach ($fields as $field)
						<th><a href="{{ url('/articles/list/'.$category.'/'.$field) }}">
							{{ $field }}
						</a></th>
					@endforeach
					<th>Admin</th>
                </tr>
            </thead>
            <tbody>
				
				@foreach($articles as $article)
				<tr>
                    <td>{{ $article->id }}</td>
					
					{{-- Loop througt article's attributes --}}
						@foreach ($article->attributes as $attribute)
							<td>{{ $attribute->value }}</td>
						@endforeach

                    <td>
                        <a href="#">Edit</a>
                        <a href="#">Delete</a>
                    </td>
                </tr>
				@endforeach
            </tbody>
        </table>


@endif
@stop

