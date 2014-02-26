<?php

class CategoriesController extends BaseController
{
    public function index()
    {
		$categories = Category::all();
        return View::make('category_index', compact('categories'));
    }
	
	public function add()
	{
		return 1;
	}
}
