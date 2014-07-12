<?php

class History extends Eloquent
{
	// Database table used by the model	
	protected $table = 'lm_history';

	// Enable timestamps	
	public $timestamps = true;

	// Status-names allowed for the articles
	protected static $article_status_names = array('all','available','borrowed');

	// Status-names allowed for the histories
	protected static $history_status_names = array('all','returned','borrowed');

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

	// Select Histories who belongs to user $user_od
	public function scopewhereUser($query,$user_id)
	{
		return $query->whereHas('User', function($query) use($user_id)
		{
			$query->where('id', $user_id);
		});
	}
	
	// Select currently still borrowed Histories
	public function scopewhereBorrowed($query)
	{
		return $query->whereNull('returned_at');
	}

	// Select returned Histories
	public function scopewhereReturned($query)
	{
		return $query->whereNotNull('returned_at');
	}

	// Select Histories with status $status_name
	public function scopewhereStatus($query, $status_name)
	{
		switch($status_name)
		{
			case 'all':
				return $query;
				break;
			case 'returned':
				return $query->whereReturned();
				break;
			case 'borrowed':
				return $query->whereBorrowed();
				break;
		}
	}

	public static function getArticleStatusNames()
	{
		return self::$article_status_names;
	}
	
	public static function getHistoryStatusNames()
	{
		return self::$history_status_names;
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
		// FIXME automatically get a carbon object also for returned_date
		// $date = $this->$field_name
		
		$date = \Carbon\Carbon::createFromTimeStamp(
			strtotime($this->$field_name) );
	
		// If more than a month has passed, use the formatted date string
		if ($date->diffInDays() > 30)
		{
			return $date->format('d.m.Y');
		}
		// Else get the difference for humans
		else
		{
			return $date->diffForHumans();
		}
	}

	public function getBorrowedDate()
	{
		return $this->getFormattedDate('created_at');
	}
	
	public function getReturnedDate()
	{
		return $this->getFormattedDate('returned_at');
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

