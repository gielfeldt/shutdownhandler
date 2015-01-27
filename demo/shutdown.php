<?php

namespace Gielfeldt\Ultimate\Example;

require __DIR__ . '/../vendor/autoload.php';

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
$handler = new ShutdownHandler('myshutdownhandler', array('cruel world'));

echo "Hello world\n";

// Register shutdown handler.
$handler2 = new ShutdownHandler('myshutdownhandler', array('for now'));

// Don't wait for shutdown phase, just run now.
$handler2->run();
