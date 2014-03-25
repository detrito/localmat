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

{{-- Loop througt categories --}}
@foreach($categories as $category)
	<h3><a href="{{ action('ArticlesController@view',
	array('status'=>'all','category_name'=>$category->name) ) }}">
		{{ $category->name }}</a></h3>

	@if (empty($category->articles->first()))
		<p>There are no articles in this category.</p>
	@else

	<table>
            <thead>
                <tr>
                    <th>Id</th>					
					{{-- Loop throught fields who belongs to this category --}}
					@foreach ($category->articles()->first()->attributes as $attribute)
						<th>{{ $attribute->field->name }}</th>
					@endforeach

					@if (Auth::check() && Auth::user()->admin)
					<th>Admin</th>
					@endif
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
@endforeach

@stop

