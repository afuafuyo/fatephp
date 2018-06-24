<?php
/**
 * @author
 * @license MIT
 */
namespace fate\web;

class ErrorHandler extends \fate\core\ErrorHandler {
    
    /**
     * 注册异常处理函数
     */
    public function register() {
        //ini_set('display_errors', false);
        
        set_exception_handler([$this, 'handleException']);
    }
    
    /**
     * 异常处理
     *
     * @param Exception $exception 异常类
     */
    public function handleException($exception) {
        if(PHP_SAPI !== 'cli') {
            http_response_code(500);
        }
        
        if(FATE_DEBUG) {
            echo '<pre>An exception occurred: '. (string) $exception .'</pre>';
               
        } else {
            echo 'An internal server error occurred';
        }
        
        exit(1);
    }
    
}
