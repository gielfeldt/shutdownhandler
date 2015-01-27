<?php

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

// Don't run the first shutdown handler anyways.
$handler->unRegister();
