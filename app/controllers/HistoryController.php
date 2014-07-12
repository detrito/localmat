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
		
		$history = $history->paginate(10);
	
		return View::make('history_lists', compact('history','status_names'))
			->with( array('status_name'=>$status_name));
	}

	public function handle_borrow()
	{
		$user = User::find( Auth::user()->id );
		$article_ids = Input::except('amount_items');
		
		foreach($article_ids as $article_id)
		{
			$article = Article::find($article_id);
			
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
					}
					else
					{
						return Redirect::back()
							->with('flash_error', 'Articles $article_id already borrowed');
					}
					break;
					
				case 'ArticleAmount':
					$amount_items = Input::get('amount_items');
					
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
					}
					else
					{
						return Redirect::back()
							->with('flash_error', 'The requested amount of items is not available');
					}
					break;
			}
		}

		return Redirect::back()
			->with('flash_notice', 'Articles successfully borrowed.');
	}

	public function handle_return($user_id)
	{
		$history_ids = Input::all();
		//var_dump($history_ids);
		//;
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
		return Redirect::action('UsersController@view',
			array('user_id'=>$user_id) )
			->with('flash_notice', 'Articles successfully returned.');
	}
}
