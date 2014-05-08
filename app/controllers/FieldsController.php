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
			$field->save();
			
			return Redirect::action('FieldsController@add')
				->with('flash_notice', 'Field successfully added.');
		}
		
		return Redirect::back()
			->withErrors($validator);
	}

	public function edit($field_id)
	{
		// get collection of this field		
		$field = Field::find($field_id);

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
			$field = Field::find($field_id);
			$field->name = Input::get('name');
			$field->type = Input::get('type');
			$field->save();
			
			return Redirect::action('FieldsController@index')
				->with('flash_notice', 'Field successfully modified.');
		}
		
		return Redirect::back()
			->withErrors($validator);
	}

	public function delete($field_id)
	{
		$field = Field::find($field_id);
		if ( $field->attributes()->get()->isEmpty() )
		{
			$field->delete();
			return Redirect::action('FieldsController@index')
				->with('flash_notice', 'Field successfully deleted.');
		}
		return Redirect::action('FieldsController@index')
				->with('flash_error', 'This field is still used!
					Make sure that no Category use it before to delete it.');
	}

}
