<?php

class ArticleAmount extends BaseEloquent
{
	// Database table used by the model	
	protected $table = 'lm_articles_amounts';

    public $timestamps = false;

    public function article()
    {
        return $this->morphOne('Article', 'proprieties');
    }

	/*
	 * Functions called from ArticleController to add, view, edit, delete, ...
	*/

	public static function callLists($status_name='all', $category_id, $field_id = Null)
	{
		// Get list of all categories				
		$categories = Category::all()->sortBy('name');

		// Get list of status names
		$status_names = History::getArticleStatusNames();

		// Retrive amount data (avaibale items, total items, ..) from the article
		$article = Category::find($category_id)->articles()->first();
		
		// Retrive history of this article
		$history = $article->history()
			->with('user')
			->orderBy('created_at','desc')
			->get();

		return View::make('article_amount_lists',
			compact('categories','status_names','article','history'))
			->with( array('status_name'=>$status_name,
				'category_id'=>$category_id,
				'field_id'=>$field_id) );
	}
	
	public static function callView($article)
	{
		return self::callLists('all', $article->category->id);
	}
	
	public static function callEdit($article)
	{
		return Redirect::action('CategoriesController@edit',
			array('category_id'=>$article->category->id) ); 
	}
	
	public static function callDelete($article)
	{
		// delete the history of this Article
		$article->history()->delete();
		
		// delete the ArticleAmount
		$article_single = $article->proprieties()->delete();
		
		$category = $article->category();
		
		// now delete the Article
		$article->delete();
		
		// delete also the category
		$category->delete();
		
		// FIXME check if a previous page exists
		return Redirect::action('ArticlesController@index')
			->with('flash_notice', 'Article and category successfully deleted.');
	}
}

