<?php

class ArticlesController extends BaseController
{
	public function index()
	{
		return $this->lists();
	}

    public function lists($status_name='all', $category_id = Null, $field_id = Null)
    {
		// get list of status names
		$status_names = History::getStatusNames();
		
		switch($category_id)
		{
			case Null:
				// get list of all categories with artcles and attributes
				$categories = Category::with('articles','articles.attributes')->get();

				//FIXME implement view of other status
				return View::make('article_lists_all',
					compact('categories','status_names'))
					->with( array('status_name'=>$status_name,
						'category_id'=>$category_id) );
				
			default:
				// get list of all categories				
				$categories = Category::all();

				if ( Category::find($category_id)->exists() )
				{
					$fields = Category::find($category_id)->fields()->get();					
					$articles = Article::whereCategory($category_id)
						->whereStatus($status_name)
						->with('attributes')
						->get();

					if(! empty($articles->first()))
					{
						// Order article's IDs by the value of $field_name
						if($field_id!=Null)
						{
							$ordered_ids = Article::getArticleIdsSortedByField($articles,$field_id);
							$articles = $articles->sortByOrder($ordered_ids);
						}
					}

					return View::make('article_lists_category',
						compact('categories','articles','fields','status_names'))
						->with( array('status_name'=>$status_name,
							'category_id'=>$category_id,
							'field_id'=>$field_id) );
				}
				else
				{
					return Redirect::action('ArticlesController@lists')
						->with('flash_notice', 'Category '.$category_name.' not found');
				}
		}
    }

	public function view($article_id)
	{
		$article = Article::find($article_id);
		$field_names = $article->getFieldNames();
		$history = $article->history()->with('user')->get();

		return View::make('article_view', compact('article','field_names','history'));
	}

	public function add($category_id=Null)
	{
		// get list of all categories		
		$categories = Category::all();

		if ($category_id == Null)
		{
			return View::make('article_add', compact('categories'));
		}
		else
		{
			if ( Category::find($category_id)->exists() )
			{
				$category = Category::find($category_id);
				$field_ids = $category->getFieldIds();
				$fields = Field::find($field_ids)->sortByOrder($field_ids)->values();

				return View::make('article_add', compact('categories','fields','category'));
			}
			else
			{
				return Redirect::action('ArticlesController@add')
						->with('flash_notice', 'Category '.$category_name.' not found');
			}
		}
	}

	public function handle_add($category_id)
	{
		// get form data
		$data = Input::except('fields',0);			

		// decode fields array
		$fields  = json_decode(Input::get('fields'));
		
		// get rules array
		$rules = Field::getRulesArray($fields);

		// validate attributes-values
		$validator = Validator::make($data, $rules);
		
		if ($validator->passes())
		{
			$article = new Article;

			// associate the article to the category $category_name
			$category = Category::find($category_id);
			$article->category()->associate($category);
			$article->save();

			// for each field
			foreach ($fields as $field_item)
			{
				// associate an attribute to $article and to $field 
				// and set the input value to it
				$field = Field::whereName($field_item->name)->first();	
				$attribute = new Attribute;
				// check if exist, for checkboxes
				if(isset($data[$field_item->name]))
					$value = $data[$field_item->name];
				else
					$value = 0;
				$attribute->value = $value;

				$attribute->field()->associate($field);
				$attribute->article()->associate($article);	
				$attribute->save();
			}
			return Redirect::action('ArticlesController@add',
				array('category_id'=>$category_id) )
				->with('flash_notice', 'Article successfully added.');
		}
		return Redirect::back()
			->withErrors($validator);
	}

	public function edit($article_id)
	{
		// get collection of this article
		$article = Article::with('attributes')->find($article_id);

		// get collection of fields-ids of this category
		$field_ids = Category::find($article->category->id)->getFieldIds();

		// order the field collection by the attribute order and reset the keys
		$fields = Field::find($field_ids)->sortByOrder($field_ids)->values();

		return View::make('article_edit', compact('article','fields'))
				->with('article_id', $article_id);
	}

	public function handle_edit($article_id)
	{
		// get form data
		$data = Input::except('fields',0);			

		// decode fields array
		$fields  = json_decode(Input::get('fields'));

		// get rules array
		$rules = Field::getRulesArray($fields);

		// validate attributes-values
		$validator = Validator::make($data, $rules);

		if ($validator->passes())
		{
			$article = Article::find($article_id);
			
			foreach($article->attributes as $attribute)
			{
				$field_name = $attribute->field->name;

				// check if exist, for checkboxes
				if(isset($data[$field_name]))					
					$value = $data[$field_name];
				else
					$value = 0;
				$attribute->value = $value;
				$attribute->save();
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
