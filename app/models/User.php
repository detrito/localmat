<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	// Database table used by the model
	protected $table = 'lm_users';

	// Enable timestamps
	public $timestamps = true;

	// Enable softDelete
	use SoftDeletingTrait;
    protected $dates = ['deleted_at'];
    
    // edit options
    protected static $root_edit_options = array('edit_profile', 'edit_password',
    	'edit_permissions', 'delete_restore');
    protected static $user_edit_options = array('edit_profile', 'edit_password');
    
	// User __hasMany__ History
	public function histories()
	{
		return $this->hasMany('History');
	}

	// Attributes excluded from the model's JSON form. 
	protected $hidden = array('password');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */

	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

	/**
	 * Laravel 4.1.26 introduces security improvements for "remember me" cookies.
	 */
	public function getRememberToken()
	{
		return $this->remember_token;
	}

	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}

	public function getRememberTokenName()
	{
		return 'remember_token';
	}
	
	public function errorDisabled()
	{
		Auth::logout();
		return Redirect::to('/')
			->with('flash_error', 'This user has currently been disabled.');
	}
	
	
	// return an array with the user data
	public function exportUserData()
	{
		// array with the stored values
		$a_user = array();

		// column to export
		$columns = array('id', 'given_name','family_name','email');

		foreach ($columns as $column)
		{			
			// array with data to be returned
			$a_user[0][$column] = $this[$column];
		}

		return $a_user;
	}
	
	// return an array with the user histories
	public function exportUserHistories()
	{
		// array with the stored values
		$a_histories = array();

		// descending sort the collection of histories
		$histories = $this->histories->sortByDesc('created_at');
		
		// get main field name
		$main_field_name = Field::getMainFieldName();
		
		foreach ($histories as $key => $history)
		{
			$a_histories[$key]['history_id'] = $history->id;
			$a_histories[$key]['article_id'] = $history->article->id;			
			$a_histories[$key]['category'] = $history->article->category->name;
			if( ! is_null($main_field_name) )
			{
				$a_histories[$key][$main_field_name] =
				$history->article->getMainField();
			}
			$a_histories[$key]['amount_items'] = $history->amount_items;
			$a_histories[$key]['borrowed_date'] = $history->getBorrowedDate();
			$a_histories[$key]['time_span'] = $history->getTimeSpan();
		}

		return $a_histories;
	}
	
	public function getEditOptions()
	{
		if(Auth::user()->admin)
			return self::$root_edit_options;
		else
			return self::$user_edit_options;		
	}
}
