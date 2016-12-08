<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\web;

use Y;
use y\core\InvalidCallException;

/**
 * 应用前端控制器
 */
class Application extends \y\core\Application {
    use \y\web\AppServiceTrait;

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
     * 异常处理
     */
    public function errorHandler() {
        $handler = Y::createObject($this->errorHandler);
        
        $handler->register();
    }
}
