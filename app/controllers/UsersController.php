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
    
    public function login()
    {
    	return View::make('login');
    }
    
    public function handle_login()
    {
    	$credentials = Input::only('email', 'password');
		$remember = true;

		if (Auth::attempt($credentials,$remember))
		{
			if(Auth::user()->enabled)
			{
				$user = Auth::user();
				$message = 'You are now logged-in as '.$user->email;
				$message_verbose = $message.' User ID '.$user->id.'.';
				Log::info($message_verbose);
				return Redirect::intended('/')
					->with('flash_notice', $message);
			}
			else
			{
				return Auth::user()->errorDisabled();
			}	
		}
		else	
			return Redirect::to('login')
			->with('flash_error', 'Your username/password combination is incorrect.');
    }
    
    public function login_as($user_id)
	{
		// remember main user
		$main_user = Auth::user();
		Session::put('main_user_id', $main_user->id);
		Session::put('main_user_email', $main_user->email);
		
		// switch user
		$user = User::findOrFail($user_id);
		Auth::login($user);
		
		$message = 'You switched user to '.$user->email;
		$message_verbose = $message.' User ID '.$user->id.'.';
		Log::info($message_verbose);
		
		return Redirect::action('UsersController@view',
			array('user_id'=>$user->id) )
			->with('flash_notice', $message);
	}
	
	public function login_back()
	{
		$user_id = Session::get('main_user_id');
		$user = User::findOrFail($user_id);
		
		Auth::login($user);
		
		Session::forget('main_user_id');
		Session::forget('main_user_email');
		
		$message = 'You switched back to user '.$user->email;
		$message_verbose = $message.' User ID '.$user->id.'.';
		Log::info($message_verbose);
		
		return Redirect::action('UsersController@index')
				->with('flash_notice', $message);
	}
	
	public function logout()
	{
		$user = Auth::user();
		Auth::logout();
		
		$message = 'You are successfully logged out.';
		$message_verbose = $message.' User ID '.$user->id.'.';
		Log::info($message_verbose);
		return Redirect::action('IndexController@index')
			->with('flash_notice', $message);
	}

	public function view($user_id)
	{
		// Get MainFieldName
		$main_field_name = Field::getMainFieldName();
	
		$user = User::
			withTrashed()
			->findOrFail($user_id);
			
		$history_borrowed = $user->histories()
			->whereBorrowed()
			->orderBy('created_at','desc')
			->get();

		return View::make('user_view', compact('user','history_borrowed',
			'main_field_name'));
	}
	
	public function history($user_id)
	{
		// Get MainFieldName
		$main_field_name = Field::getMainFieldName();
	
		$user = User::
			withTrashed()
			->findOrFail($user_id);
		
		$history = $user->histories()
			->orderBy('created_at','desc')
			->paginate(Config::get('localmat.itemsPerPage'));
		
		return View::make('user_history', compact('user','history',
			'main_field_name'));	
	}
	

    public function add()
    {
        return View::make('user_form_add');
	}

	public function handle_add()
	{
		$data = Input::all();
		$rules = array(
			'given_name' => 'required',
			'family_name' => 'required',
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
			
			$message = 'User successfully added.';
			$message_verbose = $message.' User ID '.$user->id.'.';
			Log::info($message_verbose);
			return Redirect::action('UsersController@add')
				->with('flash_notice', $message);
		}
		return Redirect::back()
			->withErrors($validator);
	}
	
	public function edit($user_id, $edit_option=Null)
	{
		$user = User::withTrashed()->find($user_id);
		$edit_options = $user->getEditOptions();
		//var_dump($edit_options);
		return View::make('user_edit',compact('user','edit_options', 'edit_option'));
	}
	
	public function handle_edit_profile($user_id)
	{
		$user = User::withTrashed()->find($user_id);
		$data = Input::all();
	
		$rules = array(
			'given_name' => 'required',
			'family_name' => 'required',
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
			
			$message = 'User\'s profile successfully modified.';
			$message_verbose = $message.' User ID '.$user->id.'.';
			Log::info($message_verbose);
			return Redirect::action('UsersController@view',
				array('user_id'=>$user->id) )
				->with('flash_notice', $message);
		}
		return Redirect::back()
			->withErrors($validator);
	}
	
	public function handle_edit_permissions($user_id)
	{
		$user = User::withTrashed()->find($user_id);
		$data = Input::all();

		// set to false if the input value has not be checked in the form
		$user->enabled = isset($data['enabled']) ? true : false;
		$user->admin = isset($data['admin']) ? true : false;		
		$user->save();
		
		$message = 'User\'s permissions successfully modified.';
		$message_verbose = $message.' User ID '.$user->id.'.';
		Log::info($message_verbose);
		return Redirect::action('UsersController@view',
			array('user_id'=>$user->id) )
			->with('flash_notice', $message);
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
			
			$message = 'User\'s password successfully modified.';
			$message_verbose = $message.' User ID '.$user->id.'.';
			Log::info($message_verbose);
			return Redirect::action('UsersController@view',
				array('user_id'=>$user->id) )
				->with('flash_notice', $message);
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
			
			$message = 'User successfully trashed.';
			$message_verbose = $message.' User ID '.$user->id.'.';
			Log::info($message_verbose);
			return Redirect::action('UsersController@index')
				->with('flash_notice', $message);
		}
		return Redirect::back()
				->with('flash_error', 'This user still has borrowed articles!
					Make sure that he returned all his articles before to trash it.');
    }
    
    public function restore($user_id)
    {
    	$user = User::withTrashed()->find($user_id);
    	if(isset($user->deleted_at))
    	{
    		$user->restore();
    	
    		$message = 'User successfully restored.';
			$message_verbose = $message.' User ID '.$user->id.'.';
			Log::info($message_verbose);
			return Redirect::action('UsersController@index')
					->with('flash_notice', $message);
		}
    }
}

