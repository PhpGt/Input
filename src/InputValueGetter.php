<?php
namespace Gt\Input;

trait InputValueGetter {
	public function getString(string $key):?string {
		return $this->get($key);
	}

	public function getInt(string $key):?int {
		$value = $this->getString($key);
		if(is_null($value)) {
			return null;
		}

		return (int)$value;
	}

	public function getFloat(string $key):?float {
		$value = $this->getString($key);
		if(is_null($value)) {
			return null;
		}

		return (float)$value;
	}

	public function getBool(string $key):?bool {
		$value = $this->getString($key);
		if(is_null($value)) {
			return null;
		}

		return (bool)$value;
	}
}