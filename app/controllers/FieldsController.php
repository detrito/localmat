<?php

class FieldsController extends BaseController
{
	private $types = array('text','boolean','integer');	

	public function get_types()
	{
		return $types;
	}
	
    public function index()
    {
		$fields = Field::all();
        return View::make('field_index', compact('fields'));
    }

	public function add()
	{
		// use $types as array-keys AND as array-values	
		$field_types = array_combine($this->types,$this->types);
		return View::make('field_add', compact('field_types') );
	}

	public function handle_add()
	{
		$data = Input::all();
		$rules = array(
			'name' => 'required|alpha_num|unique:lm_fields',
			'type' => 'in:'.implode(',', $this->types)	
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

	public function addsome()
	{
	// add some fields
	$field = new Field;
	$field->name = "Description";
	$field->type = "text";
	$field->save();

	$field = new Field;
	$field->name = "Corde statique";
	$field->type = "boolean";
	$field->save();

	$field = new Field;
	$field->name = "Longueur";
	$field->type = "integer";
	$field->save();
	}
}
