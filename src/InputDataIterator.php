<?php
namespace Gt\Input;

trait InputDataIterator {
	/** @var int */
	protected $iteratorKey;

	public function current():?string {
		return $this->data[$this->getIteratorDataKey()];
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
		return $this->dataKeys[$this->iteratorKey] ?? null;
	}
}