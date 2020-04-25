<?php
/**
 * @author
 * @license MIT
 */
namespace fate\core;

use fate\core\InvalidCallException;
use fate\core\UnknownPropertyException;

/**
 * 所有类的父类
 */
class FateObject {

    /**
     * 设置不存在或私有的属性
     *
     * @param string $name 属性名
     * @param string $value 属性值
     */
    // public function __set($name, $value) {
    //     $this->$name = $value;
    // }

    /**
     * 读取不存在或私有的属性
     *
     * @param string $name 属性名
     */
    public function __get($name) {
        // 不区分大小写
        $getter = 'get' . $name;

        if(method_exists($this, $getter)) {
            return $this->$getter();
        }

        throw new UnknownPropertyException('Getting unknown property: ' . get_class($this) . '::' . $name);
    }

    /**
     * 调用不存在或私有的方法
     *
     * @param string $name 方法名字
     * @param array $parameters 方法参数
     */
    public function __call($name, $parameters) {
        throw new InvalidCallException('Calling unknown method: ' . get_class($this) . "::$name()");
    }

    /**
     * 判断属性是否存在
     *
     * @param string $name
     * @return boolean
     */
    public function hasProperty($name) {
        return property_exists($this, $name);
    }

    /**
     * 判断方法是否存在
     *
     * @param string $name
     * @return boolean
     */
    public function hasMethod($name) {
        return method_exists($this, $name);
    }

}
