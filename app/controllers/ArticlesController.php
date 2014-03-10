<?php

class ArticlesController extends BaseController
{

    public function index($category = 'all', $field = 'id')
    {
		echo "name:".$category."|";
		echo "order:".$field."|";
		
		switch($category)
		{
			case "all":
				$categories = Category::with('articles','articles.attributes')->get();
				//return var_dump($categories);			
				return View::make('article_list_all', compact('categories'));
				
			default:
				// FIXME check if $category exist
				$articles = new Article;

				$articles = $articles
					->whereHasCategory($category)
					->with('attributes')
					->get();

				// Array of field-names
				$fields = array();
				foreach ($articles->first()->attributes as $attribute)
				{
					array_push($fields,$attribute->field->name);
				}

				// Order article's IDs by the value of a field
				if($field!='id')
				{
					$ordered_ids = $this->getArticleIdsSortedByField($articles,$field);
					//print_r($ordered_ids);
					$articles = $articles->sortByOrder($ordered_ids);
				}				

				return View::make('article_list_category', compact('articles','fields'))
					->with('category', $category);;
		}
    }

	public function getArticleIdsSortedByField($articles, $field, $order='ASC')
	{
		$article_ids = $articles->fetch('id')->toArray();	

		$attribute = new Attribute;		
		return $attribute
			->whereHas('Field', function($query) use($field)
			{
				$query->where('name', $field);
			})
			->select('article_id','value')
			->orderBy('value')
			->lists('article_id');
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
