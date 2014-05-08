<?php
 
use Illuminate\Database\Eloquent\Collection;
 
class BaseCollection extends Collection {
 
	// Collection method to to sort by comma separated string of IDs.
	// Models which do not appear in the given order are added to the
	// end in ID order.	
	// by danharper - https://gist.github.com/danharper/8596456

	public function sortByOrder($order, $delimeter = ',')
	{
		$order = is_array($order) ? $order : explode($delimeter, $order);
		return $this->sortBy(
			function($item) use ($order)
			{
				$found = array_search($item->id, $order);
				return $found === false ? 99999 + $item->id : $found;
			});
	}

	// Actually not used, but interesting (and it works!)
	/*
	public function getWhere($value, $key)
	{
		$index = $this->fetch($key)->toArray();
		$collection = array();
		foreach ($index as $k => $val) {
			if($value == $val) $collection[] = $this->items[$k];
		}
	return count($collection) ? new static($collection) : null;
	}
	// The __call() magic method, required for this dynamic getWhere() method
	public function __call($method, $args)
	{
		$key = snake_case(substr($method, 8));
		$args[] = $key;
		return call_user_func_array(array($this, 'getWhere'), $args);
	}
	*/
}
