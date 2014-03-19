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

	// Return array of fields-names of an article
	public function getFieldNames()
	{
		$field_names = array();
		foreach ($this->attributes()->get() as $attribute)
		{
			array_push($field_names,$attribute->field->name);
		}
		return $field_names;
	}
	
	// Return array of fields-ids of an article
	public function getFieldIds()
	{
		$field_ids = array();
		foreach ($this->attributes()->get() as $attribute)
		{
			array_push($field_ids,$attribute->field->id);
		}
		return $field_ids;
	}
}

