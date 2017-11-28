<?php
namespace Gt\Input\Test;

use Gt\Input\Input;
use PHPUnit\Framework\TestCase;

class InputTest extends TestCase {
	public function testConstructs():void {
		$input = new Input([],[],[]);
		self::assertInstanceOf(Input::class, $input);
	}
}