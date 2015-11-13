<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\web;

class ErrorHandler {
    
    /**
     * 注册异常处理函数
     */
    public function register() {
        //ini_set('display_errors', false);
        
        //set_exception_handler([$this, 'handleException']);
        
        //set_error_handler([$this, 'handleError']);
        
        //register_shutdown_function([$this, 'handleFatalError']);
    }
    
    /**
     * 异常处理
     *
     * @param Exception $exception 异常类
     */
    public function handleException($exception) {
        echo $exception->getMessage();
    }
    
    /**
     * 错误处理
     *
     * @param int $code the level of the error raised.
     * @param string $message the error message.
     * @param string $file the filename that the error was raised in.
     * @param int $line the line number the error was raised at.
     */
    public function handleError($code, $message, $file, $line) {
        echo $message;
    }
    
    /**
     * Handles fatal PHP errors
     */
    public function handleFatalError() {
        $error = error_get_last();
        echo $error['message'];
    }
}
