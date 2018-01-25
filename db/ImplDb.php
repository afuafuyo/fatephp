<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\db;

use y\core\InvalidCallException;

/**
 * pdo 基类
 */
abstract class ImplDb extends \y\core\Event implements IDb {

    const EVENT_BEFORE_QUERY = 'beforeQuery';
    
    const EVENT_AFTER_QUERY = 'afterQuery';

    /**
     * @var PDO pdo 类实例
     */
    public $pdo = null;
    
    /**
     * @var PDOStatement PDOStatement 类实例
     */
    public $pdoStatement = null;
    
    /**
     * 初始化操作
     *
     * @param array $config 配置信息
     */
    public function initConnection(& $config){}
    
    /**
     * __call
     *
     * @param string $name 方法名字
     * @param array $params 方法参数
     */
    public function __call($name, $params) {
        if(method_exists($this->pdo, $name)) {
            return call_user_func_array([$this->pdo, $name], $params);
        }
        
        throw new InvalidCallException('Calling unknown method: ' . get_class($this) . "::$name()");
    }

}
