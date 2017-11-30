<?php
namespace Gt\Input\Test;

use Gt\Input\Input;
use Gt\Input\Trigger;
use PHPUnit\Framework\TestCase;

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
	public function testGetAll(array $get, array $post):void {
		$input = new Input($get, $post);
		$combined = $input->getAll();

		foreach($get as $key => $value) {
			self::assertTrue(isset($combined->$key));
		}
		foreach($post as $key => $value) {
			self::assertTrue(isset($combined->$key));
		}

		self::assertFalse(isset($combined->thisDoesNotExist));
	}

	/**
	 * @dataProvider dataRandomGetPost
	 */
	public function testGetAllMethods(array $get, array $post):void {
		$input = new Input($get, $post);
		$getVariables = $input->getAll(Input::METHOD_GET);
		$postVariables = $input->getAll(Input::METHOD_POST);

		foreach($get as $key => $value) {
			self::assertTrue(isset($getVariables->$key));
			self::assertFalse(isset($postVariables->$key));
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

	public function dataRandomGetPost():array {
		$data = [];

		for($i = 0; $i < 100; $i++) {
			$params = [
				$this->getRandomKvp(rand(10, 100)),
				$this->getRandomKvp(rand(10, 100))
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

	private function getRandomKvp(int $num):array {
		$kvp = [];

		for($i = 0; $i < $num; $i++) {
			$key = uniqid() . "key";
			$value = uniqid() . "value";
			$kvp[$key] = $value;
		}

		return $kvp;
	}
}