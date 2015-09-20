<?php

namespace Gielfeldt\ShutdownHandler\Test;

use Gielfeldt\ShutdownHandler\ShutdownHandler;

/**
 * @covers \Gielfeldt\ShutdownHandler
 */
class ShutdownHandlerTest extends \PHPUnit_Framework_TestCase
{
    public static $testVariable;

    /**
     * Real shutdown handler.
     */
    public static function shutdown()
    {
        self::$testVariable++;
    }

    /**
     * Register and run a shutdown handler.
     */
    public function testRegister()
    {
        self::$testVariable = 0;
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $this->assertTrue($handler instanceof ShutdownHandler);
        $handler->run();
        $this->assertSame(1, self::$testVariable);
    }

    /**
     * Test shutdown function.
     *
     * @depends testRegister
     */
    public function testShutdown()
    {
        self::$testVariable = 0;
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array(), 'testkey');
        ShutdownHandler::shutdown();
        $this->assertSame(1, self::$testVariable);

        self::$testVariable = 0;
        ShutdownHandler::shutdown();
        $this->assertSame(0, self::$testVariable);
    }

    /**
     * Run a set of handlers.
     *
     * @depends testRegister
     * @depends testShutdown
     */
    public function testRunHandlers()
    {
        self::$testVariable = 0;
        $handlers = array();
        $count = 9;
        do {
            $handlers[] = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        } while (--$count);
        ShutdownHandler::runHandlers(array(
            $handlers[0], $handlers[1], $handlers[2], $handlers[3]
        ));
        $this->assertSame(4, self::$testVariable);

        ShutdownHandler::shutdown();
        $this->assertSame(9, self::$testVariable);
    }

    /**
     * Run all shutdown handlers.
     *
     * @depends testRegister
     * @depends testShutdown
     */
    public function testRunAll()
    {
        self::$testVariable = 0;
        $handlers = array();
        $count = 9;
        do {
            $handlers[] = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        } while (--$count);

        ShutdownHandler::runAll();
        $this->assertSame(9, self::$testVariable);

        ShutdownHandler::shutdown();
        $this->assertSame(9, self::$testVariable);
    }

    /**
     * Unregister a shutdown handler.
     *
     * @depends testRegister
     * @depends testShutdown
     */
    public function testUnRegister()
    {
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler->unRegister();

        self::$testVariable = 0;
        ShutdownHandler::shutdown();
        $this->assertSame(0, self::$testVariable);

        $handler->unRegister();

        self::$testVariable = 0;
        ShutdownHandler::shutdown();
        $this->assertSame(0, self::$testVariable);
    }

    public function testIsRegistered()
    {
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $this->assertSame(true, $handler->isRegistered());

        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler->unRegister();
        $this->assertSame(false, $handler->isRegistered());

        self::$testVariable = 0;
        ShutdownHandler::shutdown();
        $this->assertSame(1, self::$testVariable);
    }

    /**
     * Unregister all shutdown handlers.
     *
     * @depends testRegister
     * @depends testShutdown
     */
    public function testUnRegisterAll()
    {
        $handlers = array();
        $count = 9;
        do {
            $handlers[] = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        } while (--$count);
        ShutdownHandler::unRegisterAll();

        self::$testVariable = 0;
        ShutdownHandler::shutdown();
        $this->assertSame(0, self::$testVariable);
    }

    /**
     * Unregister a set of handlers.
     *
     * @depends testShutdown
     */
    public function testUnRegisterHandlers()
    {
        self::$testVariable = 0;
        $handlers = array();
        $count = 9;
        do {
            $handlers[] = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        } while (--$count);
        ShutdownHandler::unRegisterHandlers(array(
            $handlers[0], $handlers[1], $handlers[2], $handlers[3]
        ));

        self::$testVariable = 0;
        ShutdownHandler::shutdown();
        $this->assertSame(5, self::$testVariable);
    }

    /**
     * Test re-registration of shutdown handler.
     *
     * @depends testRegister
     * @depends testUnRegister
     * @depends testShutdown
     */
    public function testReRegister()
    {
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler->unRegister();
        $handler->reRegister();

        self::$testVariable = 0;
        ShutdownHandler::shutdown();
        $this->assertSame(1, self::$testVariable);
    }

    /**
     * Test keyed shutdown handler.
     *
     * @depends testRegister
     * @depends testShutdown
     */
    public function testRegisterKey()
    {
        self::$testVariable = 0;
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array(), 'testkey');
        ShutdownHandler::shutdown();
        $this->assertSame(1, self::$testVariable);

        self::$testVariable = 0;
        $handler1 = new ShutdownHandler(array(get_class($this), 'shutdown'), array(), 'testkey1');
        $handler2 = new ShutdownHandler(array(get_class($this), 'shutdown'), array(), 'testkey1');
        ShutdownHandler::shutdown();
        $this->assertSame(1, self::$testVariable);

        self::$testVariable = 0;
        $handler1 = new ShutdownHandler(array(get_class($this), 'shutdown'), array(), 'testkey1');
        $handler2 = new ShutdownHandler(array(get_class($this), 'shutdown'), array(), 'testkey2');
        ShutdownHandler::shutdown();
        $this->assertSame(2, self::$testVariable);
    }

    /**
     * Test un-registration of keyed shutdown handler.
     *
     * @depends testRegister
     * @depends testUnRegister
     * @depends testShutdown
     */
    public function testUnRegisterKey()
    {
        self::$testVariable = 0;
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array(), 'testkey');
        $handler->unRegister();
        ShutdownHandler::shutdown();
        $this->assertSame(0, self::$testVariable);

        self::$testVariable = 0;
        $handler1 = new ShutdownHandler(array(get_class($this), 'shutdown'), array(), 'testkey1');
        $handler2 = new ShutdownHandler(array(get_class($this), 'shutdown'), array(), 'testkey1');
        $handler1->unRegister();
        ShutdownHandler::shutdown();
        $this->assertSame(1, self::$testVariable);

        self::$testVariable = 0;
        $handler1 = new ShutdownHandler(array(get_class($this), 'shutdown'), array(), 'testkey1');
        $handler2 = new ShutdownHandler(array(get_class($this), 'shutdown'), array(), 'testkey2');
        $handler2->unRegister();
        ShutdownHandler::shutdown();
        $this->assertSame(1, self::$testVariable);
    }

    /**
     * Test re-registration of keyed shutdown handler.
     *
     * @depends testRegister
     * @depends testReRegister
     * @depends testShutdown
     */
    public function testReRegisterKey()
    {
        self::$testVariable = 0;
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array(), 'testkey');
        $handler->reRegister('testkey');
        ShutdownHandler::shutdown();
        $this->assertSame(1, self::$testVariable);

        self::$testVariable = 0;
        $handler1 = new ShutdownHandler(array(get_class($this), 'shutdown'), array(), 'testkey1');
        $handler2 = new ShutdownHandler(array(get_class($this), 'shutdown'), array(), 'testkey2');
        $handler2->reRegister('testkey1');
        ShutdownHandler::shutdown();
        $this->assertSame(1, self::$testVariable);
    }
}
