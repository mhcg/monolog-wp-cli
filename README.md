# Monolog WP-CLI Handler

[![Latest Stable Version](https://img.shields.io/packagist/v/mhcg/monolog-wp-cli.svg)](https://packagist.org/packages/mhcg/monolog-wp-cli)
[![Build Status](https://img.shields.io/travis/com/mhcg/monolog-wp-cli.svg)](https://travis-ci.com/mhcg/monolog-wp-cli)
[![Coveralls github](https://img.shields.io/coveralls/github/mhcg/monolog-wp-cli.svg)](https://coveralls.io/github/mhcg/monolog-wp-cli)
[![Code Climate](https://img.shields.io/codeclimate/maintainability/mhcg/monolog-wp-cli.svg)](https://codeclimate.com/github/mhcg/monolog-wp-cli)

Handler for [Monolog](https://github.com/Seldaek/monolog) to support outputting to WP-CLI when running wp command lines.

## Installation
Install the latest version with Composer.

```shell
$ composer require mhcg/monolog-wp-cli
```

## Basic Usage

### Example 1 - Basic Concept

Monolog WP-CLI Handler works the same as any other handler, so create a new WPCLIHandler object and push it to your Logger object.  Of course, this will only work as part of a WP-CLI command within a WordPress theme or plugin.  See Example 2 for a real-world example.

```php
<?php

use Monolog\Logger;
use MHCG\Monolog\Handler;

// create a log channel
$log = new Logger('name');
$log->pushHandler(new Handler\WPCLIHandler(Logger::WARNING));

// output to WP-CLI
$log->warning('This is a warning');
$log->error('An error has occurred');
$log->critical('This will report error and exit out');
$log->debug('Only shown when running wp with --debug');
$log->info('General logging - will not be shown when running wp with --quiet');
```

### Example 2 - Basic WordPress Plugin

The following code will create a a new WP-CLI command of `mycommand` that does some logging.

**Note** this code shows basic usage only and is not a recommended or suggested way to create a new command or plugin.  Check out the [WordPress-Plugin-Boilerplate](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate) GitHub project for a much better way to write plugins.

```shell
composer require mhcg/monolog-wp-cli
```

```php
<?php
/**
 * Plugin Name:     My Plugin
 */

//my-plugin.php

use Monolog\Logger;
use MHCG\Monolog\Handler;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Autoload
$autoload = dirname( __FILE__ ) . '/vendor/autoload.php';
if ( file_exists( $autoload ) ) {
	require_once $autoload;
}

// 'mycommand' WP-CLI command
if ( defined( 'WP_CLI' ) && WP_CLI ) {

	function mycommand_command( $args ) {
		// create logger
		$log = new Logger( 'name' );
		$log->pushHandler( new Handler\WPCLIHandler( Logger::INFO ) );

		// debug -- will only show when wp is run with --debug
		$log->debug( 'Some geeky stuff');

		// the following won't show when wp is run with --quiet
		$log->info( ' Started running' );
		$log->warning( 'Something happened of note' );

		// always shows even with --quiet
		$log->error( 'An error has occurred' );

		// all done - no real equivalent in Logger of WP_CLI::success
		WP_CLI::success( 'Finished running mycommand' );
	}

	WP_CLI::add_command( 'mycommand', 'mycommand_command' );

}

```

```shell
$ wp mycommand
 Started running
Warning: (WARNING) Something happened of note
Error: (ERROR) An error has occurred
Success: Finished running mycommand
```

```shell
$ wp mycommand --quiet
Error: (ERROR) An error has occured
```

## Author
Mark Heydon <[contact@mhcg.co.uk](contact@mhcg.co.uk)> 

## License
Monolog is licensed under the MIT License and therefore so is this handler - see the LICENSE file for details.
