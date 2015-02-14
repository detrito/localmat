<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFieldTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('lm_fields', function($table)
		{
			// add a "main" column: valus of these fields will be displayed
			// in compact tables like an id
			$table->boolean('main');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('lm_fields', function($table)
		{
			$table->dropColumn('main');
		});
	}

}
