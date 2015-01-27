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
    static public function shutdown($message = '')
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
