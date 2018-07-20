<?php
/**
 * @author
 * @license MIT
 */
namespace fate\db;

/**
 * PDO 基类
 */
abstract class AbstractDb extends \fate\core\Event {

    const EVENT_BEFORE_QUERY = 1;
    const EVENT_AFTER_QUERY = 2;
    const EVENT_BEFORE_EXECUTE = 3;
    const EVENT_AFTER_EXECUTE = 4;

    /**
     * @var \PDO pdo 类实例
     */
    public $pdo = null;
    
    /**
     * @var \PDOStatement PDOStatement 类实例
     */
    public $pdoStatement = null;
    
    /**
     * 初始化操作
     *
     * @param array $config 配置信息
     */
    public function initConnection(& $config) {}
    
    /**
     * 获取单独一列的值
     */
    public abstract function queryColumn();
    
    /**
     * 创建查询生成器
     */
    public abstract function createQuery();
    
    /**
     * 获取执行的 sql 语句
     *
     * @return string
     */
    public abstract function getLastSql();

}
