Encapsulated user input.
========================

By default, PHP passes all sensitive user information around in global variables, available for 
reading and modification in any code, including third party libraries.

This library wraps user input in objects that promote encapsulation, allowing functions to be
passed only the user input they require, rather than having read/write access to everything.

***

<a href="https://circleci.com/gh/phpgt/input" target="_blank">
	<img src="https://img.shields.io/circleci/project/PhpGt/Input.svg?style=for-the-badge" alt="PHP.Gt/Input build status" />
</a>
<a href="https://scrutinizer-ci.com/g/phpgt/input" target="_blank">
	<img src="https://img.shields.io/scrutinizer/g/phpgt/input.svg?style=for-the-badge" alt="PHP.Gt/Input code quality" />
</a>
<a href="https://coveralls.io/r/phpgt/input" target="_blank">
	<img src="https://img.shields.io/scrutinizer/coverage/g/PhpGt/Input/master.svg?style=for-the-badge" alt="PHP.Gt/Input code coverage" />
</a>
<a href="https://packagist.org/packages/phpgt/input" target="_blank">
	<img src="https://img.shields.io/packagist/v/phpgt/input.svg?style=for-the-badge" alt="PHP.Gt/Input latest release" />
</a>
<a href="http://www.php.gt/input" target="_blank">
	<img src="https://img.shields.io/badge/docs-www.php.gt/input-26a5e3.svg?style=for-the-badge" alt="PHP.G/Input documentation" />
</a>

Example usage
-------------

// TODO.