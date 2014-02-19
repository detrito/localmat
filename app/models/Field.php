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
		return $this->belongsToMany('Category');
	}

	// Field __belongs_to_many__ Attributes
	public function attributes()
	{
		return $this->hasMany('Attribute');
	}

}
