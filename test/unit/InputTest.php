<?php
namespace Gt\Input\Test;

use Gt\Input\Input;
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