<?php

namespace Gielfeldt;

/**
 * Class ShutdownHandler
 */
class ShutdownHandler
{
    /**
     * Registered handler objects.
     * @var array
     */
    protected static $handlers;

    /**
     * Stack counter for registered handler keys.
     * @var array
     */
    protected static $keys = array();

    /**
     * Stack counter for registered handler objects.
     * @var integer
     */
    protected static $counter = 0;

    /**
     * Callback for handler.
     * @var callback
     */
    protected $callback;

    /**
     * Arguments for callback.
     * @var array
     */
    protected $arguments;

    /**
     * The handler object's id in the stack counter.
     * @var string
     */
    protected $id;

    /**
     * The handler object's key.
     * @var string
     */
    protected $key;

    /**
     * ------ PUBLIC METHODS ------
     */

    /**
     * Constructor.
     *
     * Instantiate shutdown handler object.
     *
     * @param callback $callback
     *   Callback to call on shutdown.
     * @param array $arguments
     *   Arguments for the callback.
     */
    public function __construct($callback, array $arguments = array(), $key = null)
    {
        // Register a PHP shutdown handler first time around.
        if (!isset(static::$handlers)) {
            $this->registerShutdownFunction(array(get_class($this), 'shutdown'));
            static::$handlers = array();
        }

        // Check validity of the callback. Note, this triggers autoload of classes
        // if necessary. We do this to avoid potential fatal errors during the
        // shutdown phase.
        if (!static::isCallable($callback, false, $callback_name)) {
            throw new \RuntimeException(sprintf("Callback: '%s' is not callable", $callback_name));
        }

        // Setup object properties.
        $this->callback = $callback;
        $this->arguments = $arguments;

        // ID must be non-numerical, so that PHP array indices won't shift
        // unexpectedly.
        $this->id = ":" . (string) (static::$counter++);

        // Register this handler.
        $this->reRegister($key);
    }

    /**
     * Run the shutdown handler.
     */
    public function run()
    {
        if ($this->unRegister() && empty(static::$keys[$this->key])) {
            call_user_func_array($this->callback, $this->arguments);
        }
    }

    /**
     * Check if this handler is registered.
     *
     * @return boolean
     *   true if handler is registered.
     */
    public function isRegistered()
    {
        return isset($this->id) && !empty(static::$handlers[$this->id]);
    }

    /**
     * Unregister handler.
     *
     * @return boolean
     *   true if handler was unregistered (i.e. not already unregistered).
     */
    public function unRegister()
    {
        if (isset(static::$handlers[$this->id])) {
            if (isset($this->key)) {
                static::$keys[$this->key]--;
            }
            unset(static::$handlers[$this->id]);
            return true;
        }
        return false;
    }

    /**
     * Reregister handler.
     */
    public function reRegister($key = null)
    {
        // Set the key, and register the handler.
        $this->setKey($key);
        static::$handlers[$this->id] = $this;
    }

    /**
     * ------ INTERNAL METHODS ------
     */

    /**
     * Set key for final nested destructor.
     *
     * @param string $key
     *   Name of key.
     */
    protected function setKey($key)
    {
        // If a handler switches key, we need to decrement the counter for the old
        // key, and increment the counter for the new key.

        // Only decrement the counter, if this is already a registered handler.
        if (isset($this->key) && $this->isRegistered()) {
            static::$keys[$this->key]--;
        }

        // Set the new key, and increment the counter appropriately.
        $this->key = $key;
        if (isset($key)) {
            @static::$keys[$key]++;
        }
    }

    /**
     * Real shutdown handler.
     *
     * Called by PHP's shutdown handler. Run through the registered handlers,
     * and run them.
     */
    public static function shutdown()
    {
        // Always pick the first handler in the array. When a handler is run, it
        // will remove itself from the array (unregister).
        while ($handler = reset(static::$handlers)) {
            $handler->run();
        }
    }

    /**
     * Register PHP shutdown handler.
     *
     * This function exists, so that subclasses use another
     * register_shutdown_function().
     *
     * @param callback $callback
     *   Callback to call on shutdown.
     * @param array $arguments
     *   Arguments for the callback.
     */
    protected function registerShutdownFunction($callback, array $arguments = array())
    {
        // Just use PHP's default shutdown handler.
        register_shutdown_function($callback, $arguments);
    }

    /**
     * Wrapper for is_callable().
     *
     * Does the same as is_callable(), but uses -> notation insted of :: if
     * callable is an object method.
     *
     * @see is_callable()
     */
    public static function isCallable($name, $syntax_only = false, &$callable_name = null)
    {
        $result = is_callable($name, $syntax_only, $callable_name);
        if (is_array($name)) {
            $callable_name = is_object($name[0]) ? str_replace('::', '->', $callable_name) : $callable_name;
        }
        return $result;
    }
}
