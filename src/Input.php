<?php
namespace Gt\Input;

use ArrayAccess;
use Countable;
use Gt\Input\InputData\Datum\MultipleInputDatum;
use Iterator;
use Psr\Http\Message\StreamInterface;
use Gt\Input\Trigger\Trigger;
use Gt\Input\InputData\InputData;
use Gt\Input\InputData\Datum\InputDatum;
use Gt\Input\InputData\KeyValueArrayAccess;
use Gt\Input\InputData\KeyValueCountable;
use Gt\Input\InputData\KeyValueIterator;
use Gt\Input\InputData\BodyInputData;
use Gt\Input\InputData\CombinedInputData;
use Gt\Input\InputData\FileUploadInputData;
use Gt\Input\InputData\QueryStringInputData;

/**
 * @implements ArrayAccess<string, ?string>
 * @implements Iterator<string, ?string>
 */
class Input implements ArrayAccess, Countable, Iterator {
	use InputValueGetter;
	use KeyValueArrayAccess;
	use KeyValueCountable;
	use KeyValueIterator;

	const DATA_QUERYSTRING = "get";
	const DATA_BODY = "post";
	const DATA_FILES = "files";
	const DATA_COMBINED = "combined";

	protected BodyStream $bodyStream;
	protected QueryStringInputData $queryStringParameters;
	protected BodyInputData $bodyParameters;

	/**
	 * @param array<string, string|array<string>> $get
	 * @param array<string, string|array<string>> $post
	 * @param array<string, array<int|string, string|array<int|string>>> $files
	 * @param string $bodyPath
	 */
	public function __construct(
		array $get = [],
		array $post = [],
		array $files = [],
		string $bodyPath = "php://input"
	) {
		$this->bodyStream = new BodyStream($bodyPath);

		$this->queryStringParameters = new QueryStringInputData($get);
		$this->bodyParameters = new BodyInputData($post);
		$this->fileUploadParameters = new FileUploadInputData($files);

		$this->parameters = new CombinedInputData(
			$this->queryStringParameters,
			$this->bodyParameters,
			$this->fileUploadParameters
		);
	}

	/**
	 * Returns the input payload as a streamable HTTP request body.
	 */
	public function getStream():StreamInterface {
		return $this->bodyStream;
	}

	public function add(string $key, InputDatum $datum, string $method):void {
		switch($method) {
		case self::DATA_QUERYSTRING:
			$this->queryStringParameters =
				$this->queryStringParameters->withKeyValue(
					$key,
					$datum
				);
			break;

		case self::DATA_BODY:
			$this->bodyParameters =
				$this->bodyParameters->withKeyValue(
					$key,
					$datum
				);
			break;

		case self::DATA_FILES:
			$this->fileUploadParameters =
				$this->fileUploadParameters->withKeyValue(
					$key,
					$datum
				);
			break;

		default:
			throw new InvalidInputMethodException($method);
		}

		$this->parameters = new CombinedInputData(
			$this->queryStringParameters,
			$this->bodyParameters,
			$this->fileUploadParameters
		);
	}

	/**
	 * Get a particular input value by its key. To specify either GET or POST variables, pass
	 * Input::METHOD_GET or Input::METHOD_POST as the second parameter (defaults to
	 * Input::METHOD_BOTH).
	 */
	public function get(string $key, string $method = null):null|InputDatum|MultipleInputDatum|string {
		if(is_null($method)) {
			$method = self::DATA_COMBINED;
		}

		$data = match ($method) {
			self::DATA_QUERYSTRING => $this->queryStringParameters->get($key),
			self::DATA_BODY => $this->bodyParameters->get($key),
			self::DATA_FILES => $this->fileUploadParameters->getFile($key),
			self::DATA_COMBINED => $this->parameters->get($key),
			default => throw new InvalidInputMethodException($method),
		};

		return $data?->getValue();
	}

	/**
	 * Does the input contain the specified key?
	 */
	public function contains(string $key, string $method = null):bool {
		if(is_null($method)) {
			$method = self::DATA_COMBINED;
		}

		return match ($method) {
			self::DATA_QUERYSTRING => $this->containsQueryStringParameter($key),
			self::DATA_BODY => $this->containsBodyParameter($key),
			self::DATA_FILES => $this->containsFile($key),
			self::DATA_COMBINED => isset($this->parameters[$key]),
			default => throw new InvalidInputMethodException($method),
		};
	}

	public function containsQueryStringParameter(string $key):bool {
		return isset($this->queryStringParameters[$key]);
	}

	public function containsBodyParameter(string $key):bool {
		return isset($this->bodyParameters[$key]);
	}

	public function containsFile(string $key):bool {
		return isset($this->fileUploadParameters[$key]);
	}

	/**
	 * Get an InputData object containing all request variables. To specify only GET or POST
	 * variables, pass Input::METHOD_GET or Input::METHOD_POST.
	 */
	public function getAll(string $method = null):InputData {
		if(is_null($method)) {
			$method = self::DATA_COMBINED;
		}

		return match ($method) {
			self::DATA_QUERYSTRING => $this->queryStringParameters,
			self::DATA_BODY => $this->bodyParameters,
			self::DATA_FILES => $this->fileUploadParameters,
			self::DATA_COMBINED => $this->parameters,
			default => throw new InvalidInputMethodException($method),
		};
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
	 *
	 * @param array<string, string>|string $matches
	 */
	public function when(array|string...$matches):Trigger {
		$trigger = new Trigger($this);
		$trigger->when($matches);
		return $trigger;
	}

	/**
	 * Return a Trigger that will only pass the provided keys to its callback.
	 */
	public function with(string...$keys):Trigger {
		foreach($keys as $key) {
			if(!$this->parameters->contains($key)) {
				throw new MissingInputParameterException($key);
			}
		}

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

	protected function newTrigger(string $functionName, string...$args):Trigger {
		$trigger = new Trigger($this);
		return $trigger->$functionName(...$args);
	}

	/** @return array<string, string> */
	public function asArray():array {
		return $this->getAll()->asArray();
	}
}
