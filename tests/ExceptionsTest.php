<?php

namespace Gielfeldt\ShutdownHandler\Test;

use Gielfeldt\ShutdownHandler\ShutdownHandler;

/**
 * @covers \Gielfeldt\ShutdownHandler
 */
class ExceptionsTest extends \PHPUnit_Framework_TestCase
{

    public function testInvalidCallback()
    {
        try {
            $callback_name = null;
            do {
                $callback = 'invalidfunction' . md5(rand(0, 100000));
            } while (ShutdownHandler::isCallable($callback, false, $callback_name));
            $handler = new ShutdownHandler($callback, array());
            $handler->run();
            $this->assertTrue(false);
        } catch (\RuntimeException $e) {
            $this->assertEquals("Callback: '$callback_name' is not callable", $e->getMessage());
        }
    }
}
