<?php
namespace y\core;

use Y;
use y\core\InvalidConfigException;
use y\core\ClassNotFoundException;

/**
 * 应用前端控制器
 */
class Application extends \y\core\Object {

    public function __construct($config = []) {
        // 由于多态 此时 $this 是子类 Application
        Y::$app = $this;
        
        $this->init($config);
        
        Y::config($this, $config);
    }
	
    /**
     * 初始化应用
     *
     * @param array $config 应用配置
     * @throws InvalidConfigException 当丢失必要配置项目时
     */
    public function init(& $config) {
        if(!isset($config['id'])) {
            throw new InvalidConfigException('The "id" configuration is required.');
        }
        if(isset($config['appPath'])) {
            $this->setAppPath($config['appPath']);
            unset($config['appPath']);
            
        } else {
            throw new InvalidConfigException('The "appPath" configuration is required.');
        }
    }

    /**
     * 设置应用路径
     *
     * @param string $path 应用路径
     */
    public function setAppPath($path) {
        Y::setPathAlias('@app', $path);
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
            throw new ClassNotFoundException('The Controller class '. $clazz .' not found.');
        }
        
        return $instance;
    }

}
