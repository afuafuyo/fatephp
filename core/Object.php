<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\core;

use y\core\InvalidCallException;
use y\core\UnknownPropertyException;

/**
 * 所有类的父类
 */
class Object {

    /**
     * 设置不存在或私有的属性
     *
     * @param string $name 属性名
     * @param string $value 属性值
     */
    public function __set($name, $value) {
        $setter = 'set' . $name;
        if(method_exists($this, $setter)) {
            $this->$setter($value);
            
        } else {
            throw new UnknownPropertyException('Setting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * 读取不存在或私有的属性
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
     * 调用不存在或私有的方法
     *
     * @param string $name 方法名字
     * @param array $params 方法参数
     */
    public function __call($name, $params) {
        throw new InvalidCallException('Calling unknown method: ' . get_class($this) . "::$name()");
    }

}
