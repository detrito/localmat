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
user: any e-mail listed under
<a href="{{ action('UsersController@index') }}">Users</a><br />
pass: 123asd</p>
<p>LocalMat is written in PHP and is based on the <a href="http://laravel.com/">
Laravel</a> framework. LocalMat requires PHP >= 5.3.7 and MySQL. The source code is available on this public <a href="http://github.com/detrito/localmat/">GitHub git repository</a> under
the <a href="http://www.gnu.org/licenses/quick-guide-gplv3.html">GPLv3</a>
license. Patches and bug reports can be sent over GitHub or by 
{{ HTML::mailto('detrito(at}inventati(dot}org','e-mail') }}.

@stop
