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
     * Init
     *
     * @param array $config 配置信息
     */
    public function initConnection(& $config) {}

    /**
     * Create QueryBuilder
     */
    public abstract function createQuery();

    /**
     * Prepares a sql for execution
     *
     * @param string $sql
     * @return AbstractDb
     */
    public abstract function prepareSql($sql);

    /**
     * Prepares a sql statement for execution
     *
     * @param string $sql
     * @return AbstractDb
     */
    public abstract function prepareStatement($sql);

    /**
     * 绑定一个参数 只能用于绑定命名参数
     *
     * @param string $parameter
     * @param string $value
     * @return AbstractDb
     */
    public abstract function bindValue($parameter, $value);

    /**
     * 绑定多个参数 可以用于绑定命名参数和占位符参数
     *
     * @param array $parameters
     * @return AbstractDb
     */
    public abstract function bindValues($parameters);

    /**
     * 获取所有数据
     *
     * @return array | boolean 包含所有结果的数组 如果没有记录则返回一个空数组 发生错误时返回 false
     */
    public abstract function queryAll();

    /**
     * 获取一条数据
     *
     * @return array | boolean 结果集的第一行记录 没有记录时返回 false
     */
    public abstract function queryOne();

    /**
     * 获取单独一列的值
     *
     * @return string | boolean 结果集的第一行第一列记录 如果没有记录则返回 false
     */
    public abstract function queryColumn();

    /**
     * 执行 sql 修改语句
     *
     * @return integer 影响行数
     */
    public abstract function execute();

    /**
     * 关闭数据库连接
     */
    public abstract function close();

    /**
     * 获取最后插入的数据的 ID
     *
     * @param string $name
     */
    public abstract function getLastInsertId($name);

    /**
     * 获取执行的 sql 语句
     *
     * @return string
     */
    public abstract function getLastSql();

    /**
     * 开启事务
     *
     * @return boolean
     */
    public abstract function beginTransaction();

    /**
     * 提交事务
     *
     * @return boolean
     */
    public abstract function commitTransaction();

    /**
     * 回滚事务
     *
     * @return boolean
     */
    public abstract function rollbackTransaction();

}
