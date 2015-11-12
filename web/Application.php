<?php
namespace y\web;

use y\web\Request;
use y\core\InvalidCallException;

/**
 * 应用前端控制器
 */
class Application extends \y\core\Application {

    /**
     * @var string 路由标识
     */
    public $routeParam = 'r';

    /**
     * @var string 默认路由
     */
    public $defaultRoute = 'index';

    /**
     * @var string 默认控制器
     */
    public $defaultControllerId = 'index';

    /**
     * @var string 默认控制器命名空间
     */
    public $defaultControllerNamespace = 'app\\controllers';

    /**
     * @var array 注册的模块
     */
    public $modules = [];

    /**
     * @var string 当前的模块
     */
    public $moduleId = '';
    
    /**
     * @var string 当前的控制器
     */
    public $controllerId = '';
    
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
        $request = Request::getInstance();
        $route = $request->parseUrl($this->routeParam);
        $controller = $this->createController($route);

        if(!method_exists($controller, 'execute')) {
            throw new InvalidCallException('The Controller\'s execute() method not found.');
        }

        // 单一入口
        return $controller->execute();
    }
    
    /**
     * 异常处理
     */
    public function errorHandler() {
        $handler = $this->createObject($this->errorHandler);
        
        $handler->register();
    }

    /**
     * 创建控制器
     *
     * @param string $route 路由参数 eg. user/index or user
     * @return Object 控制器
     */
    public function createController($route) {
        $id = '';
        if('' === $route) {
            $route = $this->defaultRoute;
        }

        // 检测非法 与 路径中不能有双斜线 '//'
        $route = trim($route, '/');
        if(!preg_match('/^[a-z0-9_\-]+$/', $route) && false !== strpos($route, '//')) {
            return null;
        }

        if(false !== strpos($route, '/')) {
            list($id, $route) = explode('/', $route, 2);
        } else {
            $id = $route;
            $route = $this->defaultControllerId;
        }

        // 搜索顺序 模块控制器 -> 普通控制器
        if(isset($this->modules[$id])) {
            $c = trim($this->modules[$id], '\\') . '\\controllers\\' . ucfirst($route) . 'Controller';

            $this->moduleId = $id;
            $this->controllerId = $route;
            
            return $this->createObject($c);
        }

        // 普通控制器
        $this->controllerId = $id;

        return $this->createObject( $this->defaultControllerNamespace . '\\' . ucfirst($id) . 'Controller' );
    }

}
