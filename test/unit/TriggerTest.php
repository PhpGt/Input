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

	/**
	 * @dataProvider dataInput
	 */
	public function testWithMultipleKeysSequential(Input $input):void {
		$keys = Helper::getKeysFromInput($input, rand(2, 100));
		$trigger = new Trigger($input);

		foreach($keys as $key) {
			$trigger->with($key);
		}

		$callbackKeys = [];
		$trigger->call(function(InputData $data) use(&$callbackKeys) {
			foreach($data as $key => $value) {
				$callbackKeys []= $key;
			}
		});

		foreach($keys as $key) {
			self::assertContains($key, $callbackKeys);
		}
	}

	/**
	 * @dataProvider dataInput
	 */
	public function testWithMultipleKeysVariableArguments(Input $input):void {
		$keys = Helper::getKeysFromInput($input, rand(2, 100));
		$trigger = new Trigger($input);
		$trigger->with(...$keys);

		$callbackKeys = [];
		$trigger->call(function(InputData $data) use(&$callbackKeys) {
			foreach($data as $key => $value) {
				$callbackKeys []= $key;
			}
		});

		self::assertEquals($keys, $callbackKeys);
	}

	/**
	 * @dataProvider dataInput
	 */
	public function testWithoutSingleKey(Input $input):void {
		$keys = Helper::getKeysFromInput($input, 1);
		$trigger = new Trigger($input);
		$trigger->without($keys[0]);

		$callbackKeys = [];
		$trigger->call(function(InputData $data) use (&$callbackKeys) {
			foreach($data as $key => $value) {
				$callbackKeys []= $key;
			}
		});

		self::assertNotContains($keys[0], $callbackKeys);
	}

	/**
	 * @dataProvider dataInput
	 */
	public function testWithoutMultipleKeysSequential(Input $input):void {
		$keys = Helper::getKeysFromInput($input, rand(2, 100));
		$trigger = new Trigger($input);

		foreach($keys as $key) {
			$trigger->without($key);
		}

		$callbackKeys = [];
		$trigger->call(function(InputData $data) use (&$callbackKeys) {
			foreach($data as $key => $value) {
				$callbackKeys []= $key;
			}
		});

		foreach($keys as $key) {
			self::assertNotContains($key, $callbackKeys);
		}
	}

	/**
	 * @dataProvider dataInput
	 */
	public function testWithoutMultipleKeysVariableArguments(Input $input):void {
		$keys = Helper::getKeysFromInput($input, rand(2, 100));
		$trigger = new Trigger($input);
		$trigger->without(...$keys);

		$callbackKeys = [];
		$trigger->call(function(InputData $data) use (&$callbackKeys) {
			foreach($data as $key => $value) {
				$callbackKeys []= $key;
			}
		});

		foreach($keys as $key) {
			self::assertNotContains($key, $callbackKeys);
		}
	}

	/**
	 * @dataProvider dataInput
	 */
	public function testWithAll(Input $input):void {
		$trigger = new Trigger($input);
		$trigger->withAll();

		$callbackKeys = [];
		$trigger->call(function(InputData $data) use (&$callbackKeys) {
			foreach($data as $key => $value) {
				$callbackKeys []= $key;
			}
		});

		foreach($input->getAll() as $key => $value) {
			self::assertContains($key, $callbackKeys);
		}
	}

	/**
	 * @dataProvider dataInput
	 */
	public function testSetTriggerMatch(Input $input):void {
		$keys = Helper::getKeysFromInput($input, rand(2, 100));
		$trigger = new Trigger($input);

		foreach($keys as $key) {
			$trigger->setTrigger($key, $input[$key]);
		}

		self::assertTrue($trigger->fire());
	}

	/**
	 * @dataProvider dataInput
	 */
	public function testSetTriggerNoMatch(Input $input):void {
		$keys = Helper::getKeysFromInput($input, rand(2, 100));
		$trigger = new Trigger($input);

		foreach($keys as $key) {
			$trigger->setTrigger($key, "NOMATCH");
		}

		self::assertFalse($trigger->fire());
	}

	/**
	 * @dataProvider dataInput
	 */
	public function testSetTriggerSomeMatch(Input $input):void {
		$keys = Helper::getKeysFromInput($input, rand(2, 100));
		$trigger = new Trigger($input);

		foreach($keys as $i => $key) {
			if($i % 2 === 0) {
				$trigger->setTrigger($key, $input[$key]);
			}
			else {
				$trigger->setTrigger($key, "NOMATCH");
			}
		}

		self::assertFalse($trigger->fire());
	}

	/**
	 * @dataProvider dataInput
	 */
	public function testCallWithArgs(Input $input):void {
		$trigger = new Trigger($input);
		$param1 = "one";
		$param2 = "two";
		$param3 = "three";

		$callbackArgs = [];

		$trigger->call(function(InputData $data, $one, $two, $three) use (&$callbackArgs) {
			$callbackArgs["one"] = $one;
			$callbackArgs["two"] = $two;
			$callbackArgs["three"] = $three;
		}, $param1, $param2, $param3);

		self::assertCount(3, $callbackArgs);
		self::assertContains($param1, $callbackArgs);
		self::assertContains($param2, $callbackArgs);
		self::assertContains($param3, $callbackArgs);
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