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
		$category = new Category;
		$categories = $category::all();
		return View::make('article_add', compact('categories'));
	}

	public function handle_add()
	{
		return Redirect::action('ArticlesController@add');
	}
}
