<?php
namespace Gt\Input;

use ArrayAccess;
use Countable;
use Iterator;
use Psr\Http\Message\StreamInterface;

class Input implements ArrayAccess, Countable, Iterator {
	use InputDataArrayAccess;
	use InputDataCountable;
	use InputDataIterator;

	const DATA_QUERYSTRING = "get";
	const DATA_BODY = "post";
	const DATA_FILES = "files";
	const DATA_COMBINED = "both";

	/** @var BodyStream */
	protected $bodyStream;

	/** @var UploadData */
	protected $uploadedFileParameters;
	/** @var InputData */
	protected $queryStringParameters;
	/** @var InputData */
	protected $bodyParameters;

	public function __construct(
	array $get = [],
	array $post = [],
	array $files = [],
	string $bodyPath = "php://input"
	) {
		$this->bodyStream = new BodyStream($bodyPath);
		// TODO: The following three variables can extend the same base class. AbstractInputData?
		$this->queryStringParameters = new InputData($get);
		$this->bodyParameters = new InputData($post);
		$this->uploadedFileParameters = new UploadData($files);

		$this->data = new InputData($get, $post, $this->uploadedFileParameters);
		$this->dataKeys = $this->data->getKeys();
	}

	/**
	 * Returns the input payload as a streamable HTTP request body.
	 */
	public function getStream():StreamInterface {
		return $this->bodyStream;
	}

	/**
	 * Get a particular input value by its key. To specify either GET or POST variables, pass
	 * Input::METHOD_GET or Input::METHOD_POST as the second parameter (defaults to
	 * Input::METHOD_BOTH).
	 */
	public function get(string $key, string $method = null):?InputDatum {
		if(is_null($method)) {
			$method = self::DATA_COMBINED;
		}

		$data = null;

		if($this->has($key, $method)) {
			switch($method) {
			case self::DATA_QUERYSTRING:
				$data = $this->getQueryStringParameter($key);
				break;

			case self::DATA_BODY:
				$data =$this->getBodyParameter($key);
				break;

			case self::DATA_FILES:
				$data =$this->getUploadedFileParameter($key);
				break;

			case self::DATA_COMBINED:
				$data = $this->data[$key];
				break;

			default:
				throw new InvalidInputMethodException($method);
			}
		}

		return $data;
	}

	public function getQueryStringParameter(string $key):?InputDatum {
		if($this->hasQueryStringParameter($key)) {
			return $this->queryStringParameters[$key];
		}
		return null;
	}

	public function getBodyParameter(string $key):?InputDatum {
		if($this->hasBodyParameter($key)) {
			return $this->bodyParameters[$key];
		}
		return null;
	}

	public function getUploadedFileParameter(string $key):?InputDatum {
		if($this->hasUploadedFileParameter($key)) {
			return $this->uploadedFileParameters[$key];
		}
		return null;
	}

	/**
	 * Does the input contain the specified key?
	 */
	public function has(string $key, string $method = null):bool {
		if(is_null($method)) {
			$method = self::DATA_COMBINED;
		}

		$isset = false;

		switch($method) {
		case self::DATA_QUERYSTRING:
			$isset = $this->hasQueryStringParameter($key);
			break;

		case self::DATA_BODY:
			$isset =$this->hasBodyParameter($key);
			break;

		case self::DATA_FILES:
			$isset =$this->hasUploadedFileParameter($key);
			break;

		case self::DATA_COMBINED:
			$isset = isset($this->data[$key]);
			break;

		default:
			throw new InvalidInputMethodException($method);
		}

		return $isset;
	}

	public function hasQueryStringParameter(string $key):bool {
		return isset($this->queryStringParameters[$key]);
	}

	public function hasBodyParameter(string $key):bool {
		return isset($this->bodyParameters[$key]);
	}

	public function hasUploadedFileParameter(string $key):bool {
		return isset($this->uploadedFileParameters[$key]);
	}

	/**
	 * Get an InputData object containing all request variables. To specify only GET or POST
	 * variables, pass Input::METHOD_GET or Input::METHOD_POST.
	 * TODO: Return type needs to be AbstractInputData
	 */
	public function getAll(string $method = null):InputData {
		if(is_null($method)) {
			$method = self::DATA_COMBINED;
		}

		switch($method) {
		case self::DATA_QUERYSTRING:
			return $this->queryStringParameters;
		case self::DATA_BODY:
			return $this->bodyParameters;
		case self::DATA_FILES:
			return $this->uploadedFileParameters;
		case self::DATA_COMBINED:
			return $this->data;
		default:
			throw new InvalidInputMethodException($method);
		}
	}

	/**
	 * Return a "do" Trigger, matching when a request variable is present with the
	 * provided $match value.
	 */
	public function do(string $match):Trigger {
		return $this->when(["do" => $match]);
	}

	/**
	 * Return a Trigger, firing when one or more request variables are present with
	 * the provided key value pair(s) are present.
	 *
	 * $matches is an associative array, where the key is a request variable's name and the
	 * value is the request variable's value to match.
	 */
	public function when(array $matches):Trigger {
		$trigger = new Trigger($this);
		$trigger->when($matches);
		return $trigger;
	}

	/**
	 * Return a Trigger that will only pass the provided keys to its callback.
	 */
	public function with(string...$keys):Trigger {
		return $this->newTrigger("with", ...$keys);
	}

	/**
	 * Return a Trigger that will pass all keys apart from the provided keys to its callback.
	 */
	public function without(string...$keys):Trigger {
		return $this->newTrigger("without", ...$keys);
	}

	/**
	 * Return a Trigger that will pass all keys to its callback.
	 */
	public function withAll():Trigger {
		return $this->newTrigger("withAll");
	}

	protected function newTrigger(string $functionName, ...$args):Trigger {
		$trigger = new Trigger($this);
		return $trigger->$functionName(...$args);
	}
}