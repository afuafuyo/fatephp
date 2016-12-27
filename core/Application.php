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

    /**
     * @var array 自定义路由
     */
    public $routes = [];

    /**
     * @var array 注册的模块
     */
    public $modules = [];

    /**
     * @var string 路由标识
     */
    public $defaultRouteParam = 'r';

    /**
     * @var string 默认路由
     */
    public $defaultRoute = 'index/index';

    /**
     * @var string 默认控制器命名空间
     */
    public $defaultControllerNamespace = 'app\\controllers';

    /**
     * @var string 默认控制器
     */
    public $defaultControllerId = 'index';

    /**
     * @var string 当前的模块
     */
    public $moduleId;

    /**
     * @var string 当前的控制器
     */
    public $controllerId;

    /**
     * @var string 前缀目录
     */
    public $routePrefix;
    
    public function __construct($config = []) {
        // 由于多态 此时 $this 是子类 Application
        Y::$app = $this;
        
        $this->errorHandler();
        
        $this->init($config);
        
        Y::config($this, $config);
    }
    
    /**
     * 设置属性
     */
    public function __set($name, $value) {
        $this->$name = $value;
    }
    
    /**
     * 异常处理
     */
    public function errorHandler() {}
    
    /**
     * 运行应用
     */
    public function run() {}
    
    /**
     * 创建控制器
     * 路由 'xxx/yyy' 中 xxx 可能为模块 id 或前缀目录  
     * 如 xxx 模块的 yyy 控制器 或 xxx 目录下的 yyy 控制器
     *
     * @return Object 控制器
     */
    public function createController() {}
	
    /**
     * 初始化应用
     *
     * @param array $config 应用配置
     * @throws InvalidConfigException 当丢失必要配置项目时
     */
    public function init(& $config) {
        if(!isset($config['id'])) {
            throw new InvalidConfigException('The "id" configuration is required');
        }
        
        if(isset($config['appPath'])) {
            $this->setAppPath($config['appPath']);
            unset($config['appPath']);
            
        } else {
            throw new InvalidConfigException('The "appPath" configuration is required');
        }
        
        if(isset($config['runtimePath'])) {
            $this->setRuntimePath($config['runtimePath']);
            unset($config['runtimePath']);
            
        } else {
            // set "app/runtime"
            $this->setRuntimePath( $this->getAppPath() . '/runtime');
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
     * 得到应用目录
     */
    public function getAppPath(){
        return Y::getPathAlias('@app');
    }
    
    /**
     * 设置 runtime 路径
     *
     * @param string $path 路径
     */
    public function setRuntimePath($path) {
        Y::setPathAlias('@runtime', $path);
    }
    
    /**
     * 得到 runtime 目录
     */
    public function getRuntimePath() {
        return Y::getPathAlias('@runtime');
    }

}
