Encapsulated and type-safe user input.
====================================

By default, PHP stores all user input in global arrays (`$_GET`, `$_POST`, and `$_FILES`) available for reading and _modification_ in any code, including third party libraries.

This library wraps user input in objects that promote encapsulation, allowing functions to be
passed only the user input they require, rather than having unmitigated read/write access to everything.

Type-safe functions allow more predictable functionality, such as `$input->getFileUpload("photo")` and `$input->getDateTime("date-of-birth")`.

***

<a href="https://github.com/PhpGt/Input/actions" target="_blank">
	<img src="https://badge.status.php.gt/input-build.svg" alt="PHP.Gt/Input build status" />
</a>
<a href="https://scrutinizer-ci.com/g/phpgt/input" target="_blank">
	<img src="https://badge.status.php.gt/input-quality.svg" alt="PHP.Gt/Input code quality" />
</a>
<a href="https://scrutinizer-ci.com/g/phpgt/input" target="_blank">
	<img src="https://badge.status.php.gt/input-coverage.svg" alt="PHP.Gt/Input code coverage" />
</a>
<a href="https://packagist.org/packages/phpgt/input" target="_blank">
	<img src="https://badge.status.php.gt/input-version.svg" alt="PHP.Gt/Input latest release" />
</a>
<a href="http://www.php.gt/input" target="_blank">
	<img src="https://badge.status.php.gt/input-docs.svg" alt="PHP.G/Input documentation" />
</a>

Example usage
-------------

```html
<form method="post">
	<h1>User Profile</h1>
	<label>
		<span>Your name</span>
		<input name="name" placeholder="e.g. Eugene Kaspersky" required />	
	</label>
	
	<label>
		<span>Age</span>
		<input name="age" type="number" required />
	</label>
	
	<label>
		<span>Photo</span>
		<input name="photo" type="file" />
	</label>
	
	<button name="do" value="save">Save profile</button>
</form>
```

```php
<?php
$profile->update(
	$profileId,
// Use type-safe getters to help write maintainable code.
	$input->getString("name"),
	$input->getInt("age"),
);

// Handle file uploads with a FileUpload object.
$photoUpload = $input->getFile("photo");
if($photoUpload instanceof FailedFileUpload) {
	// Handle a failed upload here.
}

$photoUpload->moveTo("data/upload/$profileId.jpg");
```

Features at a glance
--------------------

+ Type-safe getters, implementing the [TypeSafeGetter][tsg] interface.
+ Typed `multiple` getters, for working with checkboxes, multi-select elements or multiple file uploads.
+ "do" callback functions - hook up callbacks to button presses (implemented automatically in WebEngine applications).
+ "when" triggers - execute callbacks when certain user input is present.
+ `FileUploadInputData` class for easy file uploads, including functions such as `moveTo()`, `getOriginalName()`, etc.

[tsg]: https://php.gt/typesafegetter
