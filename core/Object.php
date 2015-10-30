<?php
namespace y\core;

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
}
