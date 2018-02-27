<?php
namespace Gt\Input\InputData;

use ArrayAccess;
use Countable;
use Iterator;
use Gt\Input\InputData\Datum\InputDatum;

abstract class AbstractInputData implements ArrayAccess, Countable, Iterator {
	use KeyValueArrayAccess;
	use KeyValueCountable;
	use KeyValueIterator;

	/** @var InputDatum[] */
	protected $parameters = [];

	public function get(string $key):?InputDatum {
		return $this->parameters[$key] ?? null;
	}

	public function has(string $key):bool {
		return isset($this->parameters[$key]);
	}

	protected function set(string $key, InputDatum $value):void {
		$this->parameters[$key] = $value;
	}

	public function withKeyValue(string $key, InputDatum $value):self {
		$clone = clone($this);
		$clone->parameters[$key] = $value;
		return $clone;
	}
}