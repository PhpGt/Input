<?php
namespace Gt\Input;

class InputDatum {
	protected $value;

	public function __construct($value) {
		$this->value = $value;
	}
}