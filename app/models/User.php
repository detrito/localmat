<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	// Database table used by the model
	protected $table = 'lm_users';

	// Enable timestamps
	public $timestamps = true;

	// Enable softDelete
	protected $softDelete = true;

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
}
