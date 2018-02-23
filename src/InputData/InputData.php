<?php
namespace Gt\Input\InputData;

class InputData extends AbstractInputData {
	public function __construct(iterable...$sources) {
		$this->parameters = [];

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
		$this->parameters[$key] = $datum;
		return $this;
	}

	public function remove(string...$keys):self {
		foreach($keys as $key) {
			if(isset($this->parameters[$key])) {
				unset($this->parameters[$key]);
			}
		}

		return $this;
	}

	public function removeExcept(string...$keys):self {
		foreach($this->parameters as $key => $value) {
			if(!in_array($key, $keys)) {
				unset($this->parameters[$key]);
			}
		}

		return $this;
	}
}