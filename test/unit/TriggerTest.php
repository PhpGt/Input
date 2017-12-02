<?php
namespace Gt\Input\Test;

use Gt\Input\Input;
use Gt\Input\InputData;
use Gt\Input\Test\Helper\Helper;
use Gt\Input\Trigger;
use PHPUnit\Framework\TestCase;

class TriggerTest extends TestCase {
	/**
	 * @dataProvider dataInput
	 */
	public function testWhenMatchesInput(Input $input):void {
		$whenCriteria = Helper::getRandomWhenCriteria($input, true);
		$trigger = new Trigger($input);
		$trigger->when($whenCriteria);
		self::assertTrue($trigger->fire());
	}

	/**
	 * @dataProvider dataInput
	 */
	public function testWhenNotMatchesInput(Input $input):void {
		$whenCriteria = Helper::getRandomWhenCriteria($input, false);
		$trigger = new Trigger($input);
		$trigger->when($whenCriteria);
		self::assertFalse($trigger->fire());
	}

	/**
	 * @dataProvider dataInput
	 */
	public function testWithSingleKey(Input $input):void {
		$keys = Helper::getKeysFromInput($input, 1);
		$trigger = new Trigger($input);
		$trigger->with($keys[0]);

		$callbackKeys = [];
		$trigger->call(function(InputData $data) use(&$callbackKeys) {
			foreach($data as $key => $value) {
				$callbackKeys []= $key;
			}
		});

		self::assertContains($keys[0], $callbackKeys);
		self::assertcount(1, $callbackKeys);
	}

	public function dataInput():array {
		$data = [];

		for($i = 0; $i < 100; $i++) {
			$params = [];

			$getData = Helper::getRandomKvp(rand(10, 100), "get-");
			$postData = Helper::getRandomKvp(rand(10, 100), "post-");
			$input = new Input($getData, $postData);
			$params []= $input;

			$data []= $params;
		}

		return $data;
	}
}