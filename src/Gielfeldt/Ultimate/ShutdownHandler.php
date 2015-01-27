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
   * Registered handler keys.
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
   * Has this handler already been executed?
   * @var boolean
   */
  protected $used = FALSE;


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
  public function __construct($callback, array $arguments = array(), $key = NULL) {
    if (!isset(static::$handlers)) {
      $this->registerShutdownFunction(array(get_class($this), 'shutdown'));
      static::$handlers = array();
    }
    if (!is_callable($callback)) {
      throw new \RuntimeException(sprintf("Callback: '%s' is not callable", static::getCallbackName($callback)));
    }
    $this->callback = $callback;
    $this->arguments = $arguments;
    $this->id = ":" . (string) (static::$counter++);
    $this->setKey($key);
    static::$handlers[$this->id] = $this;
  }

  /**
   * Run the shutdown handler.
   */
  public function run() {
    if ($this->unRegister() && empty(static::$keys[$this->key])) {
      call_user_func_array($this->callback, $this->arguments);
    }
  }

  /**
   * Check if this handler is registered.
   *
   * @return boolean
   *   TRUE if handler is registered.
   */
  public function isRegistered() {
    return isset($this->id) && !empty(static::$handlers[$this->id]);
  }

  /**
   * Unregister handler.
   */
  public function unRegister() {
    unset(static::$handlers[$this->id]);
    if (!$this->used) {
      $this->used = TRUE;
      if (isset($this->key)) {
        static::$keys[$this->key]--;
      }
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Reregister handler.
   */
  public function reRegister($key = NULL) {
    if ($this->isRegistered()) {
      return FALSE;
    }
    static::$handlers[$this->id] = $this;
    $this->used = FALSE;
    $this->setKey($key);
    return TRUE;
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
  static public function getCallbackName($callback) {
    if (is_array($callback)) {
      if (is_object($callback[0])) {
        return get_class($callback[0]) . '->' . $callback[1];
      }
      else {
        return $callback[0] . '::' . $callback[1];
      }
    }
    elseif (is_object($callback)) {
      return get_class($callback);
    }
    else {
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
  protected function setKey($key) {
    if (isset($this->key) && $this->isRegistered()) {
      static::$keys[$this->key]--;
    }
    $this->key = $key;
    if (isset($key)) {
      static::$keys[$key] = empty(static::$keys[$key]) ? 1 : static::$keys[$key] + 1;
    }
  }

  /**
   * Real shutdown handler.
   */
  static public function shutdown() {
    while ($handler = array_shift(static::$handlers)) {
      $handler->run();
    }
  }

  /**
   * Register PHP shutdown handler.
   *
   * @param callback $callback
   *   Callback to call on shutdown.
   * @param array $arguments
   *   Arguments for the callback.
   */
  protected function registerShutdownFunction($callback, array $arguments = array()) {
    register_shutdown_function($callback, $arguments);
  }
}
