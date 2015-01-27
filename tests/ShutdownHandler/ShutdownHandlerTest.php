<?php

namespace Test\ShutdownHandler;

use Gielfeldt\Ultimate\ShutdownHandler;

/**
 * @covers \Gielfeldt\Ultimate\ShutdownHandler
 */
class ShutdownHandlerTest extends \PHPUnit_Framework_TestCase
{
    static public $testVariable;

    static public function shutdown($value = NULL) {
        self::$testVariable = $value;
    }

    public function testInvalidCallback() {
        self::$testVariable = FALSE;
        try {
            $handler = new ShutdownHandler('invalidfunction', array(TRUE));
            $handler->run();
            $this->assertFalse(self::$testVariable);
        }
        catch (\RuntimeException $e) {
            #$this->assertFalse(FALSE);
            $this->assertEquals($e->getMessage(), "Callback: 'invalidfunction' is not callable");
        }
    }

    public function testRegister()
    {
        self::$testVariable = FALSE;
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array(TRUE));
        $handler->run();
        $this->assertTrue(self::$testVariable);
    }

    public function testUnRegister()
    {
        self::$testVariable = FALSE;
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array(TRUE));
        $handler->unRegister();
        $handler->run();
        $this->assertFalse(FALSE, self::$testVariable);
    }

    public function testReRegister()
    {
        self::$testVariable = FALSE;
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array(TRUE));
        $handler->unRegister();
        $handler->reRegister();
        $handler->run();
        $this->assertTrue(TRUE, self::$testVariable);
    }

    public function testRegisterKey()
    {
        self::$testVariable = FALSE;
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array(TRUE), 'testkey');
        $handler->run();
        $this->assertTrue(self::$testVariable);
    }

    public function testUnRegisterKey()
    {
        self::$testVariable = FALSE;
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array(TRUE), 'testkey');
        $handler->unRegister();
        $handler->run();
        $this->assertFalse(FALSE, self::$testVariable);
    }

    public function testReRegisterKey()
    {
        self::$testVariable = FALSE;
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array(TRUE), 'testkey');
        $handler->unRegister();
        $handler->reRegister();
        $handler->run();
        $this->assertTrue(TRUE, self::$testVariable);
    }

    public function testShutdownFunction() {
        self::$testVariable = FALSE;
        $handler = new ShutdownHandler(array(get_class($this), 'shutdown'), array(TRUE), 'testkey');
        ShutdownHandler::shutdown();
        $this->assertTrue(TRUE, self::$testVariable);
    }

    public function testCallbackName() {
        $callback = array($this, 'shutdown');
        $result = ShutdownHandler::getCallbackName($callback);
        $this->assertEquals($result, get_class($this) . '->shutdown');

        $callback = array(get_class($this), 'shutdown');
        $result = ShutdownHandler::getCallbackName($callback);
        $this->assertEquals($result, get_class($this) . '::shutdown');

        $callback = 'shutdown';
        $result = ShutdownHandler::getCallbackName($callback);
        $this->assertEquals($result, 'shutdown');

        $callback = function() {};
        $result = ShutdownHandler::getCallbackName($callback);
        $this->assertEquals($result, 'Closure');
    }
}
