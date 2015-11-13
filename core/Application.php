<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\core;

use Y;
use y\core\InvalidConfigException;

/**
 * 应用前端控制器
 */
class Application extends Object {

    public function __construct($config = []) {
        // 由于多态 此时 $this 是子类 Application
        Y::$app = $this;
        
        $this->errorHandler();
        
        $this->init($config);
        
        Y::config($this, $config);
    }
    
    /**
     * 异常处理
     */
    public function errorHandler() {}
	
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

}
