<?php
/**
 * @author
 * @license MIT
 */
namespace y\core;

use Y;

/**
 * 核心类
 */
class Fate extends Object {
    
    /**
     * @var string | array 拦截所有路由
     *
     * 'app\some\Class'
     *
     * or a array config
     *
     * [
     *      'class' => 'app\some\Class',
     *      'property' => 'value'
     * ]
     *
     */
    public $interceptAll = null;
    
    /**
     * @var array 实现路由到控制器转换配置
     *
     * [
     *      'u' => 'app\controllers\user\IndexController',
     *      'account' => [
     *         'class' => 'app\controllers\user\IndexController',
     *         'property' => 'value'
     *     ]
     * ]
     *
     */
    public $routesMap = null;

    /**
     * @var array 注册的模块
     */
    public $modules = null;

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
    public $moduleId = '';

    /**
     * @var string 当前的控制器
     */
    public $controllerId = '';

    /**
     * @var string 前缀目录
     */
    public $subRoute = '';

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
        
        // 检测非法 与 路径中不能有双斜线 '//'
        if(0 === preg_match('/^[\w\-\/]+$/', $route) || false !== strpos($route, '//')) {
            return null;
        }
        
        // 拦截路由
        if(null !== $this->interceptAll) {
            return Y::createObject($this->interceptAll);
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
        $this->subRoute = $id;
        
        // 保存当前控制器标识
        if( false !== ($pos = strrpos($route, '/')) ) {
            $this->subRoute = $this->subRoute . '/' . substr($route, 0, $pos);
            $this->controllerId = substr($route, $pos + 1);
            $this->subRoute = str_replace('/', '\\', $this->subRoute);  // namespace path
        }
        if('' === $this->controllerId) {
            $this->controllerId = $this->defaultControllerId;
        }
        
        // 搜索顺序 配置 -> 模块控制器 -> 普通控制器
        // 模块没有前缀目录
        $clazz = null;
        if(null !== $this->routesMap && isset($this->routesMap[$id])) {
            
            return Y::createObject($this->routesMap[$id]);
        }
        
        if(null !== $this->modules && isset($this->modules[$id])) {
            $this->moduleId = $id;
            
            $clazz = trim($this->modules[$id], '\\')
                . '\\controllers\\'
                . ucfirst($this->controllerId) . 'Controller';
            
            return Y::createObject($clazz);
        }
        
        $clazz = $this->defaultControllerNamespace
            . '\\'
            . $this->subRoute
            . '\\'
            . ucfirst($this->controllerId) . 'Controller';
        
        return Y::createObject($clazz);
    }

}
