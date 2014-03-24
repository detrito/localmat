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

	// Article __belongs_to_a__ History
	public function history()
	{
		return $this->belongsTo('History');
	}

	// Select articles who belongs to category $name
	public function whereHasCategory($category)
	{
		return $this->whereHas('Category', function($query) use($category)
		{
			$query->where('name', $category);
		});
	}

	// Select articles who belongs to status $name
	public function scopeStatus($query, $status)
	{
		switch($status)
		{
			case 'all':
				return $query;
			case 'available':
				return $query->has('history', '==' , 'Null');
			case 'borrowed':
				return $query->has('history');
		}
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

