# Shutdown Handler

[![Build Status](https://scrutinizer-ci.com/g/gielfeldt/shutdownhandler/badges/build.png?b=master)][8]
[![Test Coverage](https://codeclimate.com/github/gielfeldt/shutdownhandler/badges/coverage.svg)][3]
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gielfeldt/shutdownhandler/badges/quality-score.png?b=master)][7]
[![Code Climate](https://codeclimate.com/github/gielfeldt/shutdownhandler/badges/gpa.svg)][5]

[![Latest Stable Version](https://poser.pugx.org/gielfeldt/shutdownhandler/v/stable.svg)][1]
[![Latest Unstable Version](https://poser.pugx.org/gielfeldt/shutdownhandler/v/unstable.svg)][1]
[![Dependency Status](https://www.versioneye.com/user/projects/55cb2eb9dfed0a001e000200/badge.svg?style=flat)][11]
[![License](https://poser.pugx.org/gielfeldt/shutdownhandler/license.svg)][4]
[![Total Downloads](https://poser.pugx.org/gielfeldt/shutdownhandler/downloads.svg)][1]

[![Documentation Status](https://readthedocs.org/projects/shutdownhandler/badge/?version=stable)][12]
[![Documentation Status](https://readthedocs.org/projects/shutdownhandler/badge/?version=latest)][12]

## Installation

To install the ShutdownHandler library in your project using Composer, first add the following to your `composer.json`
config file.
```javascript
{
    "require": {
        "gielfeldt/shutdownhandler": "~1.0"
    }
}
```

Then run Composer's install or update commands to complete installation. Please visit the [Composer homepage][6] for
more information about how to use Composer.

### Shutdown handler

This shutdown handler class allows you to create advanced shutdown handlers, that
can be manipulated after being created.

#### Motivation

1. Destructors are not run on fatal errors. In my particular case, I needed a lock class that was robust wrt to cleaning up after itself. See "Example 2" below or examples/fatal.php for an example of this.

2. PHP shutdown handlers cannot be manipulated after registration (unregister, execute, etc.).

#### Example 1 - using Shutdown Handler

```php
namespace Gielfeldt\Example;

require 'vendor/autoload.php';

use Gielfeldt\ShutdownHandler;

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
$handler = new ShutdownHandler('\Gielfeldt\Example\myshutdownhandler', array('cruel world'));

echo "Hello world\n";

// Register shutdown handler.
$handler2 = new ShutdownHandler('\Gielfeldt\Example\myshutdownhandler', array('for now'));

// Don't wait for shutdown phase, just run now.
$handler2->run();
```

#### Example 2 - Ensuring object destruction

```php
namespace Gielfeldt\Example;

require 'vendor/autoload.php';

use Gielfeldt\ShutdownHandler;

/**
 * Test class with destructor via Gielfeldt\ShutdownHandler.
 */
class MyClass
{
    /**
     * Reference to the shutdown handler object.
     * @var ShutdownHandler
     */
    protected $shutdown;

    /**
     * Constructor.
     *
     * @param string $message
     *   Message to display during destruction.
     */
    public function __construct($message = '')
    {
        // Register our shutdown handler.
        $this->shutdown = new ShutdownHandler(array(get_class($this), 'shutdown'), array($message));
    }

    /**
     * Run our shutdown handler upon object destruction.
     */
    public function __destruct()
    {
        $this->shutdown->run();
    }

    /**
     * Our shutdown handler.
     *
     * @param string $message
     *   The message to display.
     */
    public static function shutdown($message = '')
    {
        echo "Destroy $message\n";
    }
}

// Instantiate object.
$obj = new MyClass("world");

// Destroy object. The object's shutdown handler will be run.
unset($obj);

// Instantiate new object.
$obj = new MyClass("universe");

// Object's shutdown handler will be run on object's destruction or when PHP's
// shutdown handlers are executed. Whichever comes first.
```

For more examples see the examples/ folder.

#### Features

* Unregister a shutdown handler
* Run a shutdown handler prematurely
* Improve object destructors by ensuring destruction via PHP shutdown handlers
* Keyed shutdown handlers, allowing to easily deduplicate multiple shutdown handlers

#### Caveats

1. Lots probably.



[1]:  https://packagist.org/packages/gielfeldt/shutdownhandler
[2]:  https://circleci.com/gh/gielfeldt/shutdownhandler
[3]:  https://codeclimate.com/github/gielfeldt/shutdownhandler/coverage
[4]:  https://github.com/gielfeldt/shutdownhandler/blob/master/LICENSE.md
[5]:  https://codeclimate.com/github/gielfeldt/shutdownhandler
[6]:  http://getcomposer.org
[7]:  https://scrutinizer-ci.com/g/gielfeldt/shutdownhandler/?branch=master
[8]:  https://scrutinizer-ci.com/g/gielfeldt/shutdownhandler/build-status/master
[9]:  https://coveralls.io/github/gielfeldt/shutdownhandler
[10]: https://travis-ci.org/gielfeldt/shutdownhandler
[11]: https://www.versioneye.com/user/projects/55cb2eb9dfed0a001e000200
[12]: https://readthedocs.org/projects/shutdownhandler/?badge=latest
