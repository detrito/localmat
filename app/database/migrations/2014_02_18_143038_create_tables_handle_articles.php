<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablesHandleArticles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lm_fields', function($table)
		{
			$table->increments('id');
			$table->string('name', 64)->unique();
			$table->string('type',64);
			$table->boolean('required');
		});
	
		Schema::create('lm_categories', function($table)
		{
			$table->increments('id');
			$table->string('name', 64)->unique();
			$table->string('article_class', 64);
		});

		Schema::create('lm_articles', function($table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->integer('category_id')->unsigned();
			$table->foreign('category_id')->references('id')->on('lm_categories');
			// morphs create tables: proprieties_id (integer) and proprieties_type (string)
			$table->morphs('proprieties');
		});

		Schema::create('lm_articles_singles', function($table)
		{
			$table->increments('id');
			$table->boolean('borrowed');
		});

		Schema::create('lm_articles_amounts', function($table)
		{
			$table->increments('id');
			$table->integer('available_items')->unsigned();
			$table->integer('total_items')->unsigned();
		});

		Schema::create('lm_attributes', function($table)
		{
			$table->increments('id');
			$table->integer('article_single_id')->unsigned();
			$table->foreign('article_single_id')->references('id')->on('lm_articles_singles');
			$table->integer('field_id')->unsigned();
			$table->foreign('field_id')->references('id')->on('lm_fields');
			$table->string('value', 256);
		});

		Schema::create('lm_categories_fields', function($table)
		{
			$table->integer('category_id')->unsigned();
			$table->foreign('category_id')->references('id')->on('lm_categories');
			$table->integer('field_id')->unsigned();
			$table->foreign('field_id')->references('id')->on('lm_fields');
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lm_categories_fields');
		Schema::drop('lm_attributes');
		Schema::drop('lm_articles');
		Schema::drop('lm_articles_singles');
		Schema::drop('lm_articles_amounts');
		Schema::drop('lm_categories');
		Schema::drop('lm_fields');
	}
}
