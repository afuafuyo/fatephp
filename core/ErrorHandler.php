<?php
/**
 * @author
 * @license MIT
 */
namespace fate\core;

abstract class ErrorHandler extends FateObject {
    
    /**
     * Register a error handler
     */
    public abstract function register();
    
    /**
     * Unregister a error handler
     */
    public abstract function unregister();
    
}
