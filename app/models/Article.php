<?php

class Article extends BaseEloquent
{
	// Database table used by the model	
	protected $table = 'lm_articles';

	// Enable timestamps	
	public $timestamps = true;
	
	// Article __belongs_to_a__ Category
	public function category()
	{
		return $this->belongsTo('Category');
	}
    
	// Article __belongs_to_a__ History
	public function history()
	{
		return $this->hasMany('History');
	}

	// Article model belong to more than one other model (either ArticleSingle
	// or ArticleAmount
    public function proprieties()
    {
        return $this->morphTo();
	}

	// Select articles who belongs to category $name
	public function scopewhereCategory($query, $category_id)
	{
		return $query->whereHas('Category', function($query) use($category_id)
		{
			$query->where('id', $category_id);
		});
	}

	// Select articles with status $status_name
	public function scopewhereStatus($query, $status_name)
	{
		switch($status_name)
		{
			case 'all':
				return $query;
			case 'available':
				return $query->where('borrowed','=',0);
			case 'borrowed':
				return $query->where('borrowed','=',1);
		}
	}
}

