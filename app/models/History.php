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
		return $this->belongsTo('Article');
	}

	// History __belongs_to_one__ User
	public function user()
	{
		return $this->belongsTo('User');
	}

	// Select articles who belongs to category $name
	public function scopewhereUser($query,$user_id)
	{
		return $query->whereHas('User', function($query) use($user_id)
		{
			$query->where('id', $user_id);
		});
	}
	
	// Select articles who belongs to category $name
	public function scopewhereBorrowed($query)
	{
		return $query->whereNull('returned_at');
	}

	public function isBorrowed()
	{
		if( $this->returned_at == NULL )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function setReturnedDate()
	{
		$dt = new DateTime;
		$this->returned_at = $dt->format('y-m-d H:i:s');
		return 1;
	}

	public function carbonReturnedDate()
	{
		return \Carbon\Carbon::createFromTimeStamp(
			strtotime($this->returned_at) );
	}

	public function getFormattedDate($field_name)
	{
		// If more than a month has passed, use the formatted date string
		if ($this->$field_name->diffInDays() > 30)
		{
			return $this->$field_name->format('d.m.Y');
		}
		// Else get the difference for humans
		else
		{
			return $this->$field_name->diffForHumans();
		}
	}

	public function getBorrowedDate()
	{
		return $this->getFormattedDate('created_at');
	}

	public function getTimeSpan()
	{
		if ($this->isBorrowed())
		{
			return "Still borrowed";
		}
		else
		{
			$borrowed_date = $this->created_at;
			$returned_date = $this->carbonReturnedDate();
			return $borrowed_date->diffInDays($returned_date).' days';
		}

	}
}

