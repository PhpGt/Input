<?php
namespace Gt\Input\InputData\Datum;

use Gt\Input\UploadedFileMoveException;
use Gt\Input\UploadedFileSecurityException;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use SplFileInfo;
use TypeError;

class FileUpload extends InputDatum implements UploadedFileInterface {
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

	/**
	 * Retrieve a stream representing the uploaded file.
	 *
	 * This method MUST return a StreamInterface instance, representing the
	 * uploaded file. The purpose of this method is to allow utilizing native PHP
	 * stream functionality to manipulate the file upload, such as
	 * stream_copy_to_stream() (though the result will need to be decorated in a
	 * native PHP stream wrapper to work with such functions).
	 *
	 * If the moveTo() method has been called previously, this method MUST raise
	 * an exception.
	 *
	 * @return StreamInterface Stream representation of the uploaded file.
	 * @throws \RuntimeException in cases when no stream is available or can be
	 *     created.
	 */
	public function getStream() {
		// TODO: Implement getStream() method.
	}

	/**
	 * Move the uploaded file to a new location.
	 *
	 * Use this method as an alternative to move_uploaded_file(). This method is
	 * guaranteed to work in both SAPI and non-SAPI environments.
	 * Implementations must determine which environment they are in, and use the
	 * appropriate method (move_uploaded_file(), rename(), or a stream
	 * operation) to perform the operation.
	 *
	 * $targetPath may be an absolute path, or a relative path. If it is a
	 * relative path, resolution should be the same as used by PHP's rename()
	 * function.
	 *
	 * The original file or stream MUST be removed on completion.
	 *
	 * If this method is called more than once, any subsequent calls MUST raise
	 * an exception.
	 *
	 * When used in an SAPI environment where $_FILES is populated, when writing
	 * files via moveTo(), is_uploaded_file() and move_uploaded_file() SHOULD be
	 * used to ensure permissions and upload status are verified correctly.
	 *
	 * If you wish to move to a stream, use getStream(), as SAPI operations
	 * cannot guarantee writing to stream destinations.
	 *
	 * @see http://php.net/is_uploaded_file
	 * @see http://php.net/move_uploaded_file
	 * @param string $targetPath Path to which to move the uploaded file.
	 * @throws InvalidArgumentException if the $targetPath specified is invalid.
	 * @throws \RuntimeException on any error during the move operation, or on
	 *     the second or subsequent call to the method.
	 */
	public function moveTo($targetPath) {
		if(!is_uploaded_file($this->tempFilePath)) {
			throw new UploadedFileSecurityException($this->tempFilePath);
		}

		if(!is_string($targetPath)) {
			throw new TypeError("Argument 1 passed to " . __METHOD__ . " must be of type string, " . gettype($targetPath) . "given");
		}

		$targetPath = str_replace(
			["/", "\\"],
			DIRECTORY_SEPARATOR,
			$targetPath
		);

		$success = move_uploaded_file(
			$this->tempFilePath,
			$targetPath
		);

		if(!$success) {
			throw new UploadedFileMoveException($this->tempFilePath);
		}
	}

	/**
	 * Retrieve the error associated with the uploaded file.
	 *
	 * The return value MUST be one of PHP's UPLOAD_ERR_XXX constants.
	 *
	 * If the file was uploaded successfully, this method MUST return
	 * UPLOAD_ERR_OK.
	 *
	 * Implementations SHOULD return the value stored in the "error" key of
	 * the file in the $_FILES array.
	 *
	 * @see http://php.net/manual/en/features.file-upload.errors.php
	 * @return int One of PHP's UPLOAD_ERR_XXX constants.
	 */
	public function getError() {
// Note that this class ALWAYS returns UPLOAD_ERR_OK, due to failed uploads being
// represented by another class, FailedFileUpload.
		return UPLOAD_ERR_OK;
	}

	/**
	 * Retrieve the filename sent by the client.
	 *
	 * Do not trust the value returned by this method. A client could send
	 * a malicious filename with the intention to corrupt or hack your
	 * application.
	 *
	 * Implementations SHOULD return the value stored in the "name" key of
	 * the file in the $_FILES array.
	 *
	 * @return string|null The filename sent by the client or null if none
	 *     was provided.
	 */
	public function getClientFilename() {
		return $this->originalFileName;
	}

	/**
	 * Retrieve the media type sent by the client.
	 *
	 * Do not trust the value returned by this method. A client could send
	 * a malicious media type with the intention to corrupt or hack your
	 * application.
	 *
	 * Implementations SHOULD return the value stored in the "type" key of
	 * the file in the $_FILES array.
	 *
	 * @return string|null The media type sent by the client or null if none
	 *     was provided.
	 */
	public function getClientMediaType() {
		return $this->getMimeType();
	}
}