<?php

namespace Gielfeldt\Ultimate\Example;

require __DIR__ . '/../vendor/autoload.php';

use Gielfeldt\Ultimate\ShutdownHandler;

/**
 * Test class with destructor via Ultimate\ShutdownHandler.
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
    public function __construct($message = '', $key = null)
    {
        // Register our shutdown handler.
        $this->shutdown = new ShutdownHandler(array(get_class($this), 'shutdown'), array($message), $key);
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

// Instantiate a bunch of objects. Objects with the same key will only run the
// destructor when the last object is destroyed.

$objs = array();
$objs[] = new MyClass("world 1", "key1");
$objs[] = new MyClass("world 2", "key1");
$objs[] = new MyClass("world 3", "key1");
$objs[] = new MyClass("world 4", "key1");
$objs[] = new MyClass("world 5", "key2");
$objs[] = new MyClass("world 6", "key2");
$objs[] = new MyClass("world 7", "key2");
$objs[] = new MyClass("world 8", "key2");
