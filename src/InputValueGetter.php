<?php
namespace Gt\Input;

use DateTime;
use DateTimeInterface;
use Exception;
use Gt\Input\InputData\CombinedInputData;
use Gt\Input\InputData\Datum\FileUpload;
use Gt\Input\InputData\Datum\InputDatum;
use Gt\Input\InputData\Datum\MultipleInputDatum;
use Gt\Input\InputData\FileUploadInputData;
use Gt\Input\InputData\InputData;
use TypeError;

trait InputValueGetter {
	protected FileUploadInputData $fileUploadParameters;
	/** @var array<string, string>|CombinedInputData */
	protected array|CombinedInputData $parameters;

	public function getString(string $key):?string {
		return $this->get($key);
	}

	/** @return array<string> */
	public function getMultipleString(string $key):array {
		return $this->getTypedArray($key, "string");
	}

	public function getInt(string $key):?int {
		$value = $this->getString($key);
		if(is_null($value) || strlen($value) === 0) {
			return null;
		}

		return (int)$value;
	}

	/** @return array<int> */
	public function getMultipleInt(string $key):array {
		return $this->getTypedArray($key, "int");
	}

	public function getFloat(string $key):?float {
		$value = $this->getString($key);
		if(is_null($value) || strlen($value) === 0) {
			return null;
		}

		return (float)$value;
	}

	/** @return array<float> */
	public function getMultipleFloat(string $key):array {
		return $this->getTypedArray($key, "float");
	}

	public function getBool(string $key):?bool {
		$value = $this->getString($key);
		if(is_null($value) || strlen($value) === 0) {
			return null;
		}

		return (bool)$value;
	}

	/** @return array<bool> */
	public function getMultipleBool(string $key):array {
		return $this->getTypedArray($key, "bool");
	}

	public function getFile(string $key):FileUpload {
		/** @var FileUploadInputData|InputDatum[] $params */
		$params = $this->fileUploadParameters ?? $this->parameters;

		try {
			/** @var MultipleInputDatum|FileUpload $file */
			$file = $params[$key];

			if($file instanceof MultipleInputDatum) {
				return $file->current();
			}

			return $file;
		}
		catch(TypeError) {
			throw new DataNotFileUploadException($key);
		}
	}

	/** @return array<FileUpload> */
	public function getMultipleFile(string $key):array {
		$multipleFileUpload = $this->get($key);
		if(!$multipleFileUpload instanceof MultipleInputDatum) {
			throw new InputException("Parameter '$key' is not a multiple file input.");
		}

		$array = [];

		/** @var FileUpload $file */
		foreach($multipleFileUpload as $file) {
			$name = $file->getClientFilename();
			$array[$name] = $file;
		}

		return $array;
	}

	public function getDateTime(
		string $key,
		string $format = null
	):?DateTimeInterface {
		$value = $this->getString($key);
		if(is_null($value) || strlen($value) === 0) {
			return null;
		}

		try {
			if($format) {
				$dateTime = DateTime::createFromFormat($format, $value);
			}
			else {
				$dateTime = new DateTime($value);
			}
		}
		catch(Exception $exception) {
			$dateTime = false;
		}

		if($dateTime === false) {
			throw new DataNotCompatibleFormatException($key);
		}

		return $dateTime;
	}

	/** @return array<DateTimeInterface> */
	public function getMultipleDateTime(string $key):array {
		return $this->getTypedArray($key, DateTimeInterface::class);
	}

	/**
	 * @template T of object
	 * @param string $key
	 * @param string|class-string<T> $typeName
	 * @return ($typeName is class-string ? array<T> : array<int|float|bool|string>)
	 */
	private function getTypedArray(string $key, string $typeName):array {
		$array = [];
		$datum = $this->get($key);

		if(is_null($datum)) {
			return $array;
		}

		if(!$datum instanceof MultipleInputDatum) {
			return $array;
		}

		foreach($datum as $item) {
			$item = (string)$item;

			$cast = match($typeName) {
				"int" => (int)$item,
				"float" => (float)$item,
				"bool" => (bool)$item,
				DateTimeInterface::class => new DateTime($item),
				default => $item,
			};

			array_push($array, $cast);
		}

		return $array;
	}
}
