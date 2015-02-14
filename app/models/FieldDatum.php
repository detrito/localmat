<?php

class FieldDatum extends Eloquent
{
	// Database table used by the model	
	protected $table = 'lm_field_data';

	// Enable timestamps	
	public $timestamps = false;
	
	// Attribute __belongs_to_an__ ArticleSingle
	public function articleSingle()
	{
		return $this->belongsTo('ArticleSingle');
	}

	// Attribute __belongs_to_a__ Field
	public function field()
	{
		return $this->belongsTo('Field');
	}
	
	public function scopewhereArticleSingle($query, $article_single_id)
	{
		return $query->where('article_single_id','=',$article_single_id);
	}
}
