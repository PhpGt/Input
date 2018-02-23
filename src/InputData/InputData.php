<?php
namespace Gt\Input;

use ArrayAccess;
use Countable;
use Iterator;

class InputData extends AbstractInputData {
	use KeyValueArrayAccess;
	use KeyValueCountable;

	public function __construct(iterable...$sources) {
		$this->data = [];

		foreach($sources as $source) {
			foreach($source as $key => $value) {
				if(is_string($value)) {
					$value = new InputDatum($value);
				}
				$this->add($key, $value);
			}
		}
	}

	public function add(string $key, InputDatum $datum):self {
		$this->data[$key] = $datum;
		return $this;
	}

	public function remove(string...$keys):self {
		foreach($keys as $key) {
			if(isset($this->data[$key])) {
				unset($this->data[$key]);
			}
		}

		return $this;
	}

	public function removeExcept(string...$keys):self {
		foreach($this->data as $key => $value) {
			if(!in_array($key, $keys)) {
				unset($this->data[$key]);
			}
		}

		return $this;
	}
}