<?php
/**
 * @author
 * @license MIT
 */
namespace fate\db\mysql;

use PDO;

/**
 * mysql 驱动类
 *
 * 'db' => [
 *      'main' => [
 *          'dsn' => 'mysql:host=HOST;dbname=DBNAME',
 *          'username' => '',
 *          'password' => '',
 *          'charset' => 'utf8',
 *          'attributes' => [
 *              PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
 *          ]
 *      ]
 * ]
 *
 */
class Db extends \fate\db\AbstractDb {

    /**
     * @var array 待绑定的参数 可以是索引数组或关联数组
     */
    public $bindingParams = [];

    /**
     * @var string sql string
     */
    public $sqlString = '';

    public function __construct($dsn, $username, $password) {
        $this->pdo = new PDO($dsn, $username, $password);
    }

    /**
     * {@inheritdoc}
     * @return Query
     * @see \fate\db\AbstractDb::createQuery()
     */
    public function createQuery() {
        return new Query($this);
    }

    /**
     * {@inheritdoc}
     * @see \fate\db\AbstractDb::initConnection()
     */
    public function initConnection(& $config) {
        $charset = isset($config['charset']) ? $config['charset'] : 'utf8';
        $this->pdo->exec('SET NAMES \''. $charset .'\'');

        /**
         * http://www.php.net/manual/en/function.PDO-setAttribute.php
         * for details about available attributes.
         */
        if(isset($config['attributes'])) {
            foreach($config['attributes'] as $key => $val) {
                $this->pdo->setAttribute($key, $val);
            }

        } else {
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
    }

    private function closePdo() {
        $this->pdo = null;
    }

    /**
     * {@inheritdoc}
     * @see \fate\db\AbstractDb::prepareSql()
     */
    public function prepareSql($sql) {
        $this->sqlString = $sql;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see \fate\db\AbstractDb::prepareStatement()
     */
    public function prepareStatement($sql) {
        $this->sqlString = $sql;

        $this->pdoStatement = $this->pdo->prepare($sql);

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see \fate\db\AbstractDb::bindValue()
     */
    public function bindValue($param, $value) {
        $this->bindingParams[$param] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see \fate\db\AbstractDb::bindValues()
     */
    public function bindValues($params) {
        foreach($params as $k => $v) {
            $this->bindingParams[$k] = $v;
        }

        return $this;
    }

    /**
     * @return void
     */
    private function makeStatement() {
        // simple sql query
        if(null === $this->pdoStatement) {
            $this->pdoStatement = $this->pdo->query($this->sqlString);

            return;
        }

        // prepared sql query
        if( empty($this->bindingParams) ) {
            $this->pdoStatement->execute();

        } else {
            $this->pdoStatement->execute($this->bindingParams);
        }
    }

    /**
     * {@inheritdoc}
     * @see \fate\db\AbstractDb::queryAll()
     */
    public function queryAll() {
        $this->trigger(self::EVENT_BEFORE_QUERY, $this);

        $this->makeStatement();
        $data = $this->pdoStatement->fetchAll();
        $this->close();

        $this->trigger(self::EVENT_AFTER_QUERY, $this);

        return $data;
    }

    /**
     * {@inheritdoc}
     * @see \fate\db\AbstractDb::queryOne()
     */
    public function queryOne() {
        $this->trigger(self::EVENT_BEFORE_QUERY, $this);

        $this->makeStatement();
        $data = $this->pdoStatement->fetch();
        // make sure fetch end so can fetch other result
        // while(false !== $this->pdoStatement->fetch()) {}
        $this->close();

        $this->trigger(self::EVENT_AFTER_QUERY, $this);

        return $data;
    }

    /**
     * {@inheritdoc}
     * @see \fate\db\AbstractDb::execute()
     */
    public function execute() {
        $rows = 0;

        $this->trigger(self::EVENT_BEFORE_EXECUTE, $this);

        // simple sql query
        if( null === $this->pdoStatement || empty($this->bindingParams) ) {
            $rows = $this->pdo->exec($this->sqlString);

        } else {
            $this->pdoStatement->execute($this->bindingParams);

            $rows = $this->pdoStatement->rowCount();
        }

        $this->close();

        $this->trigger(self::EVENT_AFTER_EXECUTE, $this);

        return $rows;
    }

    /**
     * {@inheritdoc}
     * @see \fate\db\AbstractDb::close()
     */
    public function close() {
        if(null !== $this->pdoStatement) {
            $this->pdoStatement->closeCursor();
            $this->pdoStatement = null;
        }

        $this->bindingParams = [];
    }

    /**
     * {@inheritdoc}
     * @see \fate\db\AbstractDb::queryColumn()
     */
    public function queryColumn() {
        $this->trigger(self::EVENT_BEFORE_QUERY, $this);

        $this->makeStatement();
        $data = $this->pdoStatement->fetchColumn();
        $this->close();

        $this->trigger(self::EVENT_AFTER_QUERY, $this);

        return $data;
    }

    /**
     * 转换 Query 对象
     *
     * @param Query $query
     * @return $this
     */
    public function buildQuery($query) {
        // simple query
        if(empty($query->params)) {
            $this->prepareSql($query->sqlString);

        } else {
            $this->prepareStatement($query->sqlString);
            $this->bindValues($query->params);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see \fate\db\AbstractDb::getLastInsertId()
     */
    public function getLastInsertId($name) {
        return $this->pdo->lastInsertId($name);
    }

    /**
     * {@inheritdoc}
     * @see \fate\db\AbstractDb::getLastSql()
     */
    public function getLastSql() {
        return $this->sqlString;
    }

    /**
     * @var integer the nesting level of the transaction
     */
    private $_level = 0;

    /**
     * {@inheritdoc}
     */
    public function beginTransaction() {
        if(0 === $this->_level) {
            $this->pdo->beginTransaction();
            $this->_level = 1;

            return true;
        }

        $this->_level++;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function commitTransaction() {
        $this->_level--;

        if(0 === $this->_level) {
            $this->pdo->commit();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rollbackTransaction()  {
        $this->_level--;

        if(0 === $this->_level) {
            $this->pdo->rollBack();
        }

        return true;
    }

}
