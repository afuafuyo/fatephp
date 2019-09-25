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
     * @return $this
     */
    public abstract function prepareSql($sql);

    /**
     * Prepares a sql statement for execution
     *
     * @param string $param
     * @return $this
     */
    public abstract function prepareStatement($sql);

    /**
     * 绑定一个参数 只能用于绑定命名参数
     *
     * @param string $param
     * @param string $value
     * @return $this
     */
    public abstract function bindValue($param, $value);

    /**
     * 绑定多个参数 可以用于绑定命名参数和占位符参数
     *
     * @param array $params
     * @return $this
     */
    public abstract function bindValues($params);

    /**
     * 获取所有数据
     *
     * @return array 结果数组
     */
    public abstract function queryAll();

    /**
     * 获取一条数据
     *
     * @return array | boolean 结果数组
     */
    public abstract function queryOne();

    /**
     * 获取单独一列的值
     *
     * @return string | boolean
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

}
