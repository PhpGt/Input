<?php
namespace Gt\Input\InputData\Datum;

class InputDatum {
	protected mixed $value;

	public function __construct(mixed $value) {
		$this->value = $value;
	}

	public function __toString():string {
		return $this->value;
	}
}
