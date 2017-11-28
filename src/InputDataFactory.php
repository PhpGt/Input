<?php
namespace Gt\Input;

class InputDataFactory {
	public static function create(Input $input, array $with, array $without):InputData {
		$data = $input->getAll();

		if(empty($with)
		&& empty($without)) {
			return $data;
		}

		if(!empty($without)) {
			$data->remove(...$without);
		}

		if(!empty($with)) {
			$data->removeExcept(...$with);
		}

		return $data;
	}
}