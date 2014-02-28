<?php

class ArticlesController extends BaseController
{

    public function index($name = 'all', $order = 'id')
    {
		//echo "name:".$name."|";
		//echo "order:".$order."|";
		
		switch($name)
		{
			case "all":
				$categories = Category::all();
				return View::make('article_list', compact('categories'));
			default:
				// FIXME check if $name exist
				$categories = Category::whereName($name)->get();
				return View::make('article_list', compact('categories'))
					->with('category', $name);;								
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
