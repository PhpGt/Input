<?php
namespace Gt\Input\InputData;

use DateTime;
use DateTimeInterface;
use Exception;
use Gt\Input\DataNotCompatibleFormatException;
use Gt\Input\DataNotFileUploadException;
use Gt\Input\InputData\Datum\FileUpload;
use Gt\Input\InputData\Datum\InputDatum;
use Gt\Input\InputData\Datum\MultipleInputDatum;
use TypeError;

class InputData extends AbstractInputData {
	public function __construct(iterable...$sources) {
		$this->parameters = [];

		foreach($sources as $source) {
			foreach($source as $key => $value) {
				if(is_array($value)
				&& isset($value[0])) {
					$value = new MultipleInputDatum($value);
				}
				else if(!$value instanceof InputDatum) {
					$value = new InputDatum($value);
				}
				$this->add($key, $value);
			}
		}
	}

	public function getFile(string $key):FileUpload {
		try {
			/** @noinspection PhpIncompatibleReturnTypeInspection */
			return $this->get($key);
		}
		catch(TypeError $exception) {
			throw new DataNotFileUploadException($key);
		}
	}

	/**
	 * @return FileUpload[]
	 */
	public function getMultipleFile(string $key):MultipleInputDatum {
		return $this->get($key);
	}

	public function getDateTime(string $key):DateTimeInterface {
		try {
			$dateTime = new DateTime($this[$key]);
			return $dateTime;
		}
		catch(Exception $exception) {
			throw new DataNotCompatibleFormatException($key);
		}
	}

	/**
	 * @return DateTime[]
	 */
	public function getMultipleDateTime(string $key):MultipleInputDatum {
		return $this->get($key);
	}

	public function add(string $key, InputDatum $datum):self {
		$this->parameters[$key] = $datum;
		return $this;
	}

	public function addKeyValue(string $key, string $value):self {
		$datum = new InputDatum($value);
		return $this->add($key, $datum);
	}

	public function remove(string...$keys):self {
		foreach($keys as $key) {
			if(isset($this->parameters[$key])) {
				unset($this->parameters[$key]);
			}
		}

		return $this;
	}

	public function removeExcept(string...$keys):self {
		foreach($this->parameters as $key => $value) {
			if(!in_array($key, $keys)) {
				unset($this->parameters[$key]);
			}
		}

		return $this;
	}

	public function getKeys():array {
		return array_keys($this->parameters);
	}
}