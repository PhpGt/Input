<?php
namespace Gt\Input;

use Psr\Http\Message\StreamInterface;

class Input {
	public function getStream():StreamInterface {
		return new Body();
	}
}