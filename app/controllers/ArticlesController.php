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

		// get list of status names
		$status_names = History::getStatusNames();
		
		switch($category_name)
		{
			case "all":
				$categories = Category::with('articles','articles.attributes')->get();

				//FIXME implement view of other status
				return View::make('article_view_all',
					compact('categories','category_names','status_names'))
					->with( array('status_name'=>'all', 'category_name'=>'all') );
				
			default:
				if ( Category::whereName($category_name)->exists() )
				{
					$field_names = Article::whereCategory($category_name)
						->first()
						->getFieldNames();

					$articles = Article::whereCategory($category_name)
						->whereStatus($status_name)
						->with('attributes')
						->get();

					if(! empty($articles->first()))
					{
						// Order article's IDs by the value of $field_name
						if($field_name!='id')
						{
							$ordered_ids = Article::getArticleIdsSortedByField($articles,$field_name);
							$articles = $articles->sortByOrder($ordered_ids);
						}
					}
				
					return View::make('article_view_category',
						compact('articles','field_names','category_names','status_names'))
						->with( array('status_name'=>$status_name,
							'category_name'=>$category_name,
							'field_name'=>$field_name) );
				}
				else
				{
					return Redirect::action('ArticlesController@view')
						->with('flash_notice', 'Category '.$category_name.' not found');
				}
		}
    }

	public function add($category_name='all')
	{
		// get list of all categories				
		$categories = new Category;
		$category_names = $categories->getNames();

		if ($category_name == 'all')
		{
			return View::make('article_add', compact('category_names'))
				->with('category_name',$category_name);
		}
		else
		{
			if ( Category::whereName($category_name)->exists() )
			{
				// prepare list of field for an article of the required category
				$fields = Field::whereCategory($category_name)->get();

				return View::make('article_add', compact('fields','category_names'))
					->with('category_name',$category_name);
			}
			else
			{
				return Redirect::action('ArticlesController@add')
						->with('flash_notice', 'Category '.$category_name.' not found');
			}
		}
	}

	public function handle_add($category_name)
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
		// get collection of this article		
		$article = Article::find($article_id);
	
		// get collection of this article's fields
		$field_ids = Article::find($article_id)->getFieldIds();

		$fields = Field::find($field_ids);

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
