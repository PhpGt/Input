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

	public function move(string $destinationPath, string $renameTo = null):void {

	}

	public function getRealPath():string {

	}

	public function getOriginalName():string {

	}

	public function getOriginalExtension():string {

	}

	public function getSize():int {

	}

	public function getMimeType():string {

	}
}