
<?php

class AdminController extends BaseController
{
	public function index()
	{
		return View::make('admin_index');
	}
	
	public function logs()
	{
		// FIXME automatically load filename
		//$filename = Log::getMonolog();
		//$filename = Log::getEventDispatcher();
		$filename = storage_path()."/logs/laravel.log";

		$handle = fopen($filename, "r");
		$logs = fread($handle, filesize($filename));
		fclose($handle);
		
		return View::make('admin_logs', compact('logs') );
	}
}
