@extends('layout')

@section('title', 'Categories')

@section('content')

@if ($categories->isEmpty())
        <p>There are no fields! :(</p>
@else

{{-- print_r($categories) --}}
{{-- var_dump($categories) --}}

	<table>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Fields</th>
					<th>Admin</th>
                </tr>
            </thead>
            <tbody>
				{{-- Loop througt categories --}}
				@foreach($categories as $category)
				<tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
					<td>
						{{ implode(", ", $category->fields()->lists('name')) }}
					</td>
                    <td>
                        	<a href="{{
							action('CategoriesController@edit',
							array('category_id'=>$category->id)) }}">Edit</a>
                        	<a href="{{
							action('CategoriesController@delete',
							array($category->id)) }}">Delete</a>
                    </td>
                </tr>
				@endforeach
            </tbody>
        </table>
@endif
@stop

