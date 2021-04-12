<?php
namespace Gt\Input\InputData;

use ArrayAccess;
use Countable;
use Gt\Input\InputValueGetter;
use Iterator;
use Gt\Input\InputData\Datum\InputDatum;

abstract class AbstractInputData implements ArrayAccess, Countable, Iterator {
	use InputValueGetter;
	use KeyValueArrayAccess;
	use KeyValueCountable;
	use KeyValueIterator;

	/** @var QueryStringInputData */
	protected $queryStringParameters;
	/** @var BodyInputData */
	protected $bodyParameters;

	/** @return mixed|null */
	public function get(string $key) {
		return $this->parameters[$key] ?? null;
	}

	public function contains(string $key):bool {
		return isset($this->parameters[$key]);
	}

	public function hasValue(string $key):bool {
		$value = $this->parameters[$key] ?? "";
		return (strlen((string)$value) > 0);
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
