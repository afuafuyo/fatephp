<?php
/**
 * @author
 * @license MIT
 */
namespace fate\core;

abstract class ErrorHandler extends Object {
    
    /**
     * Register a error handler
     */
    public abstract function register();
    
    /**
     * Unregister a error handler
     */
    public abstract function unregister();
    
}
