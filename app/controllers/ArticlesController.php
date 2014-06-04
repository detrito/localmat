<?php

class ArticlesController extends BaseController
{
	public function index()
	{
		return $this->lists();
	}

    public function lists($status_name='all', $category_id = Null, $field_id = Null)
    {		
		switch($category_id)
		{
			// If no category is specified, make list of all categories
			case Null:
				// Get list of all categories with articles and proprieties
				// (field-data or amounts)
				$categories = Category::with('articles','articles.proprieties')
					->orderBy('name')
					->get();

				// FIXME implement view of other status
				return View::make('article_lists_all',
					compact('categories','status_names'))
					->with( array('status_name'=>$status_name,
						'category_id'=>$category_id) );
						
			// If a category_id is specified
			default:				
				// Check if the category exists
				if ( Category::find($category_id)->exists() )
				{
					$category = Category::find($category_id);
				}
				else
				{
					return Redirect::action('ArticlesController@lists')
						->with('flash_notice', 'Category '.$category_name.' not found');
				}
			
				// Call the lists method of the $article_class model
				$article_class = $category->article_class;
				return $article_class::callLists($status_name, $category_id, $field_id);
		}
    }

	public function view($article_id)
	{
		$article = Article::find($article_id);
		
		// Call the view method of the $article_class model
		$article_class = $article->proprieties_type;
		return $article_class::callView($article);
	}

	public function add($category_id=Null)
	{
		if ($category_id == Null)
		{
			// Get list of categories of Articles of type ArticleSingle
			$categories = Category::where('article_class','=','ArticleSingle')->get();

			// This only shows a dropdown menu to select a category
			return View::make('article_single_add', compact('categories'));
		}
		else
		{
			if ( Category::find($category_id)->exists() )
			{
				$category = Category::find($category_id);
				
				// This method only exist for the ArticleSingle class
				if($category->article_class == 'ArticleSingle')
				{
					// Call the add method of the $article_class model
					$article_class = $category->article_class;
					return $article_class::callAdd($category);
				}
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
		$category = Category::find($category_id);
		
		// This method only exist for the ArticleSingle class
		if($category->article_class == 'ArticleSingle')
		{
			// Call the handle_add method of the $article_class model
			$article_class = $category->article_class;
			return $article_class::callHandleAdd($category);
		}
	}

	public function edit($article_id)
	{
		// Call the edit method of the $article_class model
		$article = Article::find($article_id);
		$article_class = $article->proprieties_type;
		return $article_class::callEdit($article);
	}

	public function handle_edit($article_id)
	{
		// Call the handle_edit method of the $article_class model
		$article = Article::find($article_id);
		$article_class = $article->proprieties_type;
		return $article_class::callHandleEdit($article);
	}

	public function delete($article_id)
	{
		// Call the handle_edit method of the $article_class model
		$article = Article::find($article_id);
		$article_class = $article->proprieties_type;
		return $article_class::callDelete($article);
	}

}
