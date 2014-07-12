<?php

// app/controllers/UsersController.php

class UsersController extends BaseController
{
    public function index()
    {
		if (Auth::check() && Auth::user()->admin)
		{
	        $users = User::
	        	withTrashed()
				->orderBy('given_name', 'asc')
				->orderBy('family_name', 'asc')
				->get();
		}
		else
		{
	        $users = User::
				orderBy('given_name', 'asc')
				->orderBy('family_name', 'asc')
				->get();		
		}
			
        return View::make('user_index', compact('users'));
    }
    
    public function login($user_id)
	{
		$user = User::findOrFail($user_id);
		Auth::login($user);
		
		return Redirect::action('UsersController@index')
				->with('flash_notice', 'You are now logged-in as '.$user->email);
	}

	public function view($user_id)
	{
		$user = User::
			withTrashed()
			->findOrFail($user_id);
			
		$history_borrowed = $user->histories()
			->whereBorrowed()
			->orderBy('created_at','desc')
			->get();

		$history_all = $user->histories()
			->orderBy('created_at','desc')
			->get();

		return View::make('user_view', compact('user','history_borrowed','history_all'));
	}

    public function add()
    {
        return View::make('user_form_add');
	}

	public function handle_add()
	{
		$data = Input::all();
		$rules = array(
			'given_name' => 'required|alpha',
			'family_name' => 'required|alpha',
			'email' => 'required|email|unique:lm_users',
			'password' => 'required|alpha_dash|confirmed|min:6'
		);

		$validator = Validator::make($data, $rules);
		
		if ( $validator->passes() )
		{
			$user = new User;
			$user->email = $data['email'];
			$user->given_name = $data['given_name'];
			$user->family_name = $data['family_name'];
			$user->password = Hash::make($data['password']);
						
			// set to false if the input value has not be checked in the form
			$user->enabled = isset($data['enabled']) ? true : false;
			$user->admin = isset($data['admin']) ? true : false;		
			$user->save();
			
			return Redirect::action('UsersController@add')
				->with('flash_notice', 'User successfully added.');
		}
		return Redirect::back()
			->withErrors($validator);
	}
	
	public function edit_permissions($user_id)
	{
		$user = User::withTrashed()->find($user_id);
		return View::make('user_form_permissions', compact('user'));	
	}
	
	public function handle_edit_permissions($user_id)
	{
		$user = User::withTrashed()->find($user_id);
		$data = Input::all();

		// set to false if the input value has not be checked in the form
		$user->enabled = isset($data['enabled']) ? true : false;
		$user->admin = isset($data['admin']) ? true : false;		
		$user->save();
			
		return Redirect::action('UsersController@view',
			array('user_id'=>$user->id) )
			->with('flash_notice', 'User permissions successfully modified.');
	}
	
	public function edit_password($user_id)
	{
		$user = User::withTrashed()->find($user_id);
		return View::make('user_form_password',compact('user'));
	}
	
	public function handle_edit_password($user_id)
	{
		$user = User::withTrashed()->find($user_id);
		$data = Input::all();
		$rules = array( 'password' => 'required|alpha_dash|confirmed|min:6' );
		
		$validator = Validator::make($data, $rules);
		
		if ( $validator->passes() )
		{
			$user = User::withTrashed()->find($user_id);
			$user->password = Hash::make($data['password']);
			$user->save();
			
			return Redirect::action('UsersController@view',
				array('user_id'=>$user->id) )
				->with('flash_notice', 'User password successfully modified.');
		}
		return Redirect::back()
			->withErrors($validator);
	}
	
	public function edit_profile($user_id)
	{
		$user = User::withTrashed()->find($user_id);
		return View::make('user_form_profile',compact('user'));
	}
	
	public function handle_edit_profile($user_id)
	{
		$user = User::withTrashed()->find($user_id);
		$data = Input::all();
	
		$rules = array(
			'given_name' => 'required|alpha',
			'family_name' => 'required|alpha',
			'email' => 'required|email|unique:lm_users,email,'.$user_id
		);
		
		$validator = Validator::make($data, $rules);
		
		if ( $validator->passes() )
		{
			$user = User::withTrashed()->find($user_id);
			$user->email = $data['email'];
			$user->given_name = $data['given_name'];
			$user->family_name = $data['family_name'];
			$user->save();
			
			return Redirect::action('UsersController@view',
				array('user_id'=>$user->id) )
				->with('flash_notice', 'User profile successfully modified.');
		}
		return Redirect::back()
			->withErrors($validator);
	}

	public function trash($user_id)
    {
        $user = User::find($user_id);
		$history_borrowed = History::whereUser($user_id)
			->whereBorrowed()
			->get();

		if ( $history_borrowed->isEmpty() )
		{
			// softDelete this user
			$user->delete();
			return Redirect::action('UsersController@index')
				->with('flash_notice', 'User successfully trashed.');
		}
		return Redirect::action('UsersController@index')
				->with('flash_error', 'This user still has borrowed articles!
					Make sure that he returned all his articles before to trash it.');
    }
    
    public function restore($user_id)
    {
    	$user = User::withTrashed()->find($user_id);
    	if(isset($user->deleted_at))
    	{
    		$user->restore();
    	
			return Redirect::action('UsersController@index')
					->with('flash_notice', 'User sucessfully restored.');
		}
    }
}

