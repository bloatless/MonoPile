# MonoPile

A [Monolog](http://github.com/Seldaek/monolog) handler (and formatter) for Pile.

## Installation

You can install this library using composer:

```
php composer.phar require bloatless/mono-pile
```

## Usage

```php
<?php

// init Monolog
$logger = new \Monolog\Logger('MyLogger');

// init Pile handler and formatter
$handler = new \Bloatless\MonoPile\PileHandler('https://my.pileinstance.com/api/v1/log', 'myapikey');
$formatter = new \Bloatless\MonoPile\PileFormatter('SomeProjectId');
$handler->setFormatter($formatter);

// Push handler into Monolog
$logger->pushHandler($handler);

// Log your errors
$logger->debug('Some debug message...');

```

Of course you can also use the Pile handler in frameworks using Monolog as their internal error logging
solution - like Laravel oder Lumen.

## License

MIT