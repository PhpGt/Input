<?php
namespace Gt\Input\Test;

use Gt\Input\Input;
use Gt\Input\Test\Helper\Data;
use Gt\Input\Trigger;
use PHPUnit\Framework\TestCase;

class TriggerTest extends TestCase {
	/**
	 * @dataProvider dataInput
	 */
	public function testWhenMatchesInput($input):void {
		$whenCriteria = Data::getRandomWhenCriteria($input, true);
		$trigger = new Trigger($input);
		$trigger->when($whenCriteria);
		self::assertTrue($trigger->fire());
	}

	/**
	 * @dataProvider dataInput
	 */
	public function testWhenNotMatchesInput($input):void {
		$whenCriteria = Data::getRandomWhenCriteria($input, false);
		$trigger = new Trigger($input);
		$trigger->when($whenCriteria);
		self::assertFalse($trigger->fire());
	}

	public function dataInput():array {
		$data = [];

		for($i = 0; $i < 100; $i++) {
			$params = [];

			$getData = Data::getRandomKvp(rand(10, 100), "get-");
			$postData = Data::getRandomKvp(rand(10, 100), "post-");
			$input = new Input($getData, $postData);
			$params []= $input;

			$data []= $params;
		}

		return $data;
	}
}