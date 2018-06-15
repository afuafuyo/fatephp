<?php
/**
 * @author
 * @license MIT
 */
namespace y\web;

use Y;
use y\core\InvalidCallException;

/**
 * 应用前端控制器
 */
class Application extends \y\core\Application {

    /**
     * @var string 异常处理类
     */
    public $errorHandler = 'y\\web\\ErrorHandler';

    /**
     * 运行应用
     *
     * @throws InvalidCallException 方法未找到
     * @return array | null
     */
    public function run() {
        $route = Request::getInstance()->getQueryString($this->defaultRouteParam);
        
        $controller = $this->createController($route);
        
        if(null === $controller) {
            throw new InvalidCallException('The route is invalid: ' . $route);
        }
        
        if(!method_exists($controller, 'runControllerAction')) {
            // 单一入口
            return $controller->run();
        }
        
        return $controller->runControllerAction();
    }

    /**
     * {@inheritDoc}
     */
    public function errorHandler() {
        $handler = Y::createObject($this->errorHandler);
        
        $handler->register();
    }

}
