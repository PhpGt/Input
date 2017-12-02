<?php
namespace Gt\Input\Test;

use Gt\Input\Input;
use Gt\Input\InputData;
use Gt\Input\InvalidInputMethodException;
use Gt\Input\Test\Helper\Helper;
use Gt\Input\Trigger;
use PHPUnit\Framework\TestCase;
use StdClass;

class InputTest extends TestCase {
	public function testBodyStreamContents():void {
		$testMessage = "This is a test message";
		$tmpPath = "/tmp/phpgt/input/test/" . uniqid();
		mkdir(dirname($tmpPath), 0775, true);
		touch($tmpPath);
		$fh = fopen($tmpPath, "r+");
		fwrite($fh, $testMessage);

		$input = new Input([],[],[],$tmpPath);
		$body = $input->getStream();
		self::assertEquals($testMessage, (string)$body);
	}

	/**
	 * @dataProvider dataRandomGetPost
	 */
	public function testGetQueryString(array $get, array $post):void {
		$input = new Input($get, $post);

		for($i = 0; $i < 100; $i++) {
			$key = array_rand($get);

			$value = $input->get(
				$key,
				Input::DATA_QUERYSTRING
			);

			self::assertEquals($get[$key], $value);
			self::assertFalse(isset($post[$key]));
		}
	}

	/**
	 * @dataProvider dataRandomGetPost
	 */
	public function testGetPostField(array $get, array $post):void {
		$input = new Input($get, $post);

		for($i = 0; $i < 100; $i++) {
			$key = array_rand($post);

			$value = $input->get(
				$key,
				Input::DATA_POSTFIELDS
			);

			self::assertEquals($post[$key], $value);
			self::assertFalse(isset($get[$key]));
		}
	}

	/**
	 * @dataProvider dataRandomGetPost
	 */
	public function testGetInvalidDataType(array $get, array $post):void {
		self::expectException(InvalidInputMethodException::class);
		$input = new Input($get, $post);
		$input->get("test", "WRONG_TYPE");
	}

	/**
	 * @dataProvider dataRandomGetPost
	 */
	public function testGetAllInvalidDataType(array $get, array $post):void {
		self::expectException(InvalidInputMethodException::class);
		$input = new Input($get, $post);
		$input->getAll("WRONG_TYPE");
	}

	/**
	 * @dataProvider dataRandomGetPost
	 */
	public function testGetAllQueryString(array $get, array $post):void {
		$input = new Input($get, $post);
		$queryString = $input->getAll(Input::DATA_QUERYSTRING);

		foreach($post as $key => $value) {
			self::assertFalse(isset($queryString[$key]));
		}

		foreach($get as $key => $value) {
			self::assertTrue(isset($queryString[$key]));
		}
	}

	/**
	 * @dataProvider dataRandomGetPost
	 */
	public function testGetAllPostFields(array $get, array $post):void {
		$input = new Input($get, $post);
		$postFields = $input->getAll(Input::DATA_POSTFIELDS);

		foreach($get as $key => $value) {
			self::assertFalse(isset($postFields[$key]));
		}

		foreach($post as $key => $value) {
			self::assertTrue(isset($postFields[$key]));
		}
	}

	/**
	 * @dataProvider dataRandomGetPost
	 */
	public function testGetAll(array $get, array $post):void {
		$input = new Input($get, $post);
		$combined = $input->getAll();

		foreach($get as $key => $value) {
			self::assertTrue(isset($combined[$key]));
		}
		foreach($post as $key => $value) {
			self::assertTrue(isset($combined[$key]));
		}

		self::assertFalse(isset($combined->thisDoesNotExist));
	}

	/**
	 * @dataProvider dataRandomGetPost
	 */
	public function testGetAllMethods(array $get, array $post):void {
		$input = new Input($get, $post);
		$getVariables = $input->getAll(Input::DATA_QUERYSTRING);
		$postVariables = $input->getAll(Input::DATA_POSTFIELDS);

		foreach($get as $key => $value) {
			self::assertTrue(isset($getVariables[$key]));
			self::assertFalse(isset($postVariables[$key]));
		}
	}

	/**
	 * @dataProvider dataRandomString
	 */
	public function testDo(string $doName):void {
		$input = new Input(["do" => $doName]);
		$trigger = $input->do($doName);
		$this->assertInstanceOf(Trigger::class, $trigger);
		self::assertTrue($trigger->fire());
	}

	/**
	 * @dataProvider dataRandomString
	 */
	public function testNotDo(string $doName):void {
		$input = new Input(["do" => "submit"]);
		$trigger = $input->do($doName);
		self::assertFalse($trigger->fire());
	}

	/**
	 * @dataProvider dataRandomString
	 */
	public function testWhen(string $whenName):void {
		$whenValue = uniqid("whenValue");

		$input = new Input([
			uniqid("key") => uniqid("value"),
			$whenName => $whenValue,
		]);
		$trigger = $input->when([
			$whenName => $whenValue,
		]);

		self::assertTrue($trigger->fire());
	}

	/**
	 * @dataProvider dataRandomString
	 */
	public function testNotWhen(string $whenName):void {
		$whenValue = uniqid("whenValue");

		$input = new Input([
			uniqid("key") => uniqid("value"),
		]);
		$trigger = $input->when([
			$whenName => $whenValue,
		]);

		self::assertFalse($trigger->fire());
	}

	/**
	 * @dataProvider dataRandomGetPost
	 */
	public function testWithExist(array $get, array $post):void {
		$withKeys = [];

		for($i = 0; $i < 10; $i++) {
			if(rand(0, 1)) {
				$withKeys []= array_rand($get);
			}
			else {
				$withKeys []= array_rand($post);
			}
		}

		$input = new Input($get, $post);
		$trigger = $input->with(...$withKeys);
		$keysCalled = [];

		$trigger->call(function(InputData $data) use(&$keysCalled) {
			foreach($data as $key => $value) {
				$keysCalled []= $key;
			}
		});

		foreach($withKeys as $key) {
			self::assertContains($key, $keysCalled);
		}
	}

	/**
	 * @dataProvider dataRandomGetPost
	 */
	public function testWithAll(array $get, array $post):void {
		$input = new Input($get, $post);
		$trigger = $input->withAll();
		$keysCalled = [];

		$trigger->call(function(InputData $data) use(&$keysCalled) {
			foreach($data as $key => $value) {
				$keysCalled []= $key;
			}
		});

		foreach(array_merge($get, $post) as $key => $value) {
			self::assertContains($key, $keysCalled);
		}
	}

	/**
	 * @dataProvider dataRandomGetPost
	 */
	public function testWithout(array $get, array $post):void {
		$withoutKeys = [];

		for($i = 0; $i < 10; $i++) {
			if(rand(0, 1)) {
				$withoutKeys []= array_rand($get);
			}
			else {
				$withoutKeys []= array_rand($post);
			}
		}

		$input = new Input($get, $post);
		$trigger = $input->without(...$withoutKeys);
		$keysCalled = [];

		$trigger->call(function(InputData $data) use(&$keysCalled) {
			foreach($data as $key => $value) {
				$keysCalled []= $key;
			}
		});

		foreach(array_merge($get, $post) as $key => $value) {
			if(in_array($key, $withoutKeys)) {
				continue;
			}

			self::assertContains($key, $keysCalled);
		}
		foreach($withoutKeys as $key) {
			self::assertNotContains($key, $keysCalled);
		}
	}

	/**
	 * @dataProvider dataRandomGetPost
	 */
	public function testSettingOwnData(array $get, array $post):void {
		$input = new Input($get, $post);
		$originalInputCount = count($input);

		$input["added-from-test"] = uniqid();

		self::assertEquals($originalInputCount + 1, count($input));
	}

	public function dataRandomGetPost():array {
		$data = [];

		for($i = 0; $i < 100; $i++) {
			$params = [
				Helper::getRandomKvp(rand(10, 100), "get-"),
				Helper::getRandomKvp(rand(10, 100), "post-"),
			];
			$data []= $params;
		}

		return $data;
	}

	public function dataRandomString():array {
		$data = [];

		for($i = 0; $i < 100; $i++) {
			$params = [
				uniqid(),
			];
			$data []= $params;
		}

		return $data;
	}

	public function callbackWith(...$args) {

	}
}