<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoryTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// create a new table to handle the article's borrowing-history
		Schema::create('lm_history', function($table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('lm_users');
			$table->integer('article_id')->unsigned();
			$table->foreign('article_id')->references('id')->on('lm_articles');
			// amount_items set to 0 if article belongs to articles_singles
			$table->integer('amount_items')->unsigned()->default(0);
			$table->timestamps();
			// returnet_at set to Null if article is still borrowed
			$table->timestamp('returned_at')->nullable()->default(NULL);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lm_history');
	}
}
