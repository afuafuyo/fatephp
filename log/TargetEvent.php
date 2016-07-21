<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\log;

class TargetEvent extends \y\core\Event {
    const EVENT_FLUSH = 'flush';
    
    /**
     * @inheritdoc
     */
    public function trigger($eventName, $param = null) {
        if(isset($this->_handlers[$eventName])) {
            foreach($this->_handlers[$eventName] as $handler) {
                $handler->flush($param);
            }
        }
    }
}