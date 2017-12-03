Encapsulated and secured user input.
====================================

By default, PHP stores all user input in global variables, available for reading and _modification_ in any code, including third party libraries.

This library wraps user input in objects that promote encapsulation, allowing functions to be
passed only the user input they require, rather than having read/write access to everything.

User input is automatically secured using openssl ([coming in v2][v2]), preventing unauthorised access to user input from areas of code that shouldn't have it.

***

<a href="https://circleci.com/gh/phpgt/input" target="_blank">
	<img src="https://img.shields.io/circleci/project/PhpGt/Input.svg?style=flat-square" alt="PHP.Gt/Input build status" />
</a>
<a href="https://scrutinizer-ci.com/g/phpgt/input" target="_blank">
	<img src="https://img.shields.io/scrutinizer/g/PhpGt/Input.svg?style=flat-square" alt="PHP.Gt/Input code quality" />
</a>
<a href="https://scrutinizer-ci.com/g/phpgt/input" target="_blank">
	<img src="https://img.shields.io/scrutinizer/coverage/g/PhpGt/Input/master.svg?style=flat-square" alt="PHP.Gt/Input code coverage" />
</a>
<a href="https://packagist.org/packages/phpgt/input" target="_blank">
	<img src="https://img.shields.io/packagist/v/phpgt/input.svg?style=flat-square" alt="PHP.Gt/Input latest release" />
</a>
<a href="http://www.php.gt/input" target="_blank">
	<img src="https://img.shields.io/badge/docs-www.php.gt/input-26a5e3.svg?style=flat-square" alt="PHP.G/Input documentation" />
</a>

Example usage
-------------

```html
<form method="post">
	<h1>Buy this amazing product</h1>
	<label>
		<span>Your name (as it appears on your card)</span>
		<input name="name" placeholder="e.g. Eugene Kaspersky" required />	
	</label>
	
	<label>
		<span>16 digit card number</span>
		<input name="card-number" required data-secure />
	</label>
	
	<label>
		<span>Expiry date</span>
		<input name="card-expiry" pattern="\d\d\w\d\d" placeholder="e.g. 10/24" required data-secure />
	</label>
	
	<label>
		<span>CVV number</span>
		<input name="card-cvv" pattern="\d{3}" placeholder="e.g. 123" required data-secure />
	</label>
	
	<label>
		<span>Shipping address</span>
		<textarea name="address" required data-secure></textarea>
	</label>
	
	<button name="do" value="pay">Make payment!</button>
</form>
```

```php
<?php
// Pass secured input to PaymentProcessor's processCard function:
$payment = new PaymentProcessor();
$input->do("pay")
	->call([$payment, "processCard"]);

// Storing user's shipping data doesn't need to know credit card information:
$input->do("pay")
	->with("name", "address")
	->call("processShipping");

function processShipping(InputData $data) {
	setUsername($data["name"]);
	storeNameAddress($data["name"], $data["address"]);
}
```

Why?
----

This library's primary objective is to provide automatic application security, so only the code that has authorisation to read sensitive user input can do so. 

The encapsulation of user input promotes the benefits of object oriented programming. Rather than having all user input available in global scope, the developer must decide which code receives the user input.

The purpose is to enhance security, and prevent bad coding patterns such as changing the flow of logic depending on whether a particular query string parameter is set.

How?
----

Once an instance of Input is created, all global variables can be unset completely, preventing ad-hoc usage and possible alteration from unknown sources. This is done using `Gt\Input\Globals::unset()`.

Note that the automatic security feature is being released as [version 2][v2] and is not currently available in the library, but will be released when ready with no backwards breaking changes.

The page can be secured by injecting the public key into the page. If your application uses a [DOM document][dom], the forms can be injected for you with `Gt\Input\Security\Injector`, otherwise generate your own using `Gt\Input\Security\Key` to create [secure data fields][secure-data-fields].

A small amount of JavaScript is used to secure user input before being sent by the browser. The JavaScript is within the `src/JavaScript` directory.

If JavaScript fails to execute or is forgotten, PHP execution will halt to prevent unsecured input being available. 

What about `php://input`?
-------------------------

The PHP input stream can be accessed to read raw post data, and this isn't changed by this library. However, when using [secure data fields][secure-data-fields] the user input is encrypted with your application's secret key before it is sent from the user's web browser. The fields are still available to code that reads the input stream, but will be encrypted. 

[v2]: https://github.com/PhpGt/Input/issues?q=is%3Aopen+is%3Aissue+milestone%3Av2
[dom]: https://php.gt/dom
[secure-data-fields]: https://php.gt/input/security