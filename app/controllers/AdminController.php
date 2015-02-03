
<?php

class AdminController extends BaseController
{
	public function index()
	{
		return View::make('admin_index');
	}
	
	public function logs()
	{
		// FIXME avoid hardcoded file name
		$filename = storage_path()."/logs/laravel.log";		
		return $this->download($filename);
	}
	
	public function download($file)
	{
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.basename($file).'"'); 
		header('Content-Length: '.filesize($file));
		readfile($file);
	}
}
