<?php

class HistoryController extends BaseController
{	
	public function handle_borrow($status_name, $category_name, $field_name)
	{
		$article_ids = Input::all();
		$user = User::find( Auth::user()->id );			

		foreach($article_ids as $article_id)
		{
			$article = Article::find($article_id);

			// check if article is not already borrowed			
			if( $article->borrowed == false )
			{
				$article->borrowed = true;
				$article->save();

				$history = new History;
				$history->user()->associate($user);		
				$history->article()->associate($article);	
				$history->save();
			}
			else
			{
				return Redirect::action('ArticlesController@lists',
					array('status_name'=>$status_name,
						'category_name'=>$category_name,
						'field_name'=>$field_name) )
					->with('flash_error', 'Articles $article_id already borrowed');
			}
		}
		return Redirect::action('ArticlesController@lists',
			array('status_name'=>$status_name,
				'category_name'=>$category_name,
				'field_name'=>$field_name) )
			->with('flash_notice', 'Articles successfully borrowed.');
	}

	public function handle_return($user_id)
	{
		$history_ids = Input::all();
		
		foreach($history_ids as $history_id)
		{
			$history = History::find($history_id);
			$article = Article::find($history->article->id);
			
			$article->borrowed = false;
			$article->save();

			$history->setReturnedDate();
			$history->save();
		}
		return Redirect::action('UsersController@view',
			array('user_id'=>$user_id) )
			->with('flash_notice', 'Articles successfully borrowed.');
	}
}
