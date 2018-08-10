<?php
/**
 * @author
 * @license MIT
 */
namespace fate\web;

use fate\core\InvalidCallException;

/**
 * 应用前端控制器
 */
class Application extends \fate\core\Application {

    /**
     * @var string 异常处理类
     */
    public $errorHandler = '';

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
     * {@inheritdoc}
     */
    public function errorHandler() {
        $handler = new ErrorHandler();
        
        $handler->register();
    }

}
