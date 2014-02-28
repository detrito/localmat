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

@if (empty($categories->first()->articles()))
	<p>There are no articles! :(</p>
@else

{{-- Loop througt categories --}}

@foreach($categories as $category)
	<h3><a href="{{ url('/articles/list/'.$category->name) }}">
		{{ $category->name }}</a></h3>

	<table>
            <thead>
                <tr>
                    <th>Id</th>					
					{{-- Loop throught fields who belongs to this category --}}
					@foreach ($category->articles()->first()->attributes as $attribute)
						<th>{{ $attribute->field->name }}</th>
					@endforeach

					<th>Admin</th>
                </tr>
            </thead>
            <tbody>
				
				@foreach($category->articles as $article)
				<tr>
                    <td>{{ $article->id }}</td>
					
					{{-- Now loop througt article's attributes --}}
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
@endforeach


@endif
@stop

