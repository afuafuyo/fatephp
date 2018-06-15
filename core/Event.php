<?php
/**
 * @author
 * @license MIT
 */
namespace y\core;

/**
 * 事件基类
 */
class Event extends Object {

    /**
     * @var array 事件回调
     */
    protected $_handlers = [];

    /**
     * 注册事件处理
     *
     * @param string $eventName 事件名称
     * @param callable $handler 回调函数
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
     * @param callable $handler 回调函数
     */
    public function off($eventName, $handler = null) {
        if(!isset($this->_handlers[$eventName])) {
            return;
        }
        
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

    /**
     * 触发
     *
     * @param string $eventName 事件名称
     * @param mixed $param 参数
     */
    public function trigger($eventName, $param = null) {
        if(!isset($this->_handlers[$eventName])) {
            return;
        }
        
        foreach($this->_handlers[$eventName] as $handler) {
            null === $param ? call_user_func($handler)
                : call_user_func($handler, $param);
        }
    }

}

