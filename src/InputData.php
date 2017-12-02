<?php
namespace Gt\Input;

use ArrayAccess;
use Countable;
use Iterator;

class InputData implements ArrayAccess, Countable, Iterator {
	use InputDataArrayAccess;
	use InputDataCountable;
	use InputDataIterator;

	public function __construct(array...$sources) {
		$this->data = [];

		foreach($sources as $source) {
			foreach($source as $key => $value) {
				$this->add($key, $value);
			}
		}

		$this->storeDataKeys();
	}

	public function add(string $key, string $value):self {
		$this->data[$key] = $value;
		$this->storeDataKeys();
		return $this;
	}

	public function remove(string...$keys):self {
		foreach($keys as $key) {
			if(isset($this->data[$key])) {
				unset($this->data[$key]);
			}
		}

		$this->storeDataKeys();
		return $this;
	}

	public function removeExcept(string...$keys):self {
		foreach($this->data as $key => $value) {
			if(!in_array($key, $keys)) {
				unset($this->data[$key]);
			}
		}

		$this->storeDataKeys();
		return $this;
	}

	public function getKeys():array {
		return $this->dataKeys;
	}
}