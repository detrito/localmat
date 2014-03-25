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
		array('status'=>$status_name,'category'=>$name) )}}"
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
		array('status'=>$status,'category'=>$category_name) )}}"
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
	<table>
            <thead>
                <tr>
                    <th>Id</th>
					{{-- Loop throught fields --}}
					@foreach ($field_names as $field_name)
						<th><a href="{{ url('/articles/list/'.$category_name.'/'.$field_name) }}">
							{{ $field_name }}
						</a></th>
					@endforeach

					@if (Auth::check() && Auth::user()->admin)
					<th>Admin</th>
					@endif
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


@endif
@stop

