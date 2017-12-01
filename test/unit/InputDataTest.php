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

	public function testAddFromEmpty():void {
		$data = new InputData();
		$data->add("name", "Bob");
		self::assertEquals("Bob", $data["name"]);
	}

	public function testAddMultipleFromEmpty():void {
		$data = new InputData();
		$data->add("name", "Bob");
		$data->add("gender", "m");
		self::assertEquals("Bob", $data["name"]);
		self::assertEquals("m", $data["gender"]);
	}

	public function testAddFromExisting():void {
		$data = new InputData([
			"name" => "Charles",
			"gender" => "m",
		], [
			"do" => "save",
			"userAccess" => "admin",
		]);

		$data->add("view", "tab1");
		$data->add("screenSize", "large");

		self::assertEquals("Charles", $data["name"]);
		self::assertEquals("m", $data["gender"]);
		self::assertEquals("save", $data["do"]);
		self::assertEquals("admin", $data["userAccess"]);
		self::assertEquals("tab1", $data["view"]);
		self::assertEquals("large", $data["screenSize"]);
	}

	public function testRemove():void {
		$data = new InputData([
			"name" => "Debbie",
			"gender" => "f",
		]);

		self::assertEquals("f", $data["gender"]);
		$data->remove("gender");
		self::assertNull($data["gender"]);
	}

	public function testRemoveMany():void {
		$data = new InputData([
			"name" => "Eddie",
			"gender" => "m",
			"userAccess" => "admin",
		]);
		$data->remove("gender", "name");
		self::assertNull($data["name"]);
		self::assertNull($data["gender"]);
		self::assertNotNull($data["userAccess"]);
	}
}