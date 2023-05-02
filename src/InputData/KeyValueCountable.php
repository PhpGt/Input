<?php
namespace Gt\Input\InputData;

trait KeyValueCountable {
	public function count():int {
		return count($this->parameters);
	}
}
