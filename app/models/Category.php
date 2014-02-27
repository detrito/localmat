<?php

class Category extends Eloquent
{
	// Database table used by the model	
	protected $table = 'lm_categories';

	// Enable timestamps	
	public $timestamps = false;

	// Category __belongsToMany__ Fields
	public function fields()
	{
		return $this->belongsToMany('Field','lm_categories_fields');
	}
	
	// Category __hasMany__ Articles
	public function articles()
	{
		return $this->hasMany('Article');
	}
}
