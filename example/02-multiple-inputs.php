<?php
/**
 * This example shows how to work with a multiple file upload. For this to work,
 * files must be uploaded through a form with enctype="multipart/formdata" using
 * a file input type. The file input must be named with square brackets,
 * indicating that multiple values can be present.
 *
 * The value of $_FILES in this script is hard-coded to what PHP will be
 * provided when the user enters three images into a form such as the one shown
 * below.
 *
 * Example form:
 * <!doctype html>
 * <form method="post" enctype="multipart/form-data">
 *   <label>
 *     <span>Your name:</span>
 *     <input name="name" />
 *   </label> <br />
 *   <label>
 *     <input type="checkbox" name="colour[]" value="red" /> Red
 *     </label><br />
 *   <label>
 *     <input type="checkbox" name="colour[]" value="green" /> Green
 *   </label><br />
 *   <label>
 *     <input type="checkbox" name="colour[]" value="blue" /> Blue
 *   </label><br />
 *   <input type="file" multiple name="upload[]" />
 *   <button name="do" value="upload">Upload!</button>
 * </form>
 */
use Gt\Input\Input;
use Gt\Input\InputData\Datum\FailedFileUpload;

require(__DIR__ . "/../vendor/autoload.php");

$_GET = [];
$_POST = [
	"do" => "upload",
	"name" => "Greg",
	"colour" => [
		"red",
		"blue",
	],
];
$_FILES = [
	"upload" => [
		"name" => [
			"front.jpg",
			"back.jpg",
			"description.txt",
		],
		"full_path" => [
			"front.jpg",
			"back.jpg",
			"description.txt",
		],
		"type" => [
			"image/jpeg",
			"image/jpeg",
			"text/plain",
		],
		"tmp_name" => [
			"/tmp/phpkLgfwE",
			"/tmp/phpiZKQf6",
			"/tmp/php9UtO5A",
		],
		"error" => [
			0,
			0,
			0,
		],
		"size" => [
			123891,
			165103,
			915,
		],
	]
];

$input = new Input($_GET, $_POST, $_FILES);

echo "Your name: " . $input->getString("name"), PHP_EOL;

if(!$input->contains("colour")) {
	echo "No colours chosen...", PHP_EOL;
	exit;
}
foreach($input->getMultipleString("colour") as $colour) {
	echo "Colour chosen: $colour", PHP_EOL;
}

if(!$input->contains("upload")) {
	echo "Nothing uploaded...", PHP_EOL;
	exit;
}

foreach($input->getMultipleFile("upload") as $fileName => $upload) {
	if($upload instanceof FailedFileUpload) {
		echo "Error uploading $fileName!", PHP_EOL;
		continue;
	}

	$newPath = "data/upload/$fileName";
	$size = $upload->getSize();
	echo "Uploaded to $newPath (size $size)", PHP_EOL;
}
