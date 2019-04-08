<?php
namespace Gt\Input;

use DateTime;
use DateTimeInterface;
use Exception;
use Gt\Input\InputData\Datum\FileUpload;
use Gt\Input\InputData\Datum\InputDatum;
use Gt\Input\InputData\Datum\MultipleInputDatum;
use Gt\Input\InputData\FileUploadInputData;
use TypeError;

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
}