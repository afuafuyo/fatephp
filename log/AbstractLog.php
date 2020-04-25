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
    public function trigger($eventName, $parameter = null) {
        if( !isset($this->eventsMap[$eventName]) ) {
            return;
        }

        foreach($this->eventsMap[$eventName] as $handler) {
            $handler->flush($parameter);
        }
    }

}
