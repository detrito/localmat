<?php

class Field extends BaseEloquent
{
	// Database table used by the model	
	protected $table = 'lm_fields';

	// Enable timestamps	
	public $timestamps = false;
	
	// Data-types allowed for the field $value
	protected static $types = array('string', 'integer','integerpositive', 'boolean');	
	
	// Rules for the data-types
	protected static $rules = array(
		'string' => "alpha_num_dash_spaces|max:64",
		'integer' => "integer",
		'integerpositive' => "integer|between:0,100000",
		'boolean' => "integer|between:0,1"
		);
	
	// Data-types accepted by the MySQL CAST function
	protected static $mysql_cast_types = array(
		'string' => 'CHAR',
		'integer' => 'UNSIGNED',
		'integerpositive' => 'SIGNED',
		'boolean' => 'SIGNED'
		);
	
	// Field __belongs_to_many__ Categories
	public function categories()
	{
		return $this->belongsToMany('Category','lm_categories_fields');
	}
	
	// Field __belongs_to_many__ FieldDatum
	public function fieldData()
	{
		return $this->hasMany('FieldDatum');
	}
	
	public static function getMainFieldName()
	{
		return self::where('main','=',1)->pluck('name');
	}
	
	public static function getMainFieldId()
	{
		return self::where('main','=',1)->pluck('id');
	}
	
	public static function getTypes()
	{
		return self::$types;
	}

	public static function getCastType($type_name)
	{
		return self::$mysql_cast_types[$type_name];
	}

	public static function getRule($type_name, $required)
	{
		$rule = self::$rules[$type_name];
		// for boolean field: unchecked checkboboxes are automatically set to false
		if($required && $type_name !== 'boolean')
			$rule .= "|required";
		return $rule;
	}

	// Fetch the rule of each field an store them in an array
	public static function getRulesArray($fields)
	{
		$rules = array();
		foreach ($fields as $field)
		{
			$rules[$field->name] = self::getRule($field->type, $field->required);
		}
		return $rules;
	}

	public function scopewhereCategory($query, $category_name)
	{
		return $query->whereHas('categories', function($query) use($category_name)
		{
			$query->where('name', $category_name);
		});
	}

	public static function getMainFieldValue($article_single_id)
	{	
		return FieldDatum::whereArticleSingle($article_single_id)
			->whereFieldId(self::getMainFieldId())
			->pluck('value');
	}	
}
