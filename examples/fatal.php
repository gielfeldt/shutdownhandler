<?php

namespace Gielfeldt\ShutdownHandler\Example;

require 'vendor/autoload.php';

use Gielfeldt\ShutdownHandler\ShutdownHandler;

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

class Vanilla
{
    /**
     * The destructor messages.
     * @var string
     */

    protected $message;
    /**
     * Constructor.
     */
    public function __construct($message = '')
    {
        $this->message = $message;
    }
    /**
     * Destructor.
     */
    public function __destruct()
    {
        echo "Destroy $this->message\n";
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

// Plain vanilla object with destructor.
$vanilla = new Vanilla('vanilla');
unset($vanilla);

// Plain vanilla object with destructor.
$vanilla = new Vanilla('vanilla');

// Trigger a fatal error. This will prevent $vanilla's destructor from being
// run. However, $obj's destructor will be run, as it is handled by a shutdown
// handler.
$callback = null;
$callback(232);
