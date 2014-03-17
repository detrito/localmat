<?php

class FieldsController extends BaseController
{
	private $types = array('text', 'integer','integerpositive', 'boolean');	
	private $default_rule = array(
		'text' => "required|alpha_spaces|max:64",
		'integer' => "required|integer",
		'integerpositive' => "required|integer|between:0,100000",
		'boolean' => "integer|between:0,1"
		);

	public function get_types()
	{
		return $this->types;
	}

	public function get_default_rule($type)
	{
		return $this->default_rule[$type];
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
			$field->rule = $this->get_default_rule(Input::get('type'));
			$field->save();
			
			return Redirect::action('FieldsController@add')
				->with('flash_notice', 'Field successfully added.');
		}
		
		return Redirect::back()
			->withErrors($validator);
	}

}
