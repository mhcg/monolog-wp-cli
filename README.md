# Monolog WP-CLI Handler

[![Build Status](https://img.shields.io/travis/com/mhcg/monolog-wp-cli.svg)](https://travis-ci.com/mhcg/monolog-wp-cli)
[![Latest Stable Version](https://img.shields.io/packagist/v/mhcg/monolog-wp-cli.svg)](https://packagist.org/packages/mhcg/monolog-wp-cli)

Handler for [Monolog](https://github.com/Seldaek/monolog) to support outputting to WP-CLI when running wp command lines.

## Installation
Install the latest version with Composer.

`$ composer require mhcg/monolog-wp-cli`

## Basic Usage

```php
<?php

use Monolog\Logger;
use MHCG\Monolog\Handler;

// create a log channel
$log = new Logger( 'name' );
$log->pushHandler( new WPCLIHandler( Logger::WARNING ) );

// output to WP-CLI
$log->warning( 'This is a warning' );
$log->error( 'An error has occurred' );
$log->critical( 'This will report error and exit out' );
```

## Author
Mark Heydon <[contact@mhcg.co.uk](contact@mhcg.co.uk)> 

## License
Monolog is licensed under the MIT License and therefore so is this handler - see the LICENSE file for details.


