<?php

class Field extends Eloquent
{
	// Database table used by the model	
	protected $table = 'lm_fields';

	// Enable timestamps	
	public $timestamps = false;

	// Field __belongs_to_many__ Categories
	public function categories()
	{
		return $this->belongsToMany('Category','lm_categories_fields');
	}

	// Field __belongs_to_many__ Attributes
	public function attributes()
	{
		return $this->belongsToMany('Attribute');
	}
}
