<?php
namespace Gt\Input\InputData\Datum;

use ArrayAccess;
use Gt\Input\ImmutableObjectModificationException;
use Iterator;

class MultipleInputDatum extends InputDatum implements ArrayAccess, Iterator {
	protected $iteratorKey;

	public function __construct($value) {
		parent::__construct($value);

		$this->iteratorKey = 0;
	}

	public function __toString():string {
		return implode(", ", $this->value);
	}

	public function toArray():array {
		$array = [];

		foreach($this as $key => $value) {
			$array[$key] = (string)$value;
		}

		return $array;
	}

	/**
	 * @link http://php.net/manual/en/iterator.current.php
	 */
	public function current() {
		return $this->offsetGet($this->iteratorKey);
	}

	/**
	 * @link http://php.net/manual/en/iterator.next.php
	 */
	public function next():void {
		$this->iteratorKey++;
	}

	/**
	 * @link http://php.net/manual/en/iterator.key.php
	 */
	public function key():int {
		return $this->iteratorKey;
	}

	/**
	 * @link http://php.net/manual/en/iterator.valid.php
	 */
	public function valid():bool {
		return $this->offsetExists($this->iteratorKey);
	}

	/**
	 * @link http://php.net/manual/en/iterator.rewind.php
	 */
	public function rewind():void {
		$this->iteratorKey = 0;
	}

	/**
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 */
	public function offsetExists($offset):bool {
		return isset($this->value[$offset]);
	}

	/**
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 */
	public function offsetGet($offset) {
		return $this->value[$offset];
	}

	/**
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 */
	public function offsetSet($offset, $value):void {
		throw new ImmutableObjectModificationException();
	}

	/**
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 */
	public function offsetUnset($offset):void {
		throw new ImmutableObjectModificationException();
	}
}