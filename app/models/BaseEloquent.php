<?php

class BaseEloquent extends Eloquent
{
	public function newCollection(array $models = array())
	{
		return new BaseCollection($models);
	}
}
