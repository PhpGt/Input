<?php
namespace Gt\Input\InputData\Datum;

class FileUpload extends InputDatum {
	protected $originalFileName;
	protected $mimeType;
	protected $fileSize;
	protected $tempFilePath;

	public function __construct(
		string $originalFilename,
		string $mimeType,
		int $fileSize,
		string $tempFilePath
	) {
		$this->originalFileName = $originalFilename;
		$this->mimeType = $mimeType;
		$this->fileSize = $fileSize;
		$this->tempFilePath = $tempFilePath;

		parent::__construct($originalFilename);
	}
}