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
		var_dump($types);
			
		// use $types as array-keys AND as array-values	
		$field_types = array_combine($types,$types);
		return View::make('field_add', compact('field_types') );
	}

	public function handle_add()
	{
		$data = Input::all();
		$types = Field::getTypes();

		$rules = array(
			'name' => 'required|alpha_num|unique:lm_fields',
			'type' => 'in:'.implode(',', $types)	
		);

		$validator = Validator::make($data, $rules);
		if ($validator->passes())
		{
			$field = new Field;
			$field->name = Input::get('name');
			$field->type = Input::get('type');
			$field->rule = $field->getDefaultRule(Input::get('type'));
			$field->save();
			
			return Redirect::action('FieldsController@add')
				->with('flash_notice', 'Field successfully added.');
		}
		
		return Redirect::back()
			->withErrors($validator);
	}

}
