<?php
namespace Gt\Input;

class InputData {
	/** @var array */
	protected $data;

	public function __construct(array...$sources) {
		$this->data = [];

		foreach($sources as $source) {
			foreach($source as $key => $value) {
				$this->add($key, $value);
			}
		}
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
}