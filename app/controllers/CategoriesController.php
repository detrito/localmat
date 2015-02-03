<?php

class CategoriesController extends BaseController
{
    public function index()
    {
		$categories = Category::all();
        return View::make('category_index', compact('categories'));
    }

    public function add($article_class=Null)
    {
		$fields = Field::all();
		$article_classes = Article::getArticleClasses();

        return View::make('category_form', compact('fields'))
        ->with( array('action' => 'add',
			'article_class' => $article_class,
			'article_classes' => $article_classes) );
	}

	public function handle_add($article_class)
	{
		$data = Input::all();
		// FIXME this is not that clean, but it avoids AJAX stuff in the form 
		$data['article_class'] = $article_class;	
		$field_ids = Input::except('name','dropdown');
		
		return $this->insert_data($data, $field_ids, 'add');
	}

	public function edit($category_id)
	{
		$category = Category::findOrFail($category_id);
		
		// Get Article classes
		$article_classes = Article::getArticleClasses();
		
		// Get all fields
		$fields = Field::all();	

		switch($category->article_class)
		{
			case 'ArticleSingle':
				// Get status of fields for this category
				$field_ids = $category->fields()->lists('id');
				$field_values = array();
		
				foreach($fields as $field)
				{
					if( in_array($field->id, $field_ids) )
					{
						$field_values[$field->id] = true;
					}
					else
					{
						$field_values[$field->id] = false;
					}
				}
				break;
			case 'ArticleAmount':
				$article_amount = $category->articles->first()->proprieties;
				break;
		}

        return View::make('category_form',
        	compact('category','fields','field_values','article_amount'))
	        ->with( array('action' => 'edit',
			'article_class' => $category->article_class) );
	}

	public function handle_edit($category_id)
	{
		$data = Input::all();
		$field_ids = Input::except('name','dropdown');

		return $this->insert_data($data, $field_ids, 'edit', $category_id);
	}

	private function insert_data($data, $field_ids, $action, $category_id=Null)
	{		
		$rules = array(
			'name' => 'required|alpha_spaces|unique:lm_categories'
		);

		// Force unique category-name to ignore $category_id
		if( $action == 'edit')
		{
			$rules['name'] .= ',name,'.$category_id;
		}

		$validator = Validator::make($data, $rules);
		
		if ( $validator->passes() )
		{			
			// Create Category object and load $article_class variable
			if ($action == 'add')
			{
				$category = new Category;
				$article_class = $data['article_class'];
			}
			elseif ($action == 'edit')
			{
				$category = Category::findOrFail($category_id);
				$article_class = $category->article_class;
			}
			
			// Check that at least one field has been chosed			
			if( $article_class == 'ArticleSingle' && empty($field_ids) )
			{
				return Redirect::back()
					->withErrors('You must choose at least one field!');
			}
			
			// Save the category name and, if needed, the article_class
			$category->name = $data['name'];
			
			if($action == 'add')
			{
				$category->article_class = $article_class;
			}
			$category->save();
			
 			switch($article_class)
 			{
 				case 'ArticleSingle':
 				
 					if($action == 'edit')
 					{
	 					// Detach all fields before to attach the new ones
						$category->fields()->detach();
 					}
 					
					foreach($field_ids as $field_id)
					{
						$field = Field::find($field_id);
						$category->fields()->save($field);
					}
					break;
					
				case 'ArticleAmount':
					if($action == 'add')
					{
						echo "miao";
						// Create Article 
						$article = new Article;
						$article->category()->associate($category);
						$article->save();
						
						// Create ArticleAmount and associate it to Article
						$article_amount = new ArticleAmount;
						$article_amount->save();
						$article_amount->article()->save($article);
					}
					else
					{
						$article_amount = $category->articles->first()->proprieties;
					}
					// Now save the items values
					$article_amount->available_items = $data['available_items'];
					$article_amount->total_items = $data['total_items'];
					$article_amount->save();
					break;
			}

			if($action == 'add')
			{
				$message = 'Category successfully added.';
				$message_verbose = $message.' Category ID '.$category->id.'.';
				Log::info($message_verbose);
				return Redirect::action('CategoriesController@add')
					->with('flash_notice', $message);
			}
			elseif($action == 'edit')
			{
				$message = 'Category successfully modified.';
				$message_verbose = $message.' Category ID '.$category->id.'.';
				Log::info($message_verbose);
				return Redirect::action('CategoriesController@index')
					->with('flash_notice', $message);
			}
		}
		
		return Redirect::back()
			->withErrors($validator);
	}

	public function delete($category_id)
	{
		$category = Category::findOrFail($category_id);
		if ( $category->articles()->get()->isEmpty() )
		{
			// remove all relationships to the fields for this category
			$category->fields()->detach();
			$category->delete();
			
			$message = 'Category successfully deleted.';
			$message_verbose = $message.' Category ID '.$category->id.'.';
			Log::info($message_verbose);
			return Redirect::action('CategoriesController@index')
				->with('flash_notice', $message);
		}
		return Redirect::action('CategoriesController@index')
				->with('flash_error', 'This category is still used!
					Make sure that no Article use it before to delete it.');
	}
	
	public function export_all()
	{
		// export settings
		$program_settings = Config::get('localmat.title').
			" ".Config::get('localmat.version');
		
		// sheet title
		$sheet_title = 'Export all categories';
		
		// document title
		$document_title = $sheet_title." ".
			$program_settings." ".
			Carbon::today()->toDateString();
		
		// get all categories
		$categories = Category::orderBy('name')->get();
		
		// export to an excel file
		Excel::create($document_title, function($excel)
			use($categories, $program_settings, $sheet_title, $document_title)
		{
			// set file proprieties
			$excel->setTitle($document_title);
			$excel->setCreator($program_settings);
			
			foreach($categories as $category)
			{
				// create one sheet for each category
				$excel->sheet($category->name, function($sheet) use($category)
				{
						$sheet->fromArray($category->exportArticles());
				});
			}
			
		})->export('xls');
	}
}
