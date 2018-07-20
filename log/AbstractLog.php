<?php
/**
 * @author
 * @license MIT
 */
namespace fate\log;

abstract class AbstractLog extends \fate\core\Event implements ILog {
    
    const EVENT_FLUSH = 'flush';
    
    /**
     * 重写
     */
    public function trigger($eventName, $param = null) {
        if( !isset($this->_handlers[$eventName]) ) {
            return;
        }
        
        foreach($this->_handlers[$eventName] as $handler) {
            $handler->flush($param);
        }
    }
    
}
