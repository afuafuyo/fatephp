<?php
/**
 * @author
 * @license MIT
 */
namespace fate;

use fate\core\ClassNotFoundException;

class FateBase {
    
    /**
     * @var object 当前应用
     */
    public static $app = null;

    /**
     * @var array 路径别名
     */
    public static $pathAliases = ['@fate' => __DIR__];

    /**
     * @ 别名路径转换真实路径
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
            static::$pathAliases[$alias] = rtrim($path, '/');
        }
    }

    /**
     * 对象配置
     *
     * @param Object $object 需要配置的对象
     * @param array $properties 配置项
     * @return Object 源对象
     */
    public static function config($object, $properties) {
        foreach($properties as $key => $val) {
            $object->$key = $val;
        }

        return $object;
    }

    /**
     * 创建对象
     *
     * @param string | array $clazz 类全名 或 类配置
     *
     * 'some\namespace\Class'
     * or
     * [
     *    'classPath' => 'some\namespace\Class'
     *    '...' => ...
     * ]
     *
     * @param array $params 参数
     * @throws ClassNotFoundException 类未找到
     * @return Object 类实例
     */
    public static function createObject($clazz, array $params = []) {
        $file = '';
        $classPath = '';
        $properties = null;
        
        if(is_string($clazz)) {
            $classPath = $clazz;
            $file = static::namespaceToNormal($clazz);
            
        } else if(is_array($clazz) && isset($clazz['classPath'])) {
            $classPath = $clazz['classPath'];
            $file = static::namespaceToNormal($clazz['classPath']);
            
            $properties = $clazz;
            unset($properties['classPath']);
        }
        
        if('' === $file || !is_file($file)) {
            throw new ClassNotFoundException('The class: '. $clazz .' not found');
        }

        $reflection = new \ReflectionClass($classPath);
        $instance = $reflection->newInstanceArgs($params);
        
        if(null !== $properties) {
            static::config($instance, $properties);
        }

        return $instance;
    }

    /**
     * namespace 路径转换真实路径
     *
     * @param string $namespace 命名空间
     * @param string $extension 扩展
     * @return string 文件路径
     */
    public static function namespaceToNormal($namespace, $extension = '.php') {
        $path = static::getPathAlias('@' . str_replace('\\', '/', $namespace));
        
        return $path . $extension;
    }

    /**
     * 类自动加载器
     *
     * @param string $className 要载入的类全名 eg. fate\web\Application
     */
    public static function autoload($className) {
        // 导入有命名空间的类
        if(false !== strpos($className, '\\')) {
            $classFile = static::namespaceToNormal($className);
            if('' === $classFile || !is_file($classFile)) {
                return;
            }

            include($classFile);
        }

        return;
    }
    
}
