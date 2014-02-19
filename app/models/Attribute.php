<?php

class Attribute extends Eloquent
{
	// Database table used by the model	
	protected $table = 'lm_attributes';

	// Enable timestamps	
	public $timestamps = false;
	
	// Attribute __belongs_to_an__ Article
	public function article()
	{
		return $this->belongsTo('Article');
	}

	// Attribute __belongs_to_a__ Field
	public function field()
	{
		return $this->belongsTo('Field');
	}
}
