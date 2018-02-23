<?php
namespace Gt\Input\InputData;

class FileUploadInputData extends InputData {

	public function __construct(array $files) {
		$files = $this->normalizeArray($files);

		// TODO: Set $this->data with kvp of files ($files[filename] => FileUpload(data))
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