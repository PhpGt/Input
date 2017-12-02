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
	const DATA_POSTFIELDS = "post";
	const DATA_COMBINED = "both";

	/** @var Body */
	protected $body;

	/** @var InputData */
	protected $queryStringParameters;
	/** @var InputData */
	protected $postFields;
	/** @var Upload */
	protected $files;

	public function __construct(
	array $get = [],
	array $post = [],
	array $files = [],
	string $bodyPath = "php://input"
	) {
		$this->body = new Body($bodyPath);
		$this->queryStringParameters = new InputData($get);
		$this->postFields = new InputData($post);
		$this->files = new Upload($files);
		$this->data = new InputData($get, $post);
		$this->dataKeys = $this->data->getKeys();
	}

	/**
	 * Returns the input payload as a streamable HTTP request body.
	 */
	public function getStream():StreamInterface {
		return $this->body;
	}

	/**
	 * Get a particular input value by its key. To specify either GET or POST variables, pass
	 * Input::METHOD_GET or Input::METHOD_POST as the second parameter (defaults to
	 * Input::METHOD_BOTH).
	 */
	public function get(string $key, string $method = null):?string {
		if(is_null($method)) {
			$method = self::DATA_COMBINED;
		}

		switch($method) {
		case self::DATA_QUERYSTRING:
			$variable = $this->queryStringParameters;
			break;
		case self::DATA_POSTFIELDS:
			$variable = $this->postFields;
			break;
		case self::DATA_COMBINED:
			$variable = $this->data;
			break;
		default:
			throw new InvalidInputMethodException($method);
		}

		return $variable[$key];
	}

	/**
	 * Does the input contain the specified key?
	 */
	public function has(string $key):bool {
		return isset($this->data[$key]);
	}

	/**
	 * Get an InputData object containing all request variables. To specify only GET or POST
	 * variables, pass Input::METHOD_GET or Input::METHOD_POST.
	 */
	public function getAll(string $method = null):InputData {
		if(is_null($method)) {
			$method = self::DATA_COMBINED;
		}

		switch($method) {
		case self::DATA_QUERYSTRING:
			return $this->queryStringParameters;
		case self::DATA_POSTFIELDS:
			return $this->postFields;
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