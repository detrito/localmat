<?php

use Symfony\Component\Console\Output\StreamOutput;

class AdminController extends BaseController
{
	public function index()
	{
		return View::make('admin_index');
	}

	public function download($file)
	{
		$downloaded_filename = self::append_environnement(basename($file));

		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.$downloaded_filename.'"'); 
		header('Content-Length: '.filesize($file));
		readfile($file);
	}

	public function append_environnement($action)
	{
		return Carbon::now()->toW3cString()."_".
			Config::get('localmat.title')."-".
			Config::get('localmat.version')."_".
			$action;
	}
	public function export_logs()
	{
		// FIXME avoid hardcoded file name
		$filename = storage_path()."/logs/laravel.log";		
		return $this->download($filename);
	}
	
	public function export_articles()
	{		
		// document title
		$title = self::append_environnement('Articles');
		
		// get all categories
		$categories = Category::orderBy('name')->get();
		
		// export to an excel file
		Excel::create($title, function($excel) use($categories, $title)
		{
			// set file proprieties
			$excel->setTitle($title);
			$excel->setCreator( Config::get('localmat.title') );
			
			foreach($categories as $category)
			{
				// create one sheet for each category
				$excel->sheet($category->name, function($sheet) use($category)
				{
						$sheet->fromArray($category->exportArticles());
				});
			}
		})->export('xls');
	}
	
	public function export_histories()
	{
		// document title
		$title = self::append_environnement('Histories');
		
		// get all categories
		//$histories = Category::orderBy('name')->get();
		
		// export to an excel file
		Excel::create($title, function($excel) use($title)
		{
			// set file proprieties
			$excel->setTitle($title);
			$excel->setCreator( Config::get('localmat.title') );
			
			// create one single sheet
			$excel->sheet('Histories', function($sheet) use($title)
			{
				$sheet->fromArray( History::exportHistories() );
			});
		})->export('xls');
	
	}
	
	public function export_users()
	{
		// document title
		$title = self::append_environnement('Users');
		
		// get all categories
		$users = User::
				with('histories','histories.article','histories.article.category')
				->orderBy('given_name', 'asc')
				->orderBy('family_name', 'asc')
				->get();
			
		// export to an excel file
		Excel::create($title, function($excel) use($users, $title)
		{
			// set file proprieties
			$excel->setTitle($title);
			$excel->setCreator( Config::get('localmat.title') );
			
			foreach($users as $user)
			{
				$user_fullname = $user->given_name." ".$user->family_name;
				
				// create one sheet for each category
				$excel->sheet($user_fullname, function($sheet) use($user)
				{
						$sheet->fromArray($user->exportUserData());
						// FIXME search some cleaner way to add a blank row
						$emptyline = array( 0 => array(0 => "") );
						$sheet->fromArray($emptyline);
						$sheet->fromArray($user->exportUserHistories());
				});
			}
		})->export('xls');	
	}
	
	public function export_db()
	{
		$filename = storage_path()."/dumps/".self::append_environnement('DB.sql');
		
		// call schickling/laravel-backup by executing "php artisan db:backup"
		Artisan::call('db:backup',['filename'=>$filename]);
		
		// now download the on the server stored backup
		return $this->download($filename);
	}
}

