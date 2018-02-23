<?php
namespace Gt\Input\InputData;

trait KeyValueIterator {
	/** @var int */
	protected $iteratorKey;

	public function current():?string {
		return $this->parameters[$this->getIteratorDataKey()];
	}

	public function next():void {
		$this->iteratorKey++;
	}

	public function key():string {
		return $this->getIteratorDataKey();
	}

	public function valid() {
		return !empty($this->getIteratorDataKey());
	}

	public function rewind() {
		$this->iteratorKey = 0;
	}

	protected function getIteratorDataKey():?string {
		$keys = [];

		if(is_array($this->parameters)) {
			$keys = $this->parameters;
		}
		else if($this->parameters instanceof InputData) {
			$keys = $this->parameters->getKeys();
		}

		return $keys[$this->iteratorKey] ?? null;
	}
}