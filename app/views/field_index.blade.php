@extends('layout')

@section('title', 'Fields')

@section('content')

@if ($fields->isEmpty())
        <p>There are no fields! :(</p>
@else
	<table class="table table-striped">
            <thead>
                <tr>
                    <th>Field id</th>
                    <th>Name</th>
					<th>Required</th>
					<th>Main</th>
                    <th>Type</th>
					<th>Admin</th>
                </tr>
            </thead>
            <tbody>
				{{-- Loop througt articles --}}
				@foreach($fields as $field)
				<tr>
                    <td>{{ $field->id }}</td>
                    <td>{{ $field->name }}</td>
                    <td>{{ $field->required }}</td>
                    <td>{{ $field->main }}</td>
					<td>{{ $field->type }}</td>
                    <td>
                        <a href="{{
							action('FieldsController@edit',
							array('field_id'=>$field->id))
							}}">Edit</a>
                        <a href="{{
							action('FieldsController@delete',
							array($field->id))
							}}">Delete</a>
                    </td>
                </tr>
				@endforeach
            </tbody>
        </table>
@endif
@stop

