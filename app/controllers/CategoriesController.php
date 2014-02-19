<?php

class CategoriesController extends BaseController
{
    public function index()
    {
		return "categories - index";
    }

	public function add()
	{
	// add some categories
	$category = new Category;
	$category->name = "Corde";
	$category->save();

	$category = new Category;
	$category->name = "Perseuse";
	$category->save();
	}
}
