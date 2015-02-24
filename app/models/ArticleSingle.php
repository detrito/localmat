<?php

class ArticleSingle extends BaseEloquent
{
	// Database table used by the model	
	protected $table = 'lm_articles_singles';

    public $timestamps = false;

    public function article()
    {
        return $this->morphOne('Article', 'proprieties');
    }

	// Article __has_many__ FiedDatum
	public function fieldData()
	{
		return $this->hasMany('FieldDatum');
	}

	// Select ArticlesSingles who belongs to category $name
	public function scopewhereCategory($query, $category_id)
	{
		return $query->whereHas('Article', function($query) use($category_id)
		{
			return $query->whereHas('Category', function($query) use($category_id)
			{
				$query->where('id', $category_id);
			});
		});
	}

	// Select ArticlesSingles with status $status_name
	public function scopewhereStatus($query, $status_name)
	{
		switch($status_name)
		{
			case 'all':
				return $query;
				break;
			case 'available':
				return $query->where('borrowed','=',0);
				break;
			case 'borrowed':
				return $query->where('borrowed','=',1);
				break;
		}
	}

	// Sort the article_single model by the vaules of one of its fields
	public function scopeSortByField($query, $field_id, $order='asc')
	{
		// get array of article_single_id sorted by field
		$ordered_ids = $this->getArticleIdsSortedByField($field_id, $order);
		// explose array to string
		$string_ids = implode(',', $ordered_ids);
		// sort the article model by the array of ids $string_ids
		$articles_singles = $query->orderByRaw(DB::raw( "FIELD(id, $string_ids)" ));
	}

	// Return array of article-ids sorted by a field value
	public function getArticleIdsSortedByField($field_id, $order)
	{
		$article_ids = $this->lists('id');
		
		// retrive mysql cast type of the field $field_name
		// in order to sort it as CHAR or as INTEGER
		$field_type = Field::find($field_id)->type;
		$field_cast_type = Field::getCastType($field_type);

		$field_data = new FieldDatum;
		
		return $field_data
			->whereHas('Field', function($query) use($field_id)
			{
				$query->where('id', $field_id);
			})
			->whereIn('article_single_id', $article_ids)	
			->select('article_single_id', 'value')
			// FIXME this only works on MySQL
			->orderBy(DB::raw('CAST(value AS '.$field_cast_type.')'), $order)
			->lists('article_single_id');
	}

	// Return array of fields-names of an article
	public function getFieldNames()
	{
		$field_names = array();
		foreach ($this->fieldData()->get() as $field_datum)
		{
			array_push($field_names, $field_datum->field->name);
		}
		return $field_names;
	}
	
	// Return array of fields-ids of an article
	public function getFieldIds()
	{
		$field_ids = array();
		foreach ($this->fieldData()->get() as $field_datum)
		{
			array_push($field_ids, $field_datum->field->id);
		}
		return $field_ids;
	}

	// get the User who is actually borrowing this Article 
	public function getBorrowingUser()
	{
		return History::whereArticle($this->article->id)->whereBorrowed()->first()->user;
	}

	/*
	 * Functions called from ArticleController to add, view, edit, delete, ...
	*/
	
	public static function callLists($status_name, $category_id, $field_id, $order)
	{
		// Get list of all categories				
		$categories = Category::all()->sortBy('name');

		// Get list of status names
		$status_names = History::getArticleStatusNames();

		// Get fields who belongs to this category
		$fields = Category::find($category_id)->fields()->get();					

		// Get the articles who belongs to $category and to $status
		$articles_singles = ArticleSingle::whereCategory($category_id)
			->whereStatus($status_name)
			->with('article','fieldData');
		
		if(! empty( $articles_singles->first() ))
		{
			if($field_id!=Null)
			{
				// Sort the article model by the value of a field
				$articles_singles->sortByField($field_id, $order);
			}
		}
		
		// paginate
		$articles_singles = $articles_singles
			->paginate(Config::get('localmat.itemsPerPage'));

		return View::make('article_single_lists',
			compact('categories','status_names','fields','articles_singles'))
			->with( array('status_name'=>$status_name,
				'category_id'=>$category_id, 'field_id'=>$field_id,
				'order' => $order) );
	}

	public static function callView($article)
	{
		$article_single = $article->proprieties;
		$field_names = $article_single->getFieldNames();
		
		$history = $article->history()
			->with('user')
			->orderBy('created_at','desc')
			->get();

		return View::make('article_single_view', compact('article','field_names','history'));
	}
	
	public static function callAdd($category)
	{
		// Get list of all categories				
		$categories = Category::all();
	
		// Get list of fields
		$field_ids = $category->getFieldIds();
		$fields = Field::find($field_ids)->sortByOrder($field_ids)->values();
		
		return View::make('article_single_add', compact('categories','fields','category'));
	}

	public static function loadFormData()
	{
		// Get form data
		$data = Input::except('fields',0);		

		// Decode fields array
		$fields  = json_decode(Input::get('fields'));
		
		// Get rules array
		$rules = Field::getRulesArray($fields);

		// Validator for field-data values
		$validator = Validator::make($data, $rules);
		
		return array($data, $fields, $validator);
	}

	public static function callHandleAdd($category)
	{
		// load input data and prepare validator
		list($data, $fields, $validator) = self::loadFormData();
		
		if ($validator->passes())
		{
			// Create Article and associate it to $category
			$article = new Article;
			$article->category()->associate($category);
			$article->save();

			// Create ArticleSingle
			$article_single = new ArticleSingle;
			$article_single->save();

			foreach ($fields as $field_item)
			{
				// Check if input value of $field_item exist, for checkboxes
				if(isset($data[$field_item->name]))
					$value = $data[$field_item->name];
				else
					$value = 0;

				// Create a new FieldDatum to and set its value to the input value
				$field_datum = new FieldDatum;
				$field_datum->value = $value;

				// Associate the FieldDatum bute to $field and to $article_single
				$field = Field::whereName($field_item->name)->first();					
				$field_datum->field()->associate($field);
				$field_datum->articleSingle()->associate($article_single);
				$field_datum->save();
				
				// Save the polymorphic relation of $article_single to $article
				$article_single->article()->save($article);
				
			}
			
			$message = 'Article successfully added.';
			$message_verbose = $message.' Article ID '.$article->id.'.';
			Log::info($message_verbose);
			return Redirect::action('ArticlesController@add',
				array('category_id'=>$category->id) )
				->with('flash_notice', $message);
		}
		return Redirect::back()
			->withErrors($validator);
	}
	
	public static function callEdit($article)
	{
		// Load ArticleSingle (proprieties) and fieldData of the article
		$article->load('proprieties','proprieties.fieldData');

		// Get collection of fields-ids of this category
		$field_ids = Category::find($article->category->id)->getFieldIds();

		// Order the field collection by the fieldData order and reset the keys
		$fields = Field::find($field_ids)->sortByOrder($field_ids)->values();

		return View::make('article_single_edit', compact('article','fields'))
				->with('article_id', $article->id);

	}

	public static function callHandleEdit($article)
	{	
		// load input data and prepare validator
		list($data,$fields,$validator) = self::loadFormData();
		
		if ($validator->passes())
		{
			foreach($article->proprieties->fieldData as $field_datum)
			{
				$field_name = $field_datum->field->name;

				// Check if input value of $field_name exist, for checkboxes
				if(isset($data[$field_name]))					
					$value = $data[$field_name];
				else
					$value = 0;
				
				// Set the new value to $field_datum	
				$field_datum->value = $value;
				$field_datum->save();
			}
			
			$message = 'Article successfully modified.';
			$message_verbose = $message.' Article ID '.$article->id.'.';
			Log::info($message_verbose);
			// FIXME redirect to page previous the form page
			return Redirect::action('ArticlesController@index')
				->with('flash_notice', $message);
		}
		return Redirect::back()
			->withErrors($validator);		
	}
	
	public static function callDelete($article)
	{
		// delete the history of this Article
		$article->history()->delete();
		
		// delete the fieldData of this Article
		$article->proprieties->fieldData()->delete();

		// delete the ArticleSingle
		$article_single = $article->proprieties()->delete();

		// now delete the Article
		$article->delete();

		$message = 'Article successfully deleted.';
		$message_verbose = $message.' Article ID '.$article->id.'.';
		Log::info($message_verbose);
		// FIXME check if a previous page exists
		return Redirect::action('ArticlesController@index')
			->with('flash_notice', $message);
	}
	
	public static function callExport($category_id)
	{
		// retrive articles for this category 
		$articles = Category::with('articles')
			->find($category_id)->articles;
		
		// array with all articles
		$a_articles = array();
		
		foreach ($articles as $key => $article)
		{			
			// array with data to be returned
			$a_articles[$key] = array();			
			
			// append article id
			$a_articles[$key]['Id'] = $article->id;			
			
			// append field-data values
			foreach($article->proprieties->fieldData as $field_datum)
			{
				$a_articles[$key][$field_datum->field->name] = $field_datum->value;
			}
			
			// if borrowed, append user's e-mail
			if($article->proprieties()->pluck('borrowed'))
			{
				$user_string = $article->proprieties->getBorrowingUser()->email;
				$a_articles[$key]['Borrowed'] = $user_string;
			}
			else
			{
				$a_articles[$key]['Borrowed'] = 0;
			}
		}
		
		return $a_articles;
	}
}
