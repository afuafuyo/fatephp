<?php
/**
 * @author yu
 * @license MIT
 */
namespace y\log;

abstract class ImplLog extends \y\core\Event implements ILog {
    
    const EVENT_FLUSH = 'flush';
    
    /**
     * 重写
     */
    public function trigger($eventName, $param = null) {
        if(isset($this->_handlers[$eventName])) {
            foreach($this->_handlers[$eventName] as $handler) {
                $handler->flush($param);
            }
        }
    }
    
}
