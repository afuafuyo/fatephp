<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\web;

use y\core\InvalidCallException;

/**
 * 应用前端控制器
 */
class Application extends \y\core\Application {
    use \y\core\AppServiceTrait;

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
}
