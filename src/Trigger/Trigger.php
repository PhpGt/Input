<?php
namespace Gt\Input\Trigger;

use Gt\Input\CallOrOutOfSequenceException;
use Gt\Input\Input;
use Gt\Input\InputData\InputDataFactory;

class Trigger {
	protected Input $input;

	/** @var array<array<string>> */
	protected array $matches;
	/** @var array<string> */
	protected array $keyMatches;
	/** @var array<string> */
	protected array $with;
	/** @var array<string> */
	protected array $without;
	/** @var array<Callback> */
	protected array $callbacks;
	protected ?bool $hasFired;
	private InputDataFactory $inputDataFactory;

	public function __construct(Input $input) {
		$this->inputDataFactory = new InputDataFactory();
		$this->input = $input;

		$this->matches = [];
		$this->keyMatches = [];
		$this->with = [];
		$this->without = [];
		$this->callbacks = [];
		$this->hasFired = null;
	}

	/** @param string|array<string, string>...$matches */
	public function when(string|array...$matches):self {
		$matches = $this->flattenArray($matches);

		foreach($matches as $key => $match) {
			if(is_int($key)) {
				$this->setKeyTrigger($match);
			}
			else {
				$this->setTrigger($key, $match);
			}
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

	protected function setKeyTrigger(string $key):self {
		if(!isset($this->keyMatches[$key])) {
			$this->keyMatches []= $key;
		}

		return $this;
	}

	public function call(callable $callback, string...$args):self {
		$this->callbacks []= new Callback($callback, ...$args);
		$this->hasFired = $this->fire();
		return $this;
	}

	public function or(callable $callback, string...$args):self {
		if(is_null($this->hasFired)) {
			throw new CallOrOutOfSequenceException();
		}
		else {
			if(!$this->hasFired) {
				call_user_func_array($callback, $args);
			}
		}
		return $this;
	}

	public function fire():bool {
		$fired = true;

		foreach($this->matches as $key => $matchList) {
			if($this->input->contains($key)) {
				if(empty($matchList)) {
					continue;
				}

				if(!in_array($this->input->get($key), $matchList)) {
					$fired = false;
				}
			}
			else {
				$fired = false;
			}
		}

		foreach($this->keyMatches as $key) {
			if(!$this->input->contains($key)) {
				$fired = false;
			}
		}

		if($fired) {
			$this->callCallbacks();
		}

		return $fired;
	}

	protected function callCallbacks():void {
		$fields = $this->inputDataFactory->create(
			$this->input,
			$this->with,
			$this->without
		);

		foreach($this->callbacks as $callback) {
			/** @var $callback \Gt\Input\Trigger\Callback */
			$callback->call($fields);
		}
	}

	/**
	 * @param array<int|string, string|array<int|string, mixed>> $array
	 * @return array<string|int, string>
	 */
	protected function flattenArray(array $array):array {
		$result = [];

		foreach($array as $inner) {
			if(is_array($inner)) {
				foreach($inner as $key => $value) {
					if(is_array($value)) {
						$result = array_merge(
							$result,
							$value
						);
					}
					else {
						if(is_int($key)) {
							$result[] = $value;
						}
						else {
							$result[$key] = $value;
						}
					}
				}
			}
			else {
				$result = array_merge(
					$result,
					[$inner]
				);
			}
		}

		return $result;
	}
}
