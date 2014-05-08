@extends('layout')

@section('title')

@if(isset($category_name))
	<a href="{{ action('ArticlesController@lists')}}">Articles</a>
	> {{ $category_name }} > {{ $status_name }}
@else
	Articles
@endif

@stop

@section('content')

<p>Category
<select name="dropdown" onChange="document.location = this.value" value="GO">
<option value=""></option>
@foreach($categories as $category_item)
<option value="{{ action('ArticlesController@lists', array(
		'status_name'=>$status_name,
		'category_id'=>$category_item->id,
		'field_id'=>$field_id)) }}"
	@if($category_item->id == $category_id)
		selected
	@endif
	>{{ strtolower($category_item->name) }}</option>
@endforeach
</select>
</p>

<p>Status
<select name="dropdown" onChange="document.location = this.value" value="GO">
@foreach($status_names as $status)
<option value="{{ action('ArticlesController@lists', array(
		'status_name'=>$status,
		'category_id'=>$category_id,
		'field_id' => $field_id)) }}"
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
		<form action="{{ action('HistoryController@handle_borrow',
			array('category_id'=>$category_id, 'field_id'=>$field_id,
				'status_name'=>$status_name)) }}" method="post">
	@endif
	<table>
            <thead>
                <tr>
					{{-- column for the checkboxes --}}					
					@if (Auth::check() && Auth::user()->enabled)
					<th></th>
					@endif

					{{-- ID column --}}
                    <th><a href="{{ action('ArticlesController@lists',
						array('status_name'=>$status_name,
						'category_id'=>$category_id,
						'field_id'=>Null) ) }}">Id</a>
					</th>

					{{-- Loop throught fields --}}
					@foreach ($fields as $field_item)
						<th><a href="{{ action('ArticlesController@lists',
						array('status_name'=>$status_name,
						'category_id'=>$category_id,
						'field_id'=>$field_item->id) ) }}">
						{{ $field_item->name }}</a></th>
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

					@if($article->borrowed)
						disabled
					@endif
					></th>

					@endif

                    <td><a href="{{
						action('ArticlesController@view',
							array('article_id'=>$article->id) )}}">
						{{ $article->id }}</a>
					</td>
					
					{{-- Loop througt article's attributes using field-ids as keys --}}
					@foreach($fields as $field)
						<?php
						$attributes = $article->attributes;
						$attribute = $attributes->filter(function($item) use($field) {
							return $item->field->id == $field->id;
						})->first();
						?>
						<td>
						@if(!empty($attribute))
							{{ $attribute->value }}
						@endif
						</td>
					@endforeach
				
					<td>
					@if ($article->borrowed)
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
