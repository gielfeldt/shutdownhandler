# Ultimate library

[![Build Status](https://travis-ci.org/gielfeldt/ultimate.svg?branch=master)][2]
[![Build Status](https://scrutinizer-ci.com/g/gielfeldt/ultimate/badges/build.png?b=master)](https://scrutinizer-ci.com/g/gielfeldt/ultimate/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gielfeldt/ultimate/badges/quality-score.png?b=master)][3]
[![Code Coverage](https://scrutinizer-ci.com/g/gielfeldt/ultimate/badges/coverage.png?b=master)][3]

[![Latest Stable Version](https://poser.pugx.org/gielfeldt/ultimate/v/stable.svg)][1]
[![Latest Unstable Version](https://poser.pugx.org/gielfeldt/ultimate/v/unstable.svg)][1]
[![License](https://poser.pugx.org/gielfeldt/ultimate/license.svg)][4]

![Total Downloads](https://poser.pugx.org/gielfeldt/ultimate/downloads.svg)
![Monthly Downloads](https://poser.pugx.org/gielfeldt/ultimate/d/monthly.png)
![Daily Downloads](https://poser.pugx.org/gielfeldt/ultimate/daily.svg)

## Installation

To install the Ultimate library in your project using Composer, first add the following to your `composer.json`
config file.
```javascript
{
    "require": {
        "gielfeldt/ultimate": "1.0.*@alpha"
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
namespace Gielfeldt\Ultimate\Example;

require 'vendor/autoload.php';

use Gielfeldt\Ultimate\ShutdownHandler;

/**
 * Simple shutdown handler callback.
 *
 * @param string $message
 *   Message to display during shutdown.
 */
function myshutdownhandler($message = '')
{
    echo "Goodbye $message\n";
}

// Register shutdown handler to be run during PHP shutdown phase.
$handler = new ShutdownHandler('\Gielfeldt\Ultimate\Example\myshutdownhandler', array('cruel world'));

echo "Hello world\n";

// Register shutdown handler.
$handler2 = new ShutdownHandler('\Gielfeldt\Ultimate\Example\myshutdownhandler', array('for now'));

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



[1]:  https://packagist.org/packages/gielfeldt/ultimate
[2]:  https://travis-ci.org/gielfeldt/ultimate
[3]:  https://scrutinizer-ci.com/g/gielfeldt/ultimate/?branch=master
[4]:  https://github.com/gielfeldt/ultimate/blob/master/LICENSE.md
