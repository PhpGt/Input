<?php

namespace Gt\Input\Test;

use Gt\Input\InputData;
use PHPUnit\Framework\TestCase;

class InputDataTest extends TestCase {
	public function testSingleSource():void {
		$data = new InputData([
			"name" => "Alice",
			"gender" => "f",
		]);
		self::assertEquals("Alice", $data["name"]);
		self::assertEquals("f", $data["gender"]);
	}

	public function testMultipleSources():void {
		$data = new InputData([
			"name" => "Alice",
			"gender" => "f",
		], [
			"do" => "save",
			"userAccess" => "admin",
		]);

		self::assertEquals("Alice", $data["name"]);
		self::assertEquals("f", $data["gender"]);
		self::assertEquals("save", $data["do"]);
		self::assertEquals("admin", $data["userAccess"]);
	}
}