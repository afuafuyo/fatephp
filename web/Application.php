<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\web;

use Y;
use y\web\Request;
use y\core\InvalidCallException;

/**
 * 应用前端控制器
 */
class Application extends \y\core\Application {

    /**
     * @var Object 异常处理类
     */
    public $errorHandler = 'y\\web\\ErrorHandler';

    /**
     * 运行应用
     *
     * @throws InvalidCallException 方法未找到
     * @return array | null
     */
    public function run(){
        $controller = $this->createController();

        if(!method_exists($controller, 'run')) {
            throw new InvalidCallException('The run() method of Controller not found');
        }

        // 单一入口
        return $controller->run();
    }

    /**
     * @inheritdoc
     */
    public function errorHandler() {
        $handler = Y::createObject($this->errorHandler);
        
        $handler->register();
    }
    
    /**
     * @inheritdoc
     */
    public function createController() {
        // $route eg. index/index
        $route = Request::getInstance()->getParam($this->defaultRouteParam);
        if('' === $route || '/' === $route) {
            $route = $this->defaultRoute;
        }
        
        // 检测非法 与 路径中不能有双斜线 '//'
        $route = trim($route, '/');
        if(0 === preg_match('/^[\w\-\/]+$/', $route) || false !== strpos($route, '//')) {
            return null;
        }
        
        $_moduleId = '';
        $_controllerId = '';
        $_routePrefix = '';  // 前缀目录
        // 优先解析自定义路由
        $resolveRoute = $this->resolveUserRoute($route);
        if('' !== $resolveRoute[0] || '' !== $resolveRoute[1]) {
            $_moduleId = $resolveRoute[0];
            $_controllerId = $resolveRoute[1];
            $_routePrefix = str_replace('/', '\\', $resolveRoute[2]);  // namespace path
            
            if('' !== $_moduleId && !isset($this->modules[$_moduleId])) {
                throw new InvalidConfigException('The config module ' . $_moduleId . ' not found');
            }
            
        } else {
            // 解析路由
            if(false !== strpos($route, '/')) {
                list($_moduleId, $_controllerId) = explode('/', $route, 2);
            } else {
                $_moduleId = $route;
                $_controllerId = '';
            }
            
            // 解析前缀目录
            $_routePrefix = $_moduleId;
            if( false !== ($pos = strrpos($_controllerId, '/')) ) {
                $_routePrefix .= '/' . substr($_controllerId, 0, $pos);
                $_controllerId = substr($_controllerId, $pos + 1);
                $_routePrefix = str_replace('/', '\\', $_routePrefix);  // namespace path
            }
        }
        
        // 保存当前控制器标示
        $this->controllerId = '' === $_controllerId ? $this->defaultControllerId : $_controllerId;
        
        // 搜索顺序 模块控制器 -> 普通控制器
        // 模块没有前缀目录
        if('' !== $_moduleId && isset($this->modules[$_moduleId])) {
            $clazz = trim($this->modules[$_moduleId], '\\') . '\\controllers\\' .
                ucfirst($this->controllerId) . 'Controller';
            $this->moduleId = $_moduleId;
            
            return Y::createObject($clazz);
        }
        
        // 普通控制器有前缀目录
        $this->routePrefix = '' === $_routePrefix ? $this->controllerId : $_routePrefix;

        return Y::createObject( $this->defaultControllerNamespace . '\\' .
            $this->routePrefix . '\\' . ucfirst($this->controllerId) . 'Controller' );
    }
    
    /**
     * 解析自定义路由 自定义路由会明确给出模块或控制器 id
     *
     * @param string $route 路由
     */
    public function resolveUserRoute($route) {
        $_moduleId = '';
        $_controllerId = '';
        $_routePrefix = '';
        
        if(!empty($this->routes)) {
            $matches = null;
            foreach($this->routes as $regularRoute => $mapping) {
                if(1 === preg_match('/' . str_replace('/', '\\/', trim($regularRoute, '/')) . '/', $route, $matches)) {
                    if(isset($mapping['moduleId'])) {
                        $_moduleId = $mapping['moduleId'];
                    }
                    if(isset($mapping['controllerId'])) {
                        $_controllerId = $mapping['controllerId'];
                    }
                    if(isset($mapping['prefix'])) {
                        $_routePrefix = $mapping['prefix'];
                    }
                    // 用户自定义路由需要处理参数
                    if(isset($mapping['params'])) {
                        if(is_array($mapping['params']) &&
                            isset($mapping['params']['key']) &&
                            isset($mapping['params']['segment'])) {
                            
                            if(is_array($mapping['params']['key'])) {
                                for($j=0,$len=count($mapping['params']['key']); $j<$len; $j++) {
                                    $_GET[$mapping['params']['key'][$j]] = $matches[$mapping['params']['segment'][$j]];
                                }
                            
                            } else {
                                $_GET[$mapping['params']['key']] = $matches[$mapping['params']['segment']];
                            }
                        }
                    }
                    
                    break;
                }
            }
        }
        
        return [$_moduleId, $_controllerId, $_routePrefix];
    }
}
