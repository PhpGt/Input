<?php
namespace Gt\Input\InputData;

use Gt\Input\InputData\Datum\FailedFileUpload;
use Gt\Input\InputData\Datum\FileUpload;

class FileUploadInputData extends InputData {

	public function __construct(array $files) {
		$files = $this->normalizeArray($files);

		// TODO: Set $this->parameters with kvp of files ($files[filename] => FileUpload(data))
		$data = $this->createData($files);
		parent::__construct($data);
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

	protected function createData(array $files):array {
		$datumList = [];

		foreach($files as $inputName => $details) {
			foreach($details["tmp_name"] as $i => $tmpPath) {
				$params = [
					$details["name"][$i],
					$details["type"][$i],
					(int)$details["size"][$i],
					$details["tmp_name"][$i],
				];

				if($details["error"][$i] === UPLOAD_ERR_OK) {
					$datumList[$inputName] = new FileUpload(...$params);
				}
				else {
					$params []= (int)$details["error"][$i];
					$datumList[$inputName] = new FailedFileUpload(...$params);
				}
			}
		}

		return $datumList;
	}
}