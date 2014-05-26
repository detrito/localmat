<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lm_users', function($table)
		{
			$table->increments('id');
			$table->string('email', 64)->unique();
			$table->string('given_name', 64);
			$table->string('family_name', 64);
			$table->string('password', 64);
			$table->boolean('enabled');
			$table->boolean('admin');
			$table->timestamps();
			$table->softDeletes();
			/**
			 * Laravel 4.1.26 introduces security improvements for "remember me" cookies.
			 */
			$table->string('remember_token',100);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lm_users');
	}

}
