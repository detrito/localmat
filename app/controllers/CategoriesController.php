<?php

class CategoriesController extends BaseController
{
    public function index()
    {
		$categories = Category::all();
        return View::make('category_index', compact('categories'));
    }

    public function add()
    {
		$fields = Field::all();

        return View::make('category_form', compact('fields'))
			->with('action', 'add');
	}

	public function handle_add()
	{
		$data = Input::only('name');
		$field_ids = Input::except('name');

		return $this->insert_data($data, $field_ids, 'add');
	}

	public function edit($category_id)
	{
		$category = Category::find($category_id);
		$fields = Field::all();	
		$field_ids = $category->fields()->lists('id');

		$field_values = array();
		foreach($fields as $field)
		{
			if( in_array($field->id, $field_ids) )
			{
				$field_values[$field->id] = true;
			}
			else
			{
				$field_values[$field->id] = false;
			}
		}

		return View::make('category_form',
					compact('category','fields','field_values'))
					->with('action','edit');
	}

	public function handle_edit($category_id)
	{
		$data = Input::only('name');
		$field_ids = Input::except('name');

		return $this->insert_data($data, $field_ids, 'edit', $category_id);
	}

	private function insert_data($data, $field_ids, $action, $category_id=Null)
	{
		$rules = array(
			'name' => 'required|alpha|unique:lm_categories'
		);

		// force unique category-name to ignore $category_id
		if( $action == 'edit')
		{
			$rules['name'] .= ',name,'.$category_id;
		}

		$validator = Validator::make($data, $rules);
		
		if ( $validator->passes() )
		{
			// check that a field has been chosed
			if( empty($field_ids) )
			{
				return Redirect::back()
					->withErrors('You must choose at least one field!');
			}
			
			if ($action == 'add')
			{
				$category = new Category;
			}
			elseif ($action == 'edit')
			{
				$category = Category::find($category_id);
				// detach all fields before to attach the new ones
				$category->fields()->detach();
			}
			$category->name = $data['name'];
			$category->save();

			foreach($field_ids as $field_id)
			{
				$field = Field::find($field_id);
				$category->fields()->save($field);
			}

			if($action == 'add')
			{
				return Redirect::action('CategoriesController@add')
					->with('flash_notice', 'Category successfully added.');
			}
			elseif($action == 'edit')
			{
				return Redirect::action('CategoriesController@index')
					->with('flash_notice', 'Category successfully modified.');
			}
		}

		return Redirect::back()
			->withErrors($validator);
	}

	public function delete($category_id)
	{
		$category = Category::find($category_id);
		if ( $category->articles()->get()->isEmpty() )
		{
			// remove all relationships to the fields for this category
			$category->fields()->detach();
			$category->delete();
			return Redirect::action('CategoriesController@index')
				->with('flash_notice', 'Category successfully deleted.');
		}
		return Redirect::action('FieldsController@index')
				->with('flash_error', 'This category is still used!
					Make sure that no Article use it before to delete it.');
	}
}
