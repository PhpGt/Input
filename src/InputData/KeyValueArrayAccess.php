<?php
namespace Gt\Input;

trait KeyValueArrayAccess {
	public function offsetExists($offset):bool {
		return isset($this->data[$offset]);
	}

	public function offsetGet($offset):?InputDatum {
		return $this->get($offset);
	}

	public function offsetSet($offset, $value):void {
		if($this->data instanceof InputData) {
			$this->data->add($offset, $value);
		}
		else {
			$this->data[$offset] = $value;
		}
	}

	public function offsetUnset($offset) {
		if($this->data instanceof InputData) {
			$this->data->remove($offset);
		}
		else {
			unset($this->data[$offset]);
		}
	}
}