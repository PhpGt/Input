<?php
namespace Gt\Input\InputData;

class InputDatum {
	protected $value;

	public function __construct($value) {
		$this->value = $value;
	}
}