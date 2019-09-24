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

        if( !($controller instanceof Controller) ) {
            // 单一入口
            return $controller->run();
        }

        return $controller->runControllerAction();
    }

    /**
     * {@inheritdoc}
     */
    public function handlerError() {
        $handler = new ErrorHandler();

        $handler->register();
    }

}
