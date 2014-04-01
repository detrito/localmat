@extends('layout')

@section('title')

@if(isset($category_name))
	<a href="{{ action('ArticlesController@view')}}">Articles</a>
	> {{ $category_name }} > {{ $status_name }}
@else
	Articles
@endif

@stop

@section('content')

<p>Category
<select name="dropdown" onChange="document.location = this.value" value="GO">

@foreach($category_names as $name)
<option value="{{ action('ArticlesController@view',
		array('status_name'=>$status_name,'category_name'=>$name) )}}"
	@if($name == $category_name)
		selected
	@endif
	>{{ strtolower($name) }}</option>
@endforeach
</select>
</p>

<p>Status
<select name="dropdown" onChange="document.location = this.value" value="GO">
@foreach($status_names as $status)
	<option value="{{ action('ArticlesController@view',
		array('status_name'=>$status,'category_name'=>$category_name) )}}"
	@if($status_name == $status)
		selected
	@endif
	>{{ strtolower($status) }}</option>	
@endforeach
</select>
</p>

@if ( empty($articles->first()) )
	<p>There are no articles in this category.</p>
@else
	@if (Auth::check() && Auth::user()->enabled)	
		<form action="{{ action('ArticlesController@handle_borrow',
			array('category_name'=>$category_name, 'field_name'=>$field_name,
				'status_name'=>$status_name)) }}" method="post">
	@endif
	<table>
            <thead>
                <tr>
					{{-- column for the checkboxes --}}					
					@if (Auth::check() && Auth::user()->enabled)
					<th></th>
					@endif

                    <th>Id</th>
					{{-- Loop throught fields --}}
					@foreach ($field_names as $field_name)
						<th><a href="{{ action('ArticlesController@view',
						array('status_name'=>$status_name,
						'category_name'=>$category_name,
						'field_name'=>$field_name) ) }}">
						{{ $field_name }}</a></th>
					@endforeach

					<th>Status</th>

					@if (Auth::check() && Auth::user()->admin)
					<th>Admin</th>
					@endif
                </tr>
            </thead>
            <tbody>
				
				@foreach($articles as $article)
				<tr>
					{{-- display checkbox for borrowing only if user is logged in --}}
					@if (Auth::check() && Auth::user()->enabled)
					<th><input name="{{$article->id}}" type="checkbox" value="{{$article->id}}"
					{{-- disable the checkbox if the artice is already borrowed --}}					
					@if(isset($article->history_id))
						disabled
					@endif
					></th>
					@endif

                    <td>{{ $article->id }}</td>
					
					{{-- Loop througt article's attributes --}}
						@foreach ($article->attributes as $attribute)
							<td>{{ $attribute->value }}</td>
						@endforeach
					
					<td>
					@if (isset($article->history_id))
						Borrowed
					@else
						Available
					@endif
					</td>

					@if (Auth::check() && Auth::user()->admin)
                    <td>
                        <a href="{{
							action('ArticlesController@edit',
							array($article->id))
							}}">Edit</a>
						<a href="{{
							action('ArticlesController@delete',
							array($article->id))
							}}">Delete</a>
                    </td>
					@endif
                </tr>
				@endforeach
            </tbody>
        </table>
		@if (Auth::check() && Auth::user()->enabled)
			<p><input type="submit" value="Borrow"></p>
			</form>
		@endif
@endif
@stop

