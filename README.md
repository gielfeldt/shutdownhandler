# Ultimate library

## Installation

To install the Ultimate library in your project using Composer, first add the following to your `composer.json`
config file.
```javascript
{
    "require": {
        "gielfeldt/ultimate": "~1.0"
    }
}
```

Then run Composer's install or update commands to complete installation. Please visit the [Composer homepage][7] for
more information about how to use Composer.

### Shutdown handler

This shutdown handler class allows you to create advanced shutdown handlers, that
can be manipulated after being created.

#### Example

```php
require __DIR__ . '/../vendor/autoload.php';

use Gielfeldt\Ultimate\ShutdownHandler;

/**
 * Simple shutdown handler callback.
 *
 * @param string $message
 *   Message to display during shutdown.
 */
function myshutdownhandler($message = '') {
  echo "Goodbye $message\n";
}

// Register shutdown handler to be run during PHP shutdown phase.
$handler = new ShutdownHandler('myshutdownhandler', array('cruel world'));

echo "Hello world\n";

// Register shutdown handler.
$handler2 = new ShutdownHandler('myshutdownhandler', array('for now'));

// Don't wait for shutdown phase, just run now.
$handler2->run();
```

#### Features

* Unregister a shutdown handler
* Run a shutdown handler prematurely
* Improve object destructors by ensuring destruction via PHP shutdown handlers
* Keyed shutdown handlers, allowing to easily deduplicate multiple shutdown handlers

#### Caveats

1. Lots probably.

