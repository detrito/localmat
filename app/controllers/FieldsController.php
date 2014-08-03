<?php

class FieldsController extends BaseController
{	
    public function index()
    {
		$fields = Field::all();
        return View::make('field_index', compact('fields'));
    }

	public function add()
	{
		$types = Field::getTypes();
		
		// use $types as array-keys AND as array-values	
		$field_types = array_combine($types,$types);
		return View::make('field_form', compact('field_types') )
			->with('action', 'add');
	}

	public function handle_add()
	{
		$data = Input::all();
		$types = Field::getTypes();

		$rules = array(
			'name' => 'required|alpha_num_dash_spaces|unique:lm_fields',
			'type' => 'in:'.implode(',', $types)	
		);

		$validator = Validator::make($data, $rules);
		if ($validator->passes())
		{
			$field = new Field;
			$field->name = Input::get('name');
			$field->type = Input::get('type');
			if( Input::get('required') !== Null )
				$field->required = Input::get('required');
			else
				$field->required = 0;				
			$field->save();
			
			$message = 'Field successfully added.';
			$message_verbose = $message.' Field ID '.$field->id.'.';
			Log::info($message_verbose);
			return Redirect::action('FieldsController@add')
				->with('flash_notice', $message);
		}
		
		return Redirect::back()
			->withErrors($validator);
	}

	public function edit($field_id)
	{
		// get collection of this field		
		$field = Field::findOrFail($field_id);

		$types = Field::getTypes();
		// use $types as array-keys AND as array-values	
		$field_types = array_combine($types,$types);

		return View::make('field_form', compact('field','field_types') )
			->with('action', 'edit');
	}

	public function handle_edit($field_id)
	{
		$data = Input::all();
		$types = Field::getTypes();

		$rules = array(
			'name' => 'required|alpha_num_dash_spaces',
			'type' => 'in:'.implode(',', $types)	
		);

		$validator = Validator::make($data, $rules);

		if ($validator->passes())
		{
			$field = Field::findOrFail($field_id);
			$field->name = Input::get('name');
			$field->type = Input::get('type');
			if( Input::get('required') !== Null )
				$field->required = Input::get('required');
			else
				$field->required = 0;				
			$field->save();

			$message = 'Field successfully modified.';
			$message_verbose = $message.' Field ID '.$field->id.'.';
			Log::info($message_verbose);	
			return Redirect::action('FieldsController@index')
				->with('flash_notice', $message);
		}
		
		return Redirect::back()
			->withErrors($validator);
	}

	public function delete($field_id)
	{
		$field = Field::findOrFail($field_id);
		if ( $field->fieldData()->get()->isEmpty() )
		{
			$field->delete();
			
			$message = 'Field successfully deleted.';
			$message_verbose = $message.' Field ID '.$field->id.'.';
			Log::info($message_verbose);
			return Redirect::action('FieldsController@index')
				->with('flash_notice', $message);
		}
		
		return Redirect::action('FieldsController@index')
				->with('flash_error', 'This field is still used!
					Make sure that no Category use it before to delete it.');
	}

}
