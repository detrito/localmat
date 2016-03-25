<?php

class HistoryController extends BaseController
{
	public function index()
	{
		return $this->lists();
  	}
	
	public function lists($status_name='all')
	{
		// Get list of status names
		$status_names = History::getHistoryStatusNames();
		
		// Get MainFieldName
		$main_field_name = Field::getMainFieldName();
	
		// FIXME load automatically also softDeleted users
		$history = History::
			whereStatus($status_name)
			->with('user','article');
		
		if($status_name == 'returned')
		{
			$history = $history->orderBy('returned_at','desc');
		}
		else
		{
			$history = $history->orderBy('created_at','desc');
		}
		
		$history = $history->paginate(Config::get('localmat.itemsPerPage'));
	
		return View::make('history_lists', compact('history','status_names',
			'main_field_name'))
			->with( array('status_name'=>$status_name));
	}

	public function handle_borrow_post()
	{
		$article_ids = Input::except('amount_items');
		$history_ids = array();
		
		foreach($article_ids as $article_id)
		{
			$article = Article::find($article_id);

			switch ($article->proprieties_type)
			{
				case 'ArticleAmount':
					$amount_items = Input::get('amount_items');
					break;
					
				case 'ArticleSingle':
					$amount_items = NULL;
					break;
			}
			
			$history = History::borrowArticle($article, $amount_items);
						
			if ($history != NULL)
			{
				array_push($history_ids, $history->id);
			}
			else
			{
				switch ($article->proprieties_type)
				{
					case 'ArticleSingle':
						return Redirect::back()
							->with('flash_error', 'Articles $article_id already
								borrowed');
						break;
					case 'ArticleAmount':
						return Redirect::back()
							->with('flash_error', 'The requested amount of items
								is not available');
						break;
				}				
			}
		}
		
		$user = User::find( Auth::user()->id );
		$message = 'Articles successfully borrowed.';
		$message_verbose = $message.' User ID '.$user->id.
			'. History IDs '.implode(",", $history_ids).'.';
		Log::info($message_verbose);
		return Redirect::back()
			->with('flash_notice', $message);
	}
	
	public function handle_borrow_get($article_id)
	{
		$article = Article::find($article_id);
		
		if($article->proprieties_type == "ArticleSingle")
		{
			$history = History::borrowArticle($article);
			
			if ($history != NULL)
			{
				$user = User::find( Auth::user()->id );
				$message = 'Article successfully borrowed.';
				$message_verbose = $message.' User ID '.$user->id.
					'. History ID '.$history->id.'.';
				Log::info($message_verbose);
				return Redirect::back()
					->with('flash_notice', $message);
			}
			else
			{
				return Redirect::back()
					->with('flash_error', 'Articles $article_id already
						borrowed');
			}			
		}
	}
	
	public function handle_return($user_id)
	{
		$history_ids = Input::all();

		foreach($history_ids as $history_id)
		{
			$history = History::find($history_id);
			$article = Article::find($history->article->id);
			
			switch ($article->proprieties_type)
			{
				case 'ArticleSingle':
					$article_single = $article->proprieties;
					$article_single->borrowed = false;
					$article_single->save();

					$history->setReturnedDate();
					$history->save();
					break;
					
				case 'ArticleAmount':
					$article_amount = $article->proprieties;
					$article_amount->available_items += $history->amount_items;
					$article_amount->save();
					
					$history->setReturnedDate();
					$history->save();
					break;
			}
		}
		
		$message = 'Articles successfully returned.';
		$message_verbose = $message.' User ID '.$user_id.'. History IDs '.
			implode(",", $history_ids).'.';
		Log::info($message_verbose);
		return Redirect::action('UsersController@view',
			array('user_id'=>$user_id) )
			->with('flash_notice', $message);
	}
}

