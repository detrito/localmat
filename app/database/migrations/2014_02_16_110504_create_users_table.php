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
			$table->increments('uid');
			$table->string('email', 64)->unique();
			$table->string('given_name', 64);
			$table->string('family_name', 64);
			$table->string('password', 64);
			$table->boolean('enabled');
			$table->boolean('admin');
			$table->timestamps();
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
