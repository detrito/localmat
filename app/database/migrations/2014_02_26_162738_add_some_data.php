<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeData extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		/*
		 * ADD SOME FIELDS
		 */
		$field = new Field;
		$field->name = "Description";
		$field->type = "text";
		$field->save();

		$field = new Field;
		$field->name = "Corde statique";
		$field->type = "boolean";
		$field->save();

		$field = new Field;
		$field->name = "Longueur";
		$field->type = "integer";
		$field->save();

		/*
		 * ADD SOME CATEGORIES
		 */		
		$category = new Category;
		$category->name = "Perforateur";
		$category->save();
		
		$field = Field::whereName('Description')->first();
		$category->fields()->save($field);
		
		$category = new Category;
		$category->name = "Corde";
		$category->save();

		$field = Field::whereName('Longueur')->first();
		$category->fields()->save($field);
		$field = Field::whereName('Corde statique')->first();
		$category->fields()->save($field);

		/*
		 * ADD SOME ARTICLES
		 */
		
		// add a Perseuse article
		$category = Category::whereName('Perforateur')->first();
	
		$article = new Article;
		$article->category()->associate($category);
		$article->save();
	
		// add one attribute to the Article perseuse
		$field = Field::whereName('Description')->first();	
		$attribute = new Attribute;
		$attribute->value = "Bosch PSR XXX";
		$attribute->field()->associate($field);
		$attribute->article()->associate($article);	
		$attribute->save();

		// add a Corde article
		$category = Category::whereName('Corde')->first();
	
		$article = new Article;
		$article->category()->associate($category);
		$article->save();

		/// add one attribute to the article Corde
		$field = Field::whereName('Corde statique')->first();	
		$attribute = new Attribute;
		$attribute->value = "1";
		$attribute->field()->associate($field);
		$attribute->article()->associate($article);	
		$attribute->save();

		/// add another attribute to the article Corde
		$field = Field::whereName('Longueur')->first();
		$attribute = new Attribute;
		$attribute->value = "30";
		$attribute->field()->associate($field);
		$attribute->article()->associate($article);	
		$attribute->save();
	}

	public function down()
	{
		// delete all attributes
		$attributes = Attribute::all();
		foreach ($attributes as $attribute)
		{
			$attribute->delete();
		}
		

		// delete all articles	
		$articles = Article::all();
		foreach ($articles as $article)
		{
			$article->delete();
		}

		// delete all categories	
		$categories = Category::all();
		foreach ($categories as $category)
		{
			$category->fields()->detach();
			$category->delete();
		}

		// delete all fields	
		$fields = Field::all();
		foreach ($fields as $field)
		{
			$field->delete();
		}
	}

}
