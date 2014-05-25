<?php

class Attribute extends Eloquent
{
	// Database table used by the model	
	protected $table = 'lm_attributes';

	// Enable timestamps	
	public $timestamps = false;
	
	// Attribute __belongs_to_an__ ArticleSingle
	public function article_single()
	{
		return $this->belongsTo('ArticleSingle');
	}

	// Attribute __belongs_to_a__ Field
	public function field()
	{
		return $this->belongsTo('Field');
	}
}
