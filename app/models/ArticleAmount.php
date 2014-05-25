<?php

class ArticleAmount extends BaseEloquent
{
	// Database table used by the model	
	protected $table = 'lm_articles_amounts';

    public $timestamps = false;

    public function article()
    {
        return $this->morphMany('Article', 'proprieties');
    }
}

