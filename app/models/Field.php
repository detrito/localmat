<?php

class Field extends Eloquent
{
	// Database table used by the model	
	protected $table = 'lm_fields';

	// Enable timestamps	
	public $timestamps = false;

	// Data-types allowed for the field $value
	protected static $types = array('string', 'integer','integerpositive', 'boolean');	
	
	// default rules for the data-types
	protected static $default_rules = array(
		'string' => "required|alpha_spaces|max:64",
		'integer' => "required|integer",
		'integerpositive' => "required|integer|between:0,100000",
		'boolean' => "integer|between:0,1"
		);
	
	// data-types accepted by the MySQL CAST function
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

	// Field __belongs_to_many__ Attributes
	public function attributes()
	{
		return $this->belongsToMany('Attribute');
	}
	
	public static function getTypes()
	{
		return self::$types;
	}

	public static function getCastType($type_name)
	{
		return self::$mysql_cast_types[$type_name];
	}

	public static function getDefaultRule($type_name)
	{
		return self::$default_rules[$type_name];
	}
}
