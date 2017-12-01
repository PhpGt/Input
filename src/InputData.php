<?php
namespace Gt\Input;

use ArrayAccess;
use Iterator;

class InputData implements ArrayAccess, Iterator {
	/** @var array */
	protected $data;
	/** @var array */
	protected $dataKeys;
	/** @var int */
	protected $iteratorKey;

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

	protected function storeDataKeys():void {
		$this->dataKeys = array_keys($this->data);
	}

	public function offsetExists($offset):bool {
		return isset($this->data[$offset]);
	}

	public function offsetGet($offset):?string {
		return $this->data[$offset] ?? null;
	}

	public function offsetSet($offset, $value):void {
		throw new ReadOnlyInputModificationException($offset);
	}

	public function offsetUnset($offset) {
		throw new ReadOnlyInputModificationException($offset);
	}

	public function current():?string {
		return $this->data[$this->getIteratorDataKey()];
	}

	public function next():void {
		$this->iteratorKey++;
	}

	public function key():string {
		return $this->getIteratorDataKey();
	}

	public function valid() {
		return !empty($this->getIteratorDataKey());
	}

	public function rewind() {
		$this->iteratorKey = 0;
	}

	protected function getIteratorDataKey():?string {
		return $this->dataKeys[$this->iteratorKey] ?? null;
	}
}