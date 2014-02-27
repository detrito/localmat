@extends('layout')

@section('title')

Articles (list by category)

@stop

@section('content')

@if ($articles->isEmpty())
        <p>There are no articles! :(</p>
@else

{{-- Loop througt categories --}}
@foreach($categories as $category)
	<h3>{{ $category->name }}</h3>
	<table class="table table-striped">
            <thead>
                <tr>
                    <th><a href="{{ url('articles/list') }}">Id</th>
                    <th>Category</th>
                    <th>Attributes</th>
					<th>Admin</th>
                </tr>
            </thead>
            <tbody>

				@foreach($category->articles as $article)
				<tr>
                    <td>{{ $article->id }}</td>
                    <td>{{ $article->category->name }}</td>
					
					{{-- Now loop througt article's attributes --}}
					<td>
						@foreach ($article->attributes as $attribute)
							{{ $attribute->value }}
							-
						@endforeach
					</td>
                    <td>
                        <a href="#" class="btn btn-default">Edit</a>
                        <a href="#" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
				@endforeach
            </tbody>
        </table>
@endforeach

@endif
@stop

