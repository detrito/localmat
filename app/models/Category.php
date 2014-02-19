<?php

class Category extends Eloquent
{
	// Database table used by the model	
	protected $table = 'lm_categories';

	// Enable timestamps	
	public $timestamps = false;

	// Category __has_many__ Fields
	public function fields()
	{
		return $this->hasMany('Field');
	}
	
	// Category __belongs_to_many__ Articles
	public function articles()
	{
		return $this->belongsToMany('Article');
	}


}
