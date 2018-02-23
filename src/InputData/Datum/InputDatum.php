<?php
namespace Gt\Input\InputData\Datum;

class InputDatum {
	protected $value;

	public function __construct($value) {
		$this->value = $value;
	}

	public function __toString():string {
		return $this->value;
	}
}