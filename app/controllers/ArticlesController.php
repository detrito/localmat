<?php

class ArticlesController extends BaseController
{

    public function index($order = 'id')
    {
		switch($order)
		{
			case "category":
				$categories = Category::all();
				$articles = Article::all();	
				return View::make('article_list_by_categories', compact('categories','articles'));	
			default:
				$articles = Article::orderBy($order)->get();
				return View::make('article_list', compact('articles'));
		}
    }

	public function add()
	{
		$category = new Category;
		$categories = $category::all();
		return View::make('article_add', compact('categories'));
	}

	public function handle_add()
	{
		return Redirect::action('ArticlesController@add');
	}
}
