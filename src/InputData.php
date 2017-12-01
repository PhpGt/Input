<?php
namespace Gt\Input;

use Iterator;

class InputData implements Iterator {
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

		$this->dataKeys = array_keys($this->data);
	}

	public function add(string $key, string $value) {
		$this->data[$key] = $value;
	}

	public function remove(string...$keys) {
		foreach($keys as $key) {
			if(isset($this->data[$key])) {
				unset($this->data[$key]);
			}
		}
	}

	public function removeExcept(string...$keys) {
		foreach($this->data as $key => $value) {
			if(!in_array($key, $keys)) {
				unset($this->data[$key]);
			}
		}
	}

	public function __isset(string $name):bool {
		return isset($this->data[$name]);
	}

	public function __get(string $name):?string {
		return $this->data[$name] ?? null;
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