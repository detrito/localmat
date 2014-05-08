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

	// Return array of category-names
	public function getNames()
	{
		$category_names = array('all');
		foreach ($this->get() as $category)
		{
			array_push($category_names,$category->name);
		}
		return $category_names;
	}

	// Return array of fields-ids
	public function getFieldIds()
	{
		$field_ids = array();
		foreach ($this->fields()->get() as $field)
		{
			array_push($field_ids,$field->id);
		}
		return $field_ids;
	}

	// Return array of fields-names of an article
	public function getFieldNames()
	{
		$field_names = array();
		foreach ($this->fields()->get() as $field)
		{
			array_push($field_names,$field->name);
		}
		return $field_names;
	}
	
}
