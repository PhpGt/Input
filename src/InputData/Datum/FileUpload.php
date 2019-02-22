<?php
namespace Gt\Input\InputData\Datum;

use Gt\Input\UploadedFileMoveException;
use Gt\Input\UploadedFileSecurityException;
use SplFileInfo;

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

	public function move(string $destinationDirectory, string $renameTo = null):void {
		if(!is_uploaded_file($this->tempFilePath)) {
			throw new UploadedFileSecurityException($this->tempFilePath);
		}

		if(is_null($renameTo)) {
			$renameTo = $this->originalFileName;
		}

		$destinationDirectory = str_replace(
			["/", "\\"],
			DIRECTORY_SEPARATOR,
			$destinationDirectory
		);

		if(!is_dir($destinationDirectory)) {
			mkdir($destinationDirectory, 0775, true);
		}

		$destinationPath = implode(DIRECTORY_SEPARATOR, [
			$destinationDirectory,
			$renameTo,
		]);

		$success = move_uploaded_file(
			$this->tempFilePath,
			$destinationPath
		);

		if(!$success) {
			throw new UploadedFileMoveException($this->tempFilePath);
		}
	}

	public function getFileInfo():SplFileInfo {
		return new SplFileInfo($this->tempFilePath);
	}

	public function getRealPath():string {
		return $this->tempFilePath;
	}

	public function getOriginalName():string {
		return $this->originalFileName;
	}

	public function getOriginalExtension():string {
		return pathinfo($this->originalFileName, PATHINFO_EXTENSION);
	}

	public function getSize():int {
		return $this->fileSize;
	}

	public function getMimeType():string {
		return $this->mimeType;
	}
}