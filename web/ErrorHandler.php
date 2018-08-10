<?php
/**
 * @author
 * @license MIT
 */
namespace fate\web;

use Fate;

/**
 * Web 异常处理
 */
class ErrorHandler extends \fate\core\ErrorHandler {
    
    /**
     * {@inheritdoc}
     */
    public function register() {
        // ini_set('display_errors', false);
        
        set_exception_handler([$this, 'handleException']);
    }
    
    /**
     * {@inheritdoc}
     */
    public function unregister() {
        restore_exception_handler();
    }
    
    /**
     * 异常处理
     *
     * @param \Exception $exception 异常类
     */
    public function handleException($exception) {
        $this->unregister();
        
        if(PHP_SAPI !== 'cli') {
            http_response_code(500);
        }
        
        $msg = $this->handlerExceptionMessage($exception);
        
        try {
            if('' !== Fate::$app->errorHandler) {
                $h = Fate::createObject(Fate::$app->errorHandler);
                $h->run($msg);
                
            } else {
                echo $msg;
            }
            
        } catch(\Exception $e) {
            echo $this->handlerExceptionMessage($exception);
        }
        
        exit(1);
    }
    
    public function handlerExceptionMessage($exception) {
        $msg = FATE_DEBUG ?
            '<pre>An exception occurred: '. (string) $exception .'</pre>' :
            'An internal server error occurred';
            
        return $msg;
    }
    
}
