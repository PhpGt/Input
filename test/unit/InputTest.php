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
	public function testGetCombined(array $_GET, array $_POST):void {

	}

	public function dataRandomGetPost():array {
		$data = [];

		for($i = 0; $i < 100; $i++) {
			$params = [$this->getRandomKvp(), $this->getRandomKvp()];
			$data []= $params;
		}

		return $data;
	}

	private function getRandomKvp():array {
		$kvp = [];



		return $kvp;
	}
}