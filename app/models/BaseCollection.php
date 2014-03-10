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
}
