<?php
namespace Gt\Input\Test\Helper;

use Gt\Input\Input;
use Gt\Input\InputData;

class Data {
	public static function getRandomKvp(int $num, string $prefix = ""):array {
		$kvp = [];

		for($i = 0; $i < $num; $i++) {
			$key = "key-$i-$prefix" . uniqid();
			$value = "value-$i-$prefix" . uniqid();
			$kvp[$key] = $value;
		}

		return $kvp;
	}

	public static function getRandomWhenCriteria(Input $input, bool $inInput = true):array {
		if($inInput) {
			$inputData = $input->getAll();
			$inputDataArray = self::convertInputDataToArray($inputData);
			$numberToCreate = rand(1, count($inputDataArray));
			$keys = array_rand($inputDataArray, $numberToCreate);
		}
		else {
			$inputDataArray = [];
			$numberToCreate = rand(1, 100);
			for($i = 0; $i < $numberToCreate; $i++) {
				$inputDataArray[
					"key-$i-madeup" . uniqid()
				] = "value-$i-madeup" . uniqid();
			}
			$keys = array_rand($inputDataArray, $numberToCreate);
		}

		if(!is_array($keys)) {
			$keys = [$keys];
		}

		$criteria = [];
		foreach($keys as $k) {
			$criteria[$k] = $inputDataArray[$k];
		}

		return $criteria;
	}

	public static function convertInputDataToArray(InputData $inputData):array {
		$array = [];

		foreach($inputData as $key => $value) {
			$array[$key] = $value;
		}

		return $array;
	}
}