<?php
namespace Gt\Input\InputData;

trait KeyValueArrayAccess {
	public function offsetExists($offset):bool {
		return isset($this->parameters[$offset]);
	}

	public function offsetGet($offset):?InputDatum {
		return $this->get($offset);
	}

	public function offsetSet($offset, $value):void {
		if($this->parameters instanceof InputData) {
			$this->parameters->add($offset, $value);
		}
		else {
			$this->parameters[$offset] = $value;
		}
	}

	public function offsetUnset($offset) {
		if($this->parameters instanceof InputData) {
			$this->parameters->remove($offset);
		}
		else {
			unset($this->parameters[$offset]);
		}
	}
}