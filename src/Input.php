<?php
namespace Gt\Input;

use Psr\Http\Message\StreamInterface;

class Input {
	/** @var Body */
	protected $body;

	public function __construct() {
		$this->body = new Body("php://input");
	}

	public function getStream():StreamInterface {
		return $this->body;
	}
}