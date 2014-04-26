<?php

// app/controllers/UsersController.php

class UsersController extends BaseController
{
    public function index()
    {
        // Show a listing of games.
        $users = User::all();

        return View::make('user_index', compact('users'));
    }

	public function view($user_id)
	{
		$user = User::find($user_id);

		$history_borrowed = History::whereUser($user_id)
			->whereBorrowed()
			->get();

		$history_all = History::whereUser($user_id)
			->get();

		return View::make('user_view', compact('user','history_borrowed','history_all'));
	}

    public function add()
    {
        return View::make('user_form')
			->with('action', 'add');; 
	}

	public function handle_add()
	{
		$data = Input::all();
		return $this->insert_data($data,'add');
	}

	private function insert_data($data, $action, $user_id=Null)
	{
		$rules = array(
			'given_name' => 'required|alpha',
			'family_name' => 'required|alpha',
			'email' => 'required|email|unique:lm_users'
		);

		// force unique rule on email to ignore $user_id
		if( $action == 'edit')
		{
			$rules['email'] .= ',email,'.$user_id;
		}

		// require password when
		// 1. editing a user and a password has been entered
		// 2. adding a new user		
		if( $action == 'edit' &&  !empty($data['password']) || $action == 'add')
		{
			// add password-rule to $rules
			$rules['password'] = 'required|alpha_dash|confirmed|min:6';
		}

		$validator = Validator::make($data, $rules);
		
		if ( $validator->passes() )
		{
			if ($action == 'add')
			{
				$user = new User;
			}
			elseif ($action == 'edit')
			{
				$user = User::find($user_id);
			}

			$user->email = $data['email'];
			$user->given_name = $data['given_name'];
			$user->family_name = $data['family_name'];
			if( $action == 'edit' &&  !empty($data['password']) || $action == 'add')
			{
				$user->password = Hash::make($data['password']);
			}			
			// set to false if the input value has not be checked in the form
			$user->enabled = isset($data['enabled']) ? true : false;
			$user->admin = isset($data['admin']) ? true : false;		
			$user->save();
			
			if($action == 'add')
			{
				return Redirect::action('UsersController@add')
					->with('flash_notice', 'User successfully added.');
			}
			elseif($action == 'edit')
			{
				return Redirect::action('UsersController@index')
					->with('flash_notice', 'User successfully modified.');
			}
		}
		return Redirect::back()
			->withErrors($validator);
	}

	public function edit($user_id)
    {
		$user = User::find($user_id);

		return View::make('user_form',compact('user'))
			->with('action', 'edit');
    }
	
	public function handle_edit($user_id)
	{
		$data = Input::all();
		return $this->insert_data($data, 'edit', $user_id);
	}

	public function delete($user_id)
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
				->with('flash_notice', 'User successfully deleted.');
		}
		return Redirect::action('UsersController@index')
				->with('flash_error', 'This user still has borrowed articles!
					Make sure that he returned all his articles before to delete it.');
    }
}

