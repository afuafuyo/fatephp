<?php
namespace y\core;

use y\core\InvalidCallException;
use y\core\UnknownPropertyException;

/**
 * 所有类的父类
 */
class Object {

    /*
    public function __set($name, $value) {
        $this->$name = $value;
    }
    */

    /**
     * 读取不存在的属性
     *
     * @param string $name 属性名
     */
    public function __get($name) {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
            
        } else {
            throw new UnknownPropertyException('Getting unknown property: ' . get_class($this) . '::' . $name);
        }
    }
    
    /**
     * 调用不存在的方法
     *
     * @param string $name 方法名字
     * @param array $params 方法参数
     */
    public function __call($name, $params) {
        throw new InvalidCallException('Calling unknown method: ' . get_class($this) . "::$name()");
    }
    
    /**
     * 方法是否存在
     *
     * @param string $name 方法名字
     */
    public function hasMethod($name) {
        return method_exists($this, $name);
    }
    
}
