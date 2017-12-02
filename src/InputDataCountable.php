<?php
namespace Gt\Input;

trait InputDataCountable {
	public function count():int {
		return count($this->data);
	}
}