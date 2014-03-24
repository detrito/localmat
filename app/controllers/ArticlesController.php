<?php

class ArticlesController extends BaseController
{
	public function index()
	{
		return 1;
	}

    public function view($status='all', $category_name = 'all', $field_name = 'id')
    {
		switch($category_name)
		{
			case "all":
				$categories = Category::with('articles','articles.attributes')->get();
				//return var_dump($categories);			
				return View::make('article_view_all', compact('categories'));
				
			default:
				// FIXME check if $category exist
				$article_model = new Article;

				//return;
				$field_names = $article_model
					->whereHasCategory($category_name)
					->first()
					->getFieldNames();

				$articles = $article_model
					->whereHasCategory($category_name)
					->Status($status)
					->with('attributes')
					->get();

				if(! empty($articles->first()))
				{
					// Order article's IDs by the value of $field_name
					if($field_name!='id')
					{
						$ordered_ids = $this->getArticleIdsSortedByField($articles,$field_name);
						$articles = $articles->sortByOrder($ordered_ids);
					}
				}			

				return View::make('article_view_category', compact('articles','field_names'))
					->with( array('status'=>$status, 'category_name'=>$category_name) );
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
			return View::make('article_add', compact('fields'))
				->with('category',$category);
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
					$value = $data[$fieldname];
				else
					$value = 0;
				$attribute->value = $value;

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

	public function edit($article_id)
	{
		$article_model = new Article;
		//var_dump($article_model);

		// get collection of this article		
		$article = $article_model
			->find($article_id);
		//var_dump($article->attributes);
		//return 1;

		// get collection of this article's fields
		$field_ids = $article_model
			->find($article_id)
			->getFieldIds();

		$fields = Field::find($field_ids);
		//var_dump($fields);

		return View::make('article_edit', compact('article','fields'))
				->with('article_id', $article_id);
	}

	public function handle_edit($article_id)
	{
		// FIXME clean this mess with some nice functions

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
			$article_model = new Article;
			
			$article = $article_model
				->find($article_id);
			
			foreach($article->attributes as $attribute)
			{
				$attribute_model = Attribute::find($attribute->id);
				$fieldname = $attribute_model->field->name;
				// check if exist, for checkboxes
				if(isset($data[$fieldname]))
					$value = $data[$fieldname];
				else
					$value = 0;
				$attribute_model->value = $value;
				$attribute_model->save();
			}
			
		// FIXME redirect to page previous the form page
		return Redirect::action('ArticlesController@index')
			->with('flash_notice', 'Article successfully modified.');
		}
		return Redirect::back()
			->withErrors($validator);		
	}

	public function delete($article_id)
	{
		$article = Article::find($article_id);
		$attribute_ids = $article->attributes->lists('id');
		
		// delete attributes who belongs to this article
		foreach($attribute_ids as $attribute_id)
		{
			$attribute = Attribute::find($attribute_id);
			$attribute->delete();
		}

		// now delete the article
		$article->delete();

		// FIXME check if a previous page exists
		return Redirect::back()
			->with('flash_notice', 'Article successfully deleted.');
	}
}
