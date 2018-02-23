<?php
namespace Gt\Input\InputData;

use Gt\Input\InputData\Datum\FileUpload;

class FileUploadInputData extends InputData {

	public function __construct(array $files) {
		$parameters = [];
		$files = $this->normalizeArray($files);

		// TODO: Set $this->parameters with kvp of files ($files[filename] => FileUpload(data))
		$parameters = $this->createParameters($files);
		parent::__construct($parameters);
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

	protected function createParameters(array $files):array {
		$parameters = [];

		foreach($files as $inputName => $details) {
			foreach($details["tmp_name"] as $i => $tmpPath) {
				$parameters []= new FileUpload(

				);
			}
		}

		return $parameters;
	}
}