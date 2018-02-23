<?php
namespace Gt\Input;

use ArrayAccess;
use Countable;
use Iterator;

abstract class AbstractInputData implements ArrayAccess, Countable, Iterator {
	use KeyValueArrayAccess;
	use KeyValueCountable;
	use KeyValueIterator;

	/** @var InputDatum[] */
	protected $data;

	public function get(string $key):?InputDatum {
		return $this->data[$key] ?? null;
	}

	protected function set(string $key, InputDatum $value):void {
		$this->data[$key] = $value;
	}

	public function withKeyValue(string $key, InputDatum $value):self {
		$clone = clone($this);
		$clone->data[$key] = $value;
		return $clone;
	}
}