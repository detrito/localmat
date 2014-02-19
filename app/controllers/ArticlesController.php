<?php

class ArticlesController extends BaseController
{
    public function index()
    {
		$articles = Article::all();
        return View::make('article_index', compact('articles'));
    }

	public function add()
	{
	/*
	// add a Perseuse article
	$category = Category::whereName('Perseuse')->first();
	
	$article = new Article;
	$article->category()->associate($category);
	$article->save();
	echo $article;

	/// add one attribute to the Article perseuse
	$field = Field::whereName('Description')->first();	
	echo $field;	
	$attribute = new Attribute;
	$attribute->value = "Bosch PSR XXX";
	$attribute->field()->associate($field);
	$attribute->article()->associate($article);	
	$attribute->save();
	*/

	// add a Corde article
	$category = Category::whereName('Corde')->first();
	
	$article = new Article;
	$article->category()->associate($category);
	$article->save();
	echo $article;

	/// add one attribute to the article Corde
	$field = Field::whereName('Corde statique')->first();	
	echo $field;	
	$attribute = new Attribute;
	$attribute->value = "1";
	$attribute->field()->associate($field);
	$attribute->article()->associate($article);	
	$attribute->save();

	/// add another attribute to the article Corde
	$field = Field::whereName('Longueur')->first();	
	echo $field;	
	$attribute = new Attribute;
	$attribute->value = "30";
	$attribute->field()->associate($field);
	$attribute->article()->associate($article);	
	$attribute->save();

	return "ok";
	}

    public function delall()
    {
		// delete all articles	
		$articles = Article::all();
		foreach ($articles as $article)
		{
			$article->delete();
		}
		return "sad.. all the articles have been deleted :(";
	}
}
