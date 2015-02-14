@extends('layout')


@section('title')

Home

@stop

@section('content')

<p>This is a demonstration website for the software
<a href="{{ Config::get('localmat.url')}}">LocalMat</a>
{{ Config::get('localmat.version')}}</p>

<p>LocalMat is a MVC (Model View Controller) web application created to manage
the equipement of caving organisations.<p>
First, enter the Categories (e.g. helmet, rope, ...) and the Fields (e.g. brand,
cord length, serial number, year of introduction, ...) that you wish. You can
then start to add some Articles to those Categories. These Articles can be
browsed and listed by their status (borrowed or available) and by their
FieldData. Users can borrow and return the Articles, and their History can also
be visualised.
</p>

<p>You can <a href="{{url('login')}}">login</a> as <strong>administrator</strong>
with following credentials:<br />
user: admin@local.mat<br />
pass: admin</p>

<p>You can also login as <strong>unprivileged user</strong>
with following credentials:<br />
<?php
	$user_id = Faker\Factory::create('en_GB')->numberBetween(2, 10);
	$user = User::withTrashed()->findOrFail( $user_id );
?>
{{ $user->email }}<br />
pass: 123asd</p>
<p>LocalMat is written in PHP and is based on the <a href="http://laravel.com/">
Laravel</a> framework. LocalMat requires PHP >= 5.3.7 and MySQL. The source code is available on this public <a href="http://github.com/detrito/localmat/">GitHub git repository</a> under
the <a href="http://www.gnu.org/licenses/quick-guide-gplv3.html">GPLv3</a>
license. Patches and bug reports can be sent over GitHub or by 
{{ HTML::mailto('detrito(at}inventati(dot}org','e-mail') }}.


<h3>Last borrowed articles</h3>

@if ( empty($history_borrowed->first()) )
	<p>No articles habe been borrowed yet.</p>
@else
	<table>
		<thead>
			<tr>
				<th>Article</th>
				<th>{{ $main_field_name }}</th>
				<th>Items</th>
				<th>User</th>
				<th>Borrowed date</th>
				<th>Time span</th>
			</tr>
		</thead>
		<tbody>
			@foreach($history_borrowed as $history_item)
			
				@if(isset($history_item->user))
					<?php $user = $history_item->user ?>
					<tr>
				@else
					{{-- Load the attributes of the softDeleted user --}}
					<?php $user = User::withTrashed()->find($history_item->user_id) ?>
					<tr class="inactive">
				@endif
				
                    <td><a href="{{
						action('ArticlesController@view',
							array('article_id'=>$history_item->article_id) )}}">
						{{ $history_item->article->category->name }}</a>
					</td>
					<td>
						@if($history_item->article->getMainField() != 0)
							{{ $history_item->article->getMainField() }}
						@endif
					</td>
					
					<td>
						@if($history_item->amount_items != 0)
							{{ $history_item->amount_items }}
						@endif
					</td>
					<td><a href="{{
						action('UsersController@view',
							array('user_id'=>$user->id) )}}">
						{{$user->given_name }}
						{{$user->family_name }}
						</a>
					</td>
					<td>{{$history_item->getBorrowedDate()}}</td>
					<td>{{$history_item->getTimeSpan()}}</td>
				</tr>		
			@endforeach
		</tbody>
	</table>
@endif

<h3>Last returned articles</h3>

@if ( empty($history_returned->first()) )
	<p>No articles habe been returned yet.</p>
@else
	<table>
		<thead>
			<tr>
				<th>Article</th>
				<th>{{ $main_field_name }}</th>
				<th>Items</th>
				<th>User</th>
				<th>Returned date</th>
				<th>Time span</th>
			</tr>
		</thead>
		<tbody>
			@foreach($history_returned as $history_item)
			
				@if(isset($history_item->user))
					<?php $user = $history_item->user ?>
					<tr>
				@else
					{{-- Load the attributes of the softDeleted user --}}
					<?php $user = User::withTrashed()->find($history_item->user_id) ?>
					<tr class="inactive">
				@endif
				
                    <td><a href="{{
						action('ArticlesController@view',
							array('article_id'=>$history_item->article_id) )}}">
						{{ $history_item->article->category->name }}</a>
					</td>
					<td>
						@if($history_item->article->getMainField() != 0)
							{{ $history_item->article->getMainField() }}
						@endif
					</td>
					
					<td>
						@if($history_item->amount_items != 0)
							{{ $history_item->amount_items }}
						@endif
					</td>
					<td><a href="{{
						action('UsersController@view',
							array('user_id'=>$user->id) )}}">
						{{$user->given_name }}
						{{$user->family_name }}
						</a>
					</td>
					<td>{{$history_item->getReturnedDate()}}</td>
					<td>{{$history_item->getTimeSpan()}}</td>
				</tr>		
			@endforeach
		</tbody>
	</table>
@endif



@stop
