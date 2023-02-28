<?php

namespace Gt\Input\Test\InputData;

use Gt\Input\InputData\BodyInputData;
use Gt\Input\InputData\Datum\InputDatum;
use Gt\Input\InputData\Datum\MultipleInputDatum;
use Gt\Input\Test\Helper\Helper;
use PHPUnit\Framework\TestCase;

class BodyInputDataTest extends TestCase {
	public function testGet_returnsInputDatumOrMultipleInputDatum():void {
		$sut = new BodyInputData(Helper::getPostPizza());
		self::assertInstanceOf(InputDatum::class, $sut->get("name"));
		self::assertInstanceOf(MultipleInputDatum::class, $sut->get("toppings"));
		self::assertNull($sut->get("nothing"));
	}
}
