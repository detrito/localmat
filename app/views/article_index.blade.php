@extends('layout')

@section('title')

<a href="{{ action('ArticlesController@index') }}">Articles</a>

@stop

@section('content')

@if ($articles->isEmpty())
        <p>There are no articles! :(</p>
@else
	<table class="table table-striped">
            <thead>
                <tr>
                    <th>Article id</th>
                    <th>Category</th>
                    <th>Attributes</th>
					<th>Admin</th>
                </tr>
            </thead>
            <tbody>
				{{-- Loop througt articles --}}
				@foreach($articles as $article)
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
@endif
@stop

