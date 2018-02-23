<?php
namespace Gt\Input;

trait KeyValueCountable {
	public function count():int {
		return count($this->data);
	}
}