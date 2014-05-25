<?php

class ArticleSingle extends BaseEloquent
{
	// Database table used by the model	
	protected $table = 'lm_articles_singles';

    public $timestamps = false;

    public function article()
    {
        return $this->morphMany('Article', 'proprieties');
    }

	// Article __has_many__ Attributes
	public function attributes()
	{
		return $this->hasMany('Attribute');
	}

	public static function getArticleIdsSortedByField($articles, $field_id, $order='ASC')
	{
		$article_ids = $articles->fetch('id')->toArray();	
	
		// retrive mysql cast type of the field $field_name
		// in order to sort it as CHAR or as INTEGER
		$field_type = Field::find($field_id)->type;
		$field_cast_type = Field::getCastType($field_type);

		$attribute = new Attribute;	
		return $attribute
			->whereHas('Field', function($query) use($field_id)
			{
				$query->where('id', $field_id);
			})
			->select('article_id', 'value')
			// FIXME this only works on MySQL
			->orderBy(DB::raw('CAST(value AS '.$field_cast_type.')'), $order)
			->lists('article_id');
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
