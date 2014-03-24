<?php

class History extends Eloquent
{
	// Database table used by the model	
	protected $table = 'lm_history';

	// Enable timestamps	
	public $timestamps = true;

	// History __has_one__ Article
	public function article()
	{
		return $this->hasOne('Article');
	}

	// History __belongs_to_one__ User
	public function user()
	{
		return $this->belongsToOne('User');
	}
}
