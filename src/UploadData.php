<?php
namespace Gt\Input;

class UploadData extends AbstractInputData {
	public function __construct(array $files) {
		$files = $this->normalizeArray($files);
	}

	/**
	 * @link http://php.net/manual/en/iterator.current.php
	 */
	public function current() {
		// TODO: Implement current() method.
	}

	/**
	 * @link http://php.net/manual/en/iterator.next.php
	 */
	public function next():void {
		// TODO: Implement next() method.
	}

	/**
	 * @link http://php.net/manual/en/iterator.key.php
	 */
	public function key() {
		// TODO: Implement key() method.
	}

	/**
	 * @link http://php.net/manual/en/iterator.valid.php
	 */
	public function valid():bool {
		// TODO: Implement valid() method.
	}

	/**
	 * @link http://php.net/manual/en/iterator.rewind.php
	 */
	public function rewind():void {
		// TODO: Implement rewind() method.
	}

	/**
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 */
	public function offsetExists($offset):bool {
		// TODO: Implement offsetExists() method.
	}

	/**
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 */
	public function offsetGet($offset) {
		// TODO: Implement offsetGet() method.
	}

	/**
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 */
	public function offsetSet($offset, $value):void {
		// TODO: Implement offsetSet() method.
	}

	/**
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 */
	public function offsetUnset($offset):void {
		// TODO: Implement offsetUnset() method.
	}

	/**
	 * @link http://php.net/manual/en/countable.count.php
	 */
	public function count():int {
		// TODO: Implement count() method.
	}

	/**
	 * The files array is an associative array where the key is the name of the request
	 * parameter, and the value is another associative array with keys:
	 * + name
	 * + type
	 * + tmp_name
	 * + error
	 * + size
	 * Each key's value is string, unless the request parameter name ends with [], in which case
	 * each value is another array. This function normalises the array to the latter.
	 */
	protected function normalizeArray($files):array {
		foreach($files as $parameterName => $fileDetailArray) {
			foreach($fileDetailArray as $key => $value) {
				if(!is_array($value)) {
					$files[$parameterName][$key] = [$value];
				}
			}
		}

		return $files;
	}
}