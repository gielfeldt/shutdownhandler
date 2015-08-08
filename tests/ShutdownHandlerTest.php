<?php

namespace Gielfeldt\Test\ShutdownHandler;

use Gielfeldt\ShutdownHandler;

/**
 * @covers \Gielfeldt\ShutdownHandler
 */
class ShutdownHandlerTest extends \PHPUnit_Framework_TestCase
{
    static public $testVariable;

    static public function shutdown() {
        self::$testVariable++;
    }

    public function testInvalidCallback() {
        self::$testVariable = 0;
        try {
            $callback_name = null;
            do
            {
                $callback = 'invalidfunction' . md5(rand(0, 100000));
            } while (ShutdownHandler::isCallable($callback, false, $callback_name));
            $handler = new ShutdownHandler($callback, array());
            $handler->run();
            $this->assertTrue(false);
        }
        catch (\RuntimeException $e) {
            $this->assertEquals("Callback: '$callback_name' is not callable", $e->getMessage());
        }
    }

    public function testRegister()
    {
        self::$testVariable = 0;
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler->run();
        $this->assertSame(1, self::$testVariable);
    }

    public function testUnRegister()
    {
        self::$testVariable = 0;
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler->unRegister();
        $handler->run();
        $this->assertSame(0, self::$testVariable);
    }

    public function testUnRegisterAll()
    {
        self::$testVariable = 0;
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        ShutdownHandler::unRegisterAll();
        ShutdownHandler::runAll();
        $this->assertSame(0, self::$testVariable);
    }

    public function testRunHandlers()
    {
        self::$testVariable = 0;
        $handler1 = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler2 = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler3 = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler4 = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler5 = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler6 = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler7 = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler8 = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler9 = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        ShutdownHandler::runHandlers(array(
            $handler1, $handler2, $handler3, $handler4
        ));
        ShutdownHandler::unRegisterAll();
        $this->assertSame(4, self::$testVariable);
    }

    public function testUnRegisterHandlers()
    {
        self::$testVariable = 0;
        $handler1 = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler2 = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler3 = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler4 = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler5 = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler6 = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler7 = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler8 = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler9 = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        ShutdownHandler::unRegisterHandlers(array(
            $handler1, $handler2, $handler3, $handler4
        ));
        ShutdownHandler::runAll();
        $this->assertSame(5, self::$testVariable);
    }

    public function testReRegister()
    {
        self::$testVariable = 0;
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array());
        $handler->unRegister();
        $handler->reRegister();
        $handler->run();
        $this->assertSame(1, self::$testVariable);
    }

    public function testRegisterKey()
    {
        self::$testVariable = 0;
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array(), 'testkey');
        $handler->run();
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

    public function testUnRegisterKey()
    {
        self::$testVariable = 0;
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array(), 'testkey');
        $handler->unRegister();
        $handler->run();
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

    public function testReRegisterKey()
    {
        self::$testVariable = 0;
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array(), 'testkey');
        $handler->reRegister('testkey');
        $handler->run();
        $this->assertSame(1, self::$testVariable);

        self::$testVariable = 0;
        $handler1 = new ShutdownHandler(array(get_class($this), 'shutdown'), array(), 'testkey1');
        $handler2 = new ShutdownHandler(array(get_class($this), 'shutdown'), array(), 'testkey2');
        $handler2->reRegister('testkey1');
        ShutdownHandler::shutdown();
        $this->assertSame(1, self::$testVariable);
    }

    public function testShutdownFunction() {
        self::$testVariable = 0;
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array(), 'testkey');
        ShutdownHandler::shutdown();
        $this->assertSame(1, self::$testVariable);

        self::$testVariable = 0;
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array(), 'testkey');
        $handler->run();
        $this->assertSame(1, self::$testVariable);

        self::$testVariable = 0;
        ShutdownHandler::shutdown();
        $this->assertSame(0, self::$testVariable);
    }
}
