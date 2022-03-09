<?php
/**
 * This example should be served and accessed within a web browser.
 * To serve using PHP, open up the example directory in a terminal and run:
 * php -S 0.0.0.0:8080 and then visit http://localhost:8080/02-multiple-
 */
use Gt\Input\Input;

require __DIR__ . "/../../vendor/autoload.php";

if(empty($_POST)) {
	goto website;
}

ini_set("display_errors", true);
$input = new Input($_GET, $_POST, $_FILES);

echo "<pre>";
echo "Your name is: ", $input->getString("name"), PHP_EOL;

$colourString = implode(", ", $input->getMultipleString("colour"));
echo "Colours chosen: ", $colourString, PHP_EOL;

echo "Files uploaded: ", PHP_EOL;
foreach($input->getMultipleFile("upload") as $fileName => $upload) {
	echo "$fileName is size: ", $upload->getSize(), PHP_EOL;
}

website:?>
<!doctype html>
<form method="post" enctype="multipart/form-data">
	<label>
		<span>Your name:</span>
		<input name="name" />
	</label><br>
	<label>
		<input type="checkbox" name="colour[]" value="red" /> Red
	</label><br>
	<label>
		<input type="checkbox" name="colour[]" value="green" /> Green
	</label><br>
	<label>
		<input type="checkbox" name="colour[]" value="blue" /> Blue
	</label><br>
	<input type="file" multiple name="upload[]" />
	<button name="do" value="upload">Upload!</button>
</form>
