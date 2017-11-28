<?php
namespace Gt\Input;

class Callback {
	/** @var callable */
	private $callback;
	private $args;

	public function __construct(callable $callback, ...$args) {
		$this->callback = $callback;
		$this->args = $args;
	}

	public function call(InputData $data):void {
		$parameters = [$data];
		foreach($this->args as $arg) {
			$parameters []= $arg;
		}

		call_user_func_array($this->callback, $parameters);
	}
}