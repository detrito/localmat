<?php

class ArticlesController extends BaseController
{

    public function index($category = 'all', $field = 'id')
    {	
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
					->with('category', $category);
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

	public function add($category='all')
	{
		// FIXME check if $category exists
		if ($category == 'all')
		{
			$category = new Category;
			$categories = $category::all();
			return View::make('article_add_list',compact('categories'));
		}
		else
		{
			// prepare list of field for an article of the required category
			$field = new Field;
			$fields = $field
				->whereHas('categories', function($query) use($category)
				{
					$query->where('name', $category);
				})
				->get();
				//->select(');			
			return View::make('article_add', compact('fields'))
				->with('category', $category);
		}
	}

	public function handle_add($category)
	{
		// decode attributes names and values
		$rawdata = Input::except('fields',0);			
		$data = array();		
		foreach($rawdata as $k=>$v )
		{
			$data[rawurldecode($k)] = rawurldecode($v);
		}
		
		// load illuminate collection of fields from GET
		// and create $rules array
		$fields  = json_decode(Input::get('fields'));
		$rules = array();
		foreach ($fields as $field)
		{
			$rules[$field->name] = $field->rule;
		}

		// validate attributes-values
		$validator = Validator::make($data, $rules);
		
		if ($validator->passes())
		{
			$article = new Article;

			// associate the article to the category $categoryname
			$categoryname = $category;
			$category = Category::whereName($category)->first();
			$article->category()->associate($category);
			$article->save();			

			// for each field
			foreach ($fields as $field)
			{
				// associate an attribute to $article and to $field 
				// and set the input value to it
				$fieldname = $field->name;
				$field = Field::whereName($fieldname)->first();	
				$attribute = new Attribute;
				// check if exist, for checkboxes
				if(isset($data[$fieldname]))
					$attribute->value = $data[$fieldname];
				else
					$attribute->value = 0;
								
				$attribute->field()->associate($field);
				$attribute->article()->associate($article);	
				$attribute->save();
			}
			return Redirect::action('ArticlesController@add')
				->with('flash_notice', 'Article successfully added.');
		}
		return Redirect::back()
			->withErrors($validator);
	}
}
