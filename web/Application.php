<?php
/**
 * @author
 * @license MIT
 */
namespace fate\web;

use Fate;
use fate\http\Request;
use fate\core\InvalidCallException;

/**
 * 应用前端控制器
 */
class Application extends \fate\core\Application {

    /**
     * @property string | array 拦截所有路由
     *
     * 'some\namespace\Class'
     *
     * or a array config
     *
     * [
     *      'classPath' => 'some\namespace\Class',
     *      'property' => 'value'
     * ]
     *
     */
    public $interceptAll = null;

    /**
     * @property array 实现路由到控制器转换配置
     *
     * [
     *      'u' => 'app\controllers\user\IndexController',
     *      'account' => [
     *         'classPath' => 'app\controllers\user\IndexController',
     *         'property' => 'value'
     *     ]
     * ]
     *
     */
    public $routesMap = null;

    /**
     * @property array 注册的模块
     */
    public $modules = null;

    /**
     * @property string 路由标识
     */
    public $defaultRouteParameter = 'r';

    /**
     * @property string 默认路由
     */
    public $defaultRoute = 'index/index';

    /**
     * @property string 默认控制器命名空间
     */
    public $defaultControllerNamespace = 'app\\controllers';

    /**
     * @property string 默认控制器
     */
    public $defaultControllerId = 'index';

    /**
     * @property string 当前的模块
     */
    public $moduleId = '';

    /**
     * @property string 当前的控制器
     */
    public $controllerId = '';

    /**
     * @property string 前缀目录
     */
    public $viewPath = '';

    /**
     * @property string 布局文件路径
     */
    public $layout = '';

    /**
     * 运行应用
     *
     * @throws InvalidCallException 方法未找到
     * @return array | null
     */
    public function run() {
        $route = Request::getInstance()->getQueryString($this->defaultRouteParameter);

        $controller = $this->createController($route);

        if(null === $controller) {
            throw new InvalidCallException('The route is invalid: ' . $route);
        }

        if( !($controller instanceof Controller) ) {
            // 单一入口
            return $controller->run();
        }

        return $controller->runControllerAction();
    }

    /**
     * 创建控制器
     *
     * @param string $route 路由
     * @return Object 控制器
     */
    public function createController($route) {
        $route = trim($route, '/');

        if('' === $route || '/' === $route) {
            $route = $this->defaultRoute;
        }

        // 路径中不能有双斜线 '//'
        if(false !== strpos($route, '//')) {
            return null;
        }

        // 拦截路由
        if(null !== $this->interceptAll) {
            return Fate::createObject($this->interceptAll);
        }

        // 解析路由
        // 目录前缀或模块 id
        $id = '';
        $pos = strpos($route, '/');
        if(false !== $pos) {
            $id = substr($route, 0, $pos);
            $route = substr($route, $pos + 1);
            $this->controllerId = $route;

        } else {
            $id = $route;
            $route = '';
        }

        // 保存前缀
        $this->viewPath = $id;

        // 保存当前控制器标识
        if( false !== ($pos = strrpos($route, '/')) ) {
            $this->viewPath = $this->viewPath . '/' . substr($route, 0, $pos);
            $this->controllerId = substr($route, $pos + 1);
            $this->viewPath = str_replace('/', '\\', $this->viewPath);  // namespace path
        }
        if('' === $this->controllerId) {
            $this->controllerId = $this->defaultControllerId;
        }

        // 搜索顺序 配置 -> 模块控制器 -> 普通控制器
        // 模块没有前缀目录
        $clazz = null;
        if(null !== $this->routesMap && isset($this->routesMap[$id])) {
            return Fate::createObject($this->routesMap[$id]);
        }

        if(null !== $this->modules && isset($this->modules[$id])) {
            $this->moduleId = $id;

            $clazz = trim($this->modules[$id], '\\')
                . '\\controllers\\'
                . ucfirst($this->controllerId) . 'Controller';

            return Fate::createObject($clazz);
        }

        $clazz = $this->defaultControllerNamespace
            . '\\'
            . $this->viewPath
            . '\\'
            . ucfirst($this->controllerId) . 'Controller';

        return Fate::createObject($clazz);
    }

    /**
     * {@inheritdoc}
     */
    public function handlerError() {
        $handler = new ErrorHandler();

        $handler->register();
    }

}
