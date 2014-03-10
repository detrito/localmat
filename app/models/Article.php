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
    
	// Article __has_many__ Attributes
	public function attributes()
	{
		return $this->hasMany('Attribute');
	}

	// Select articles who belongs to category $name
	public function whereHasCategory($category)
	{
		return $this->whereHas('Category', function($query) use($category)
		{
			$query->where('name', $category);
		});
	}
}

