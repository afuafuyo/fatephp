<?php
/**
 * @author
 * @license MIT
 */
namespace fate\core;

/**
 * 行为基类
 */
class Behavior extends FateObject {

    /**
     * @property Component 拥有行为的组件
     */
    public $component = null;

    /**
     * 声明要监听的组件的事件和对应事件的处理程序
     *
     * @return array
     *
     * [eventName => handler, ...]
     *
     */
    public function events() {
        return null;
    }

    /**
     * 监听组件的事件
     *
     * @param Component component 组件
     */
    public function listen($component) {
        $this->component = $component;

        $events = $this->events();

        if(null === $events) {
            return;
        }

        foreach($events as $eventName => $val) {
            $this->component->on($eventName, $val);
        }
    }

    /**
     * 取消监听组件的事件
     */
    public function unListen() {
        if(null === $this->component) {
            return;
        }

        $events = $this->events();

        if(null === $events) {
            return;
        }

        foreach($events as $eventName => $val) {
            $this->component->off($eventName, $val);
        }

        $this->component = null;
    }

}
