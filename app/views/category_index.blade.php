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
						@foreach ($category->fields as $field)
							{{ $field->name }}
							- 
						@endforeach
					</td>
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

