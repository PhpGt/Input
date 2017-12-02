<?php
namespace Gt\Input;

class Trigger {
	/** @var Input */
	protected $input;

	protected $matches = [];
	protected $with = [];
	protected $without = [];
	/** @var Callback[] */
	protected $callbacks = [];

	public function __construct(Input $input) {
		$this->input = $input;
	}

	public function when(array $matches):self {
		foreach($matches as $key => $value) {
			$this->setTrigger($key, $value);
		}

		return $this;
	}

	public function with(string...$keys):self {
		foreach($keys as $key) {
			$this->with []= $key;
		}

		return $this;
	}

	public function without(string...$keys):self {
		foreach($keys as $key) {
			$this->without []= $key;
		}

		return $this;
	}

	public function withAll():self {
		$this->with = [];
		$this->without = [];

		return $this;
	}

	public function setTrigger(string $key, string $value):self {
		if(!isset($this->matches[$key])) {
			$this->matches[$key] = [];
		}

		$this->matches[$key] []= $value;

		return $this;
	}

	public function call(callable $callback, ...$args):self {
		$this->callbacks []= new Callback($callback, ...$args);
		$this->fire();

		return $this;
	}

	public function fire():bool {
		$fired = true;

		foreach($this->matches as $key => $matchList) {
			if($this->input->has($key)) {
				if(empty($matchList)) {
					continue;
				}

				if(!in_array($this->input->get($key),$matchList)) {
					$fired = false;
				}
			}
			else {
				$fired = false;
			}
		}

		if($fired) {
			$this->callCallbacks();
		}

		return $fired;
	}

	protected function callCallbacks() {
		$fields = InputDataFactory::create(
			$this->input,
			$this->with,
			$this->without
		);

		foreach($this->callbacks as $callback) {
			$callback->call($fields);
		}
	}
}