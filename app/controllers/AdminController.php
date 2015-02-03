<?php

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
		return Carbon::today()->toDateString()." ".
			Config::get('localmat.title')."-".
			Config::get('localmat.version')." ".
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
		$title = self::append_environnement('Export all articles');
		
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
}
