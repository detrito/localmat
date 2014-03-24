<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	// Database table used by the model
	protected $table = 'lm_users';
	
	// Enable timestamps
	public $timestamps = true;

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
	
	public function errorDisabled()
	{
		Auth::logout();
		return Redirect::to('/')
			->with('flash_error', 'This user has currently been disabled.');
	}
}
