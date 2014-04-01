<?php

class ArticlesController extends BaseController
{
	public function index()
	{
		return $this->view();
	}

    public function view($status_name='all', $category_name = 'all', $field_name = 'id')
    {
		// get list of all categories				
		$categories = new Category;
		$category_names = $categories->getNames();

		// get list of status
		// FIXME implement this in an indipendent class
		$status_names = array('all','available','borrowed');
		
		switch($category_name)
		{
			case "all":
				$categories = Category::with('articles','articles.attributes')->get();

				//FIXME implement view of other status
				return View::make('article_view_all',
					compact('categories','category_names','status_names'))
					->with( array('status_name'=>'all', 'category_name'=>'all') );
				
			default:
				// FIXME check if $category exist
				$article_model = new Article;

				$field_names = $article_model
					->whereHasCategory($category_name)
					->first()
					->getFieldNames();

				$articles = $article_model
					->whereHasCategory($category_name)
					->Status($status_name)
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

				return View::make('article_view_category',
					compact('articles','field_names','category_names','status_names'))
					->with( array('status_name'=>$status_name,
						'category_name'=>$category_name,
						'field_name'=>$field_name) );
		}
    }

	public function getArticleIdsSortedByField($articles, $field_name, $order='ASC')
	{
		$article_ids = $articles->fetch('id')->toArray();	
	
		// retrive mysql cast type of the field $field_name
		// in order to sort it as CHAR or as INTEGER
		$field_type = Field::whereName($field_name)->pluck('type');
		$field_cast_type = Field::getCastType($field_type);

		$attribute = new Attribute;	
		return $attribute
			->whereHas('Field', function($query) use($field_name)
			{
				$query->where('name', $field_name);
			})
			->select('article_id', 'value')
			// FIXME this only works on MySQL
			->orderBy(DB::raw('CAST(value AS '.$field_cast_type.')'), $order)
			->lists('article_id');
	}

	public function handle_borrow($status_name, $category_name, $field_name)
	{
		$article_ids = Input::all();
		$user = User::find( Auth::user()->id );			

		foreach($article_ids as $article_id)
		{
			$article = Article::find($article_id);

			// check if article is not already borrowed			
			if( $article->history_id == null )
			{
				$history = new History;
				$history->borrowed = true;
				$history->user()->associate($user);			
				$history->save();
	
				$article->history()->associate($history);
				$article->save();
			}
			else
			{
				return Redirect::action('ArticlesController@view',
					array('status_name'=>$status_name,
						'category_name'=>$category_name,
						'field_name'=>$field_name) )
					->with('flash_error', 'Articles $article_id already borrowed');
			}
		}
		return Redirect::action('ArticlesController@view',
			array('status_name'=>$status_name,
				'category_name'=>$category_name,
				'field_name'=>$field_name) )
			->with('flash_notice', 'Articles successfully borrowed.');
	}


	public function add($category_name='all')
	{
		// get list of all categories				
		$categories = new Category;
		$category_names = $categories->getNames();

		// FIXME check if $category exists
		if ($category_name == 'all')
		{
			return View::make('article_add', compact('category_names'))
				->with('category_name',$category_name);
		}
		else
		{
			// prepare list of field for an article of the required category
			$field = new Field;
			$fields = $field
				->whereHas('categories', function($query) use($category_name)
				{
					$query->where('name', $category_name);
				})
				->get();
			return View::make('article_add', compact('fields','category_names'))
				->with('category_name',$category_name);
		}
	}

	public function handle_add($category_name)
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

			// associate the article to the category $category_name
			$category = Category::whereName($category_name)->first();
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
			return Redirect::action('ArticlesController@add',
				array('category_name'=>$category_name) )
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
