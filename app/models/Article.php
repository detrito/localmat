<?php

class Article extends BaseEloquent
{
	// Database table used by the model	
	protected $table = 'lm_articles';

	// Enable timestamps	
	public $timestamps = true;

	// Classes of Articles
	protected static $article_classes = array(
		'ArticleSingle',
		'ArticleAmount'
	);
	
	// Article __belongs_to_a__ Category
	public function category()
	{
		return $this->belongsTo('Category');
	}
    
	// Article __belongs_to_a__ History
	public function history()
	{
		return $this->hasMany('History');
	}

	// Article model belong to more than one other model (either ArticleSingle
	// or ArticleAmount
    public function proprieties()
    {
        return $this->morphTo();
	}
	
	// Get types of article classes
	public static function getArticleClasses()
	{
		return self::$article_classes;
	}
}

