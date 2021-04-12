<?php
/**
 * This example shows how a hypothetical application can encapsulate user input
 * so that only the payment processor can access the sensitive credit card
 * information.
 *
 * Two functions are defined, processPayment and processShipping.
 * The processPayment function needs to receive the sensitive data in the
 * user input data, but processShipping shouldn't be able to view the credit
 * card information.
 */

use Gt\Input\Input;
use Gt\Input\InputData\InputData;

require(__DIR__ . "/../vendor/autoload.php");

$_GET = [];
// Fake a form submission by setting the _POST superglobal here.
$_POST = [
	"name" => "Eugene Kaspersky",
	"card-number" => "4111011101110111",
	"card-expiry" => "23/11",
	"card-cvv" => "359",
	"address" => "14 Brooke Street",
	"do" => "pay",
];
$_FILES = [];

$input = new Input($_GET, $_POST, $_FILES);

$input->do("pay")
	->call("processCard");

$input->do("pay")
	->with("name", "address")
	->call("processShipping");

function processCard(InputData $data) {
	echo "Executing processCard...", PHP_EOL;
	foreach($data as $key => $value) {
		echo "processCard InputData: $key = $value", PHP_EOL;
	}
	echo PHP_EOL;
}

function processShipping(InputData $data) {
	echo "Executing processShipping...", PHP_EOL;
	echo "Name: ", $data->getString("name"), PHP_EOL;
	echo "Address: ", $data->getString("address"), PHP_EOL;
	echo "Credit card: ", $data->getString("card-number") ?? "DATA NOT PASSED!", PHP_EOL;
}
