<?php

class Article extends Eloquent
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
}
