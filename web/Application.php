<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
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
    public $defaultRouteParam = 'r';

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
     * @var Object 异常处理类
     */
    public $errorHandler = 'y\\web\\ErrorHandler';

    /**
     * @var array 注册的模块
     */
    public $modules = [];

    /**
     * @var string 当前的控制器
     */
    public $controllerId;

    /**
     * @var string 当前的模块
     */
    public $moduleId;

    /**
     * @var string 路由前缀
     */
    public $routePrefix;

    /**
     * 运行应用
     *
     * @throws InvalidCallException 方法未找到
     * @return array | null
     */
    public function run(){
        $route = Request::getInstance()->getParam($this->defaultRouteParam);
        $controller = $this->createController($route);

        if(!method_exists($controller, 'execute')) {
            throw new InvalidCallException('The Controller\'s execute() method not found');
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
        if('' === $route || '/' === $route) {
            $route = $this->defaultRoute;
        }

        // 检测非法 与 路径中不能有双斜线 '//'
        $route = trim($route, '/');
        if(!preg_match('/^[\w\-]+$/', $route) && false !== strpos($route, '//')) {
            return null;
        }

        if(false !== strpos($route, '/')) {
            list($id, $route) = explode('/', $route, 2);
        } else {
            $id = $route;
            $route = $this->defaultControllerId;
        }

        $prefix = $id;
        if( false !== ($pos = strrpos($route, '/')) ) {
            $prefix .= '/' . substr($route, 0, $pos);
            $route = substr($route, $pos + 1);
            $prefix = str_replace('/', '\\', $prefix);
        }

        // 保存当前控制器标示
        $this->controllerId = $route;
        $this->routePrefix = $prefix;

        // 搜索顺序 模块控制器 -> 普通控制器
        if(isset($this->modules[$id])) {
            $clazz = trim($this->modules[$id], '\\') . '\\controllers\\' . $prefix . '\\' . ucfirst($route) . 'Controller';
            $this->moduleId = $id;
            
            return $this->createObject($clazz);
        }

        return $this->createObject( $this->defaultControllerNamespace . '\\' . $prefix . '\\' . ucfirst($route) . 'Controller' );
    }
}
