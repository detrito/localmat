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
	
	// Select Histories who belongs to article $article_id
	public function scopewhereArticle($query,$article_id)
	{
		return $query->whereHas('Article', function($query) use($article_id)
		{
			$query->where('id', $article_id);
		});
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
	
	public static function borrowArticle($article, $amount_items = NULL)
	{
		$user = User::find( Auth::user()->id );

		switch ($article->proprieties_type)
		{
			case 'ArticleSingle':
				$article_single = $article->proprieties;		

				// Check if article is not already borrowed			
				if( $article_single->borrowed == false )
				{
					$article_single->borrowed = true;
					$article_single->save();

					$history = new History;
					$history->user()->associate($user);		
					$history->article()->associate($article);	
					$history->save();
					
					return $history;
				}
				
			case 'ArticleAmount':
				$article_single = $article->proprieties;

				if ($article_single->available_items >= $amount_items)
				{
					$article_single->available_items -= $amount_items;
					$article_single->save();
				
					$history = new History;
					$history->user()->associate($user);		
					$history->article()->associate($article);
					$history->amount_items = $amount_items;
					$history->save();
					
					return $history;
				}
				break;
			// return 1 if article no available
			// or not enough items availables
			return NULL;
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
		return Carbon::createFromTimeStamp(
			strtotime($this->returned_at) );
	}

	public function getFormattedDate($field_name)
	{
		// FIXME automatically get a carbon object also for returned_date
		// $date = $this->$field_name
		
		$date = Carbon::createFromTimeStamp(
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
	
	public static function exportHistories()
	{
		// array with all histories
		$a_histories = array();
	
		$histories = History::
			with('user','article.category')
			->orderBy('created_at','desc')
			->get();
		
		// get main field name
		$main_field_name = Field::getMainFieldName();
		
		foreach ($histories as $key => $history)
		{			
			// array with data to be returned
			$a_histories[$key] = array();
			
			// append history values
			$a_histories[$key]['Id'] = $history->id;
			$a_histories[$key]['Article_id'] = $history->article_id;
			$a_histories[$key]['Category'] = $history->article->category->name;
			
			if( ! is_null($main_field_name) )
			{
				$a_histories[$key][$main_field_name] =
				$history->article->getMainField();
			}
			
			$a_histories[$key]['Amount items'] = $history->amount_items;
			
			if( isset($history->user))
			{
					$user = $history->user;
			}
			else
			{
					$user = User::withTrashed()->find($history->user_id);
			}
			$a_histories[$key]['User'] = $user->given_name." ".$user->family_name;
						
			$a_histories[$key]['Borrowed date'] = $history->getBorrowedDate();
			$a_histories[$key]['Time span'] = $history->getTimeSpan();
		}
		
		return $a_histories;
	}
}

