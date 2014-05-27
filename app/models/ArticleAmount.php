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

	public function lists($status_name, $category_id, $field_id)
	{
		// Get list of all categories				
		$categories = Category::all();

		// Get list of status names
		$status_names = History::getStatusNames();

		// Retrive amount data (avaibale items, total items, ..) from the article
		$article = Category::find($category_id)->articles()->first();

		return View::make('article_amount_lists',
			compact('categories','status_names','article'))
			->with( array('status_name'=>$status_name,
				'category_id'=>$category_id,
				'field_id'=>$field_id) );
	}
}

