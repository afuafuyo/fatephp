<?php
/**
 * @author
 * @license MIT
 */
namespace fate\core;

/**
 * 事件基类
 */
class Event extends FateObject {

    /**
     * @var array the attached event handlers
     *
     * [
     *     'eventName' => [fn1, fn2...]
     *     'eventName2' => [fn1, fn2...]
     * ]
     *
     */
    protected $eventsMap = [];

    /**
     * 注册事件处理
     *
     * @param string $eventName 事件名称
     * @param callable $handler 回调函数
     */
    public function on($eventName, $handler) {
        if(!isset($this->eventsMap[$eventName])) {
            $this->eventsMap[$eventName] = [];
        }

        $this->eventsMap[$eventName][] = $handler;
    }

    /**
     * 注销事件处理
     *
     * @param string $eventName 事件名称
     * @param callable $handler 回调函数
     */
    public function off($eventName, $handler = null) {
        if(!isset($this->eventsMap[$eventName])) {
            return;
        }

        if(null === $handler) {
            unset($this->eventsMap[$eventName]);

        } else {
            foreach($this->eventsMap[$eventName] as $i => $h) {
                if($handler === $h) {
                    unset($this->eventsMap[$eventName][$i]);
                }
            }
        }
    }

    /**
     * 触发
     *
     * @param string $eventName 事件名称
     * @param mixed $parameter 参数
     */
    public function trigger($eventName, $parameter = null) {
        if(!isset($this->eventsMap[$eventName])) {
            return;
        }

        foreach($this->eventsMap[$eventName] as $handler) {
            null === $parameter ? call_user_func($handler)
                : call_user_func($handler, $parameter);
        }
    }

}

