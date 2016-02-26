<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\core;

use Y;
use y\core\InvalidCallException;
use y\core\UnknownPropertyException;
use y\core\ClassNotFoundException;

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
    
    /**
     * 创建对象
     *
     * @param string $clazz 类全名
     * @throws ClassNotFoundException 类未找到
     * @return Object 类实例
     */
    public function createObject($clazz) {
        $instance = Y::createObject($clazz);
        
        if(null === $instance) {
            throw new ClassNotFoundException('The class: '. $clazz .' not found');
        }
        
        return $instance;
    }
    
}
