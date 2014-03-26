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
		$user_model = new User;
		$user = $user_model->find($user_id);

		$history_model = new History;
		$history_borrowed = $history_model
			->whereHasUser($user_id)
			->whereBorrowed(1)
			->get();
		$history_all = $history_model
			->whereHasUser($user_id)
			->get();

		return View::make('user_view', compact('user','history_borrowed','history_all'));
	}

    public function add()
    {

        return View::make('user_add'); 
	}

	public function handle_add()
	{
		$data = Input::all();
		$rules = array(
			'email' => 'required|email|unique:lm_users',
			'given_name' => 'required|alpha',
			'family_name' => 'required|alpha',
			'password' => 'required|alpha_dash|confirmed|min:6',	
		);
		$validator = Validator::make($data, $rules);
		
		if ($validator->passes())
		{
			$user = new User;
			$user->email = Input::get('email');
			$user->given_name = Input::get('given_name');
			$user->family_name = Input::get('family_name');
			$user->password = Hash::make(Input::get('password'));
			// set to 0 if the input value is absent
			$user->enabled = Input::get('enabled',0);
			$user->admin = Input::get('admin',0);		
			$user->save();
		
			return Redirect::action('UsersController@add')
				->with('flash_notice', 'User successfully added.');
		}
		
		return Redirect::back()
		->withErrors($validator);
	}

	public function edit(User $user)
    {
        // Show the create game form.
        return View::make('user_create');
    }

	public function delete(User $user)
    {
        // Show the create game form.
        return View::make('user_create');
    }
}

