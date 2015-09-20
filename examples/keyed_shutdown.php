<?php

namespace Gielfeldt\ShutdownHandler\Example;

require 'vendor/autoload.php';

use Gielfeldt\ShutdownHandler\ShutdownHandler;

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
$handlers = array();
$handlers[] = new ShutdownHandler('\Gielfeldt\ShutdownHandler\Example\myshutdownhandler', array('test0'));
$handlers[] = new ShutdownHandler('\Gielfeldt\ShutdownHandler\Example\myshutdownhandler', array('test1'), 'key1');
$handlers[] = new ShutdownHandler('\Gielfeldt\ShutdownHandler\Example\myshutdownhandler', array('test2'), 'key1');
$handlers[] = new ShutdownHandler('\Gielfeldt\ShutdownHandler\Example\myshutdownhandler', array('test3'), 'key1');
$handlers[] = new ShutdownHandler('\Gielfeldt\ShutdownHandler\Example\myshutdownhandler', array('test4'), 'key1');

echo "Hello world\n";

// This has no effect.
unset($handlers[0], $handlers[1], $handlers[2], $handlers[3]);

// This has no effect.
unset($handlers[3], $handlers[2], $handlers[1], $handlers[0]);

echo "All local objects unset\n";

// 'test0' and 'test4' will now be run.
