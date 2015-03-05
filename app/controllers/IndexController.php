
<?php

class IndexController extends BaseController
{
	public function index()
	{
	
		// Get MainFieldName
		$main_field_name = Field::getMainFieldName();
	
		$history_borrowed = History::
			with('user','article')
			->orderBy('created_at','desc')
			->take(5)
			->get();
		
		$history_returned = History::
			whereReturned()
			->with('user','article')
			->orderBy('returned_at','desc')
			->take(5)
			->get();
		
		return View::make('index', compact('main_field_name',
			'history_borrowed','history_returned'));
	}
}
