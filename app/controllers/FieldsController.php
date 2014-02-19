<?php

class FieldsController extends BaseController
{
    public function index()
    {
		return "fields - index";
    }

	public function add()
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
