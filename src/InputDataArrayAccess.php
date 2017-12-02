<?php
namespace Gt\Input;

trait InputDataArrayAccess {
	/** @var array */
	protected $data;
	/** @var array|InputData */
	protected $dataKeys;

	public function offsetExists($offset):bool {
		return isset($this->data[$offset]);
	}

	public function offsetGet($offset):?string {
		return $this->data[$offset] ?? null;
	}

	public function offsetSet($offset, $value):void {
		if($this->data instanceof InputData) {
			$this->add($offset, $value);
		}
		else {
			$this->data[$offset] = $value;
			$this->storeDataKeys();
		}
	}

	public function offsetUnset($offset) {
		if($this->data instanceof InputData) {
			$this->remove($offset);
		}
		else {
			unset($this->data[$offset]);
			$this->storeDataKeys();
		}
	}

	protected function storeDataKeys():void {
		if($this->data instanceof InputData) {
			$this->dataKeys = $this->data->getKeys();
		}
		else {
			$this->dataKeys = array_keys($this->data);
		}
	}
}