<?php
namespace y;

class YBase {
	/**
	 * @var object 当前应用
	 */
	public static $app = null;
	
	/**
	 * @var array 路径别名
	 */
	public static $pathAliases = ['@y' => __DIR__];
	
	/**
	 * 根据别名的到真实路径
	 *
	 * @param string $alias 路径别名
	 * @return string 路径
	 */
	public static function getPathAlias($alias) {
		if('@' !== $alias[0]) {
			return $alias;
		}
		
		// 截取开头作为别名
		$pos = strpos($alias, '/');
        $root = false === $pos ? $alias : substr($alias, 0, $pos);
		if(isset(static::$pathAliases[$root])) {
			return false === $pos ? 
				static::$pathAliases[$root] : 
				static::$pathAliases[$root] . substr($alias, $pos);
		}
		
		return '';
	}
	
	/**
	 * 设置路径别名
	 *
	 * @param string $alias 路径别名
	 * @param string $path 路径
	 */
	public static function setPathAlias($alias, $path) {
		if('@' !== $alias[0]) {
			$alias = '@' . $alias;
		}
		
		if(null === $path) {
			unset(static::$pathAliases[$alias]);
			
		} else {
			static::$pathAliases[$alias] = $path;
		}
	}
	
	/**
	 * 对象配置
	 *
	 * @param Object $object 需要配置的对象
	 * @param array $property 配置项
	 * @return Object 源对象
	 */
	public static function config($object, $propertys) {
		foreach($propertys as $key => $val) {
			$object->$key = $val;
		}
		
		return $object;
	}
	
	/**
	 * 创建对象
	 *
	 * @param string $clazz 类全名
	 * @return null | Object 类实例
	 */
	public static function createObject($clazz) {
		$real = static::getPathAlias('@' . str_replace('\\', '/', $clazz) . '.php');
		if('' === $real || !is_file($real)) {
			return null;
		}
		
		$reflection = new \ReflectionClass($clazz);
		
		return $reflection->newInstance();
	}
	 
	/**
	 * 类自动加载器
	 *
	 * @param string $className 要载入的类全名
	 */
	public static function autoload($className) {
		// 导入有命名空间的类
		if(false !== strpos($className, '\\')) {
			$classFile = static::getPathAlias('@' . str_replace('\\', '/', $className) . '.php');
            if('' === $classFile || !is_file($classFile)) {
                return;
            }
			
			include($classFile);
		}
		
		return;
	}
}
