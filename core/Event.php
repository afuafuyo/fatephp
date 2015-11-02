<?php
namespace y\core;

/**
 * 事件基类
 */
class Event extends Object {

    /**
     * @var array 事件回调
     */
    private $_handlers = [];

    /**
     * 注册事件处理
     *
     * @param string $eventName 事件名称
     * @param function $handler 回调函数
     */
    public function on($eventName, $handler) {
        if(!isset($this->_handlers[$eventName])) {
            $this->_handlers[$eventName] = [];
        }
        
        $this->_handlers[$eventName][] = $handler;
    }

    /**
     * 注销事件处理
     *
     * @param string $eventName 事件名称
     * @param function $handler 回调函数
     */
    public function off($eventName, $handler = null) {
        if(isset($this->_handlers[$eventName])) {
            if(null === $handler) {
                unset($this->_handlers[$eventName]);
                
            } else {
                foreach($this->_handlers[$eventName] as $i => $h) {
                    if($handler === $h) {
                        unset($this->_handlers[$eventName][$i]);
                    }
                }
            }
        }
    }

    /**
     * 触发
     *
     * @param string $eventName 事件名称
     */
    public function trigger($eventName) {
        if(isset($this->_handlers[$eventName])) {
            foreach($this->_handlers[$eventName] as $handler) {
                call_user_func($handler);
            }
        }
    }
}

