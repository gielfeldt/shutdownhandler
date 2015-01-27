<?php

namespace Gielfeldt\Ultimate;

/**
 * Class ShutdownHandler
 */
class ShutdownHandler {
    /**
     * Registered handler objects.
     * @var ShutdownHandler
     */
    static protected $handlers;

    /**
     * Stack counter for registered handler keys.
     * @var array
     */
    static protected $keys = array();

    /**
     * Stack counter for registered handler objects.
     * @var integer
     */
    static protected $counter = 0;

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
     * @var integer
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
    public function __construct($callback, array $arguments = array(), $key = NULL)
    {
        // Register a PHP shutdown handler first time around.
        if (!isset(static::$handlers))
        {
            $this->registerShutdownFunction(array(get_class($this), 'shutdown'));
            static::$handlers = array();
        }

        // Check validity of the callback. Note, this triggers autoload of classes
        // if necessary. We do this to avoid potential fatal errors during the
        // shutdown phase.
        if (!is_callable($callback))
        {
            throw new \RuntimeException(sprintf("Callback: '%s' is not callable", static::getCallbackName($callback)));
        }

        // Setup object properties.
        $this->callback = $callback;
        $this->arguments = $arguments;

        // ID must be non-numerical, so that PHP array indices won't shift
        // unexpectedly.
        $this->id = ":" . (string) (static::$counter++);

        // Register this handler.
        $this->reRegister($key);
        $this->setKey($key);
    }

    /**
     * Run the shutdown handler.
     */
    public function run()
    {
        if ($this->unRegister() && empty(static::$keys[$this->key]))
        {
            call_user_func_array($this->callback, $this->arguments);
        }
    }

    /**
     * Check if this handler is registered.
     *
     * @return boolean
     *   TRUE if handler is registered.
     */
    public function isRegistered()
    {
        return isset($this->id) && !empty(static::$handlers[$this->id]);
    }

    /**
     * Unregister handler.
     *
     * @return boolean
     *   TRUE if handler was unregistered (i.e. not already unregistered).
     */
    public function unRegister()
    {
        if (isset(static::$handlers[$this->id]))
        {
            if (isset($this->key))
            {
                static::$keys[$this->key]--;
            }
            unset(static::$handlers[$this->id]);
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Reregister handler.
     */
    public function reRegister($key = NULL)
    {
        // Set the key, and register the handler.
        $this->setKey($key);
        static::$handlers[$this->id] = $this;
    }

    /**
     * Get callback name in human readable format.
     *
     * @param callback $callback
     *   Any type of callback.
     *
     * @return string
     *   The name of the callback in human readable format.
     */
    static public function getCallbackName($callback)
    {
        if (is_array($callback))
        {
            if (is_object($callback[0]))
            {
                // Return name of object method with arrow notation.
                return get_class($callback[0]) . '->' . $callback[1];
            }
            else
            {
                // Return name of class method with double-colon notation.
                return $callback[0] . '::' . $callback[1];
            }
        }
        elseif (is_object($callback))
        {
            // Most likely an anonymous function. Return the class name.
            return get_class($callback);
        }
        else
        {
            // Plain string containing function name.
            return $callback;
        }
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

        // Only decrement the counter, if this is a registered handler.
        if (isset($this->key) && $this->isRegistered())
        {
            static::$keys[$this->key]--;
        }

        // Set the new key, and increment the counter appropriately.
        $this->key = $key;
        if (isset($key))
        {
            static::$keys[$key] = empty(static::$keys[$key]) ? 1 : static::$keys[$key] + 1;
        }
    }

    /**
     * Real shutdown handler.
     *
     * Called by PHP's shutdown handler. Run through the registered handlers,
     * and run them.
     */
    static public function shutdown()
    {
        // Always pick the first handler in the array. When a handler is run, it
        // will remove itself from the array (unregister).
        while ($handler = reset(static::$handlers))
        {
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
}
