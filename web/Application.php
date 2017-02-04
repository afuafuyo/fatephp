<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\web;

use Y;
use y\web\Request;
use y\web\Router;
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
        
        // 解析路由
        $router = Router::resolve($this, $route);
        $moduleId = $router[0];
        $controllerId = $router[1];
        $routePrefix = $router[2];  // 前缀目录
                
        // 保存当前控制器标示
        $this->controllerId = '' === $controllerId ? $this->defaultControllerId : $controllerId;
        
        // 搜索顺序 模块控制器 -> 普通控制器
        // 模块没有前缀目录
        if('' !== $moduleId && null !== $this->modules && isset($this->modules[$moduleId])) {
            $clazz = trim($this->modules[$moduleId], '\\') . '\\controllers\\' .
                ucfirst($this->controllerId) . 'Controller';
            $this->moduleId = $moduleId;
            
            return Y::createObject($clazz);
        }
        
        // 普通控制器有前缀目录
        $this->routePrefix = '' === $routePrefix ? $this->controllerId : $routePrefix;

        return Y::createObject( $this->defaultControllerNamespace . '\\' .
            $this->routePrefix . '\\' . ucfirst($this->controllerId) . 'Controller' );
    }
    
}
