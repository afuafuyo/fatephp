<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\db\mysql;

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
 *          ],
 *          'prefix'=> ''
 *      ]
 * ]
 *
 */
class Db extends \y\db\ImplDb {
    
    use \y\db\DbOperationTrait;
    
    /**
     * @var int 操作类型
     */
    private $_operate = 0;
    
    /**
     * @var string sql 语句
     */
    private $_sql = '';
    
    /**
     * @var array 数据
     */
    private $_data = [];
    
    /**
     * @var array 操作
     */
    private $_options = [];
    
    /**
     * @var string 表前缀
     */
    private $_tablePrefix;

    public function __construct($dsn, $username, $password) {
        $this->pdo = new PDO($dsn, $username, $password);
    }
    
    private function reset() {
        $this->_operate = 0;
        $this->_sql = '';
        $this->_data = [];
        $this->_options = [];
    }
    
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
        
        if(isset($config['prefix'])) {
            $this->_tablePrefix = $config['prefix'];
        }
    }
    
    private function closeStatement() {
        $this->pdoStatement->closeCursor();
        $this->pdoStatement = null;
    }
    
    private function closePdo() {
        $this->pdo = null;
    }
    
    private function buildCols() {
        $ret = implode('`, `', array_keys($this->_data));
        
        return '' === $ret ? '()' : '(`' . $ret . '`)';
    }
    
    private function buildValues() {
        $ret = implode('\', \'', array_values($this->_data));
        
        return '' === $ret ? '()' : '(\'' . $ret . '\')';
    }
    
    private function buildSet() {
        $ret = '';
        foreach($this->_data as $k => $v) {
            $ret .= "`{$k}`='{$v}',";
        }
        
        return '' === $ret ? '' : 'SET ' . rtrim($ret, ',');
    }
    
    private function initSql() {
        $sql = '';
        switch($this->_operate) {
            case self::$INSERT :
                // insert into t() values()
                $table = isset($this->_options['table']) ? $this->_options['table'] : '';
                $cols = $this->buildCols();
                $values = $this->buildValues();
                $sql = 'INSERT INTO ' . $table . $cols . ' VALUES' . $values;
                
                break;
            
            case self::$DELETE :
                // delete from t where x
                $table = isset($this->_options['table']) ? $this->_options['table'] : '';
                
                $where = isset($this->_options['where']) ?
                    ' WHERE ' . $this->_options['where'] : '';
                
                if('' !== $where) {
                    $sql = 'DELETE FROM ' . $table . $where;
                }
                
                break;
            
            case self::$UPDATE :
                // update t set x=y, a=b where x
                $table = isset($this->_options['table']) ? $this->_options['table'] : '';
                $set = $this->buildSet();
                
                $where = isset($this->_options['where']) ?
                    ' WHERE ' . $this->_options['where'] : '';
                
                if('' !== $where) {
                    $sql = 'UPDATE '. $table . ' ' . $set . $where;
                }
                
                break;
            
            case self::$SELECT :
            case self::$SELECTONE :
                // select * from t
                // where x
                // group by x
                // having x
                // order by x
                // limit x
                $fields = isset($this->_options['fields']) ? $this->_options['fields'] : '*';
                $table = isset($this->_options['table']) ? $this->_options['table'] : '';
                
                $where = isset($this->_options['where']) ?
                    ' WHERE ' . $this->_options['where'] : '';
                $groupBy = isset($this->_options['groupBy']) ?
                    ' GROUP BY ' . $this->_options['groupBy'] : '';
                $having = isset($this->_options['having']) ?
                    ' HAVING ' . $this->_options['having'] : '';
                $orderBy = isset($this->_options['orderBy']) ?
                    ' ORDER BY ' . $this->_options['orderBy'] : '';
                $limit = self::$SELECTONE === $this->_operate ?
                    ' LIMIT 0,1' :
                    (isset($this->_options['limit']) ?
                        ' LIMIT ' . $this->_options['limit'] : '');
                
                $sql = 'SELECT ' . $fields . ' FROM ' . $table .
                    $where .
                    $groupBy .
                    $having .
                    $orderBy .
                    $limit;
                    
                break;
            
            case self::$COUNT :
                $table = isset($this->_options['table']) ? $this->_options['table'] : '';
                $field = isset($this->_options['countField']) ? $this->_options['countField'] : '*';
                $where = isset($this->_options['where']) ?
                    ' WHERE ' . $this->_options['where'] : '';
            
                $sql = $this->_sql = "SELECT COUNT({$field}) FROM `{$table}` {$where}";
            
                break;
            
            default :
                break;
        }
        
        // 重置条件
        $this->reset();
        
        return $sql;
    }
    
    /**
     * 返回上一次执行的 sql 语句
     */
    public function getLastSql() {
        return $this->_sql;
    }
    
    public function fields($fields) {
        $this->_options['fields'] = $fields;
        
        return $this;
    }
    
    public function table($table) {
        $this->_options['table'] = $this->_tablePrefix . $table;
        
        return $this;
    }
    
    public function where($condition) {
        $this->_options['where'] = $condition;
        
        return $this;
    }
    
    public function orderBy($order) {
        $this->_options['orderBy'] = $order;
        
        return $this;
    }
    
    /**
     * 限制条数
     *
     * @param string | int $limit
     */
    public function limit($limit) {
        $this->_options['limit'] = $limit;
        
        return $this;
    }
    
    public function insert(& $data) {
        $this->_operate = self::$INSERT;
        $this->_data = $data;
        $sql = $this->_sql = $this->initSql();
        $this->executeSql($sql);
        
        return $this->pdo->lastInsertId();
    }
    
    public function delete() {
        $this->_operate = self::$DELETE;
        $sql = $this->_sql = $this->initSql();

        return $this->executeSql($sql);
    }
    
    public function update(& $data) {
        $this->_operate = self::$UPDATE;
        $this->_data = $data;
        $sql = $this->_sql = $this->initSql();
        
        return $this->executeSql($sql);
    }
    
    public function getAll() {
        $this->_operate = self::$SELECT;
        $sql = $this->_sql = $this->initSql();
        $data = $this->querySql($sql);
        
        return $data;
    }
    
    public function getOne() {
        $this->_operate = self::$SELECTONE;
        $sql = $this->_sql = $this->initSql();
        
        $stat = $this->prepareStatement($sql);
        
        return $stat->fetch();
    }
    
    /**
     * 获取记录数
     *
     * @param string $field 列
     * @return int 结果
     */
    public function count($field = '*') {
        $this->_operate = self::$COUNT;
        $this->_options['countField'] = $field;
        $sql = $this->_sql = $this->initSql();
        
        $stat = $this->prepareStatement($sql);
        
        return $stat->fetchColumn();
    }
    
    /**
     * 执行 sql 语句 生成 PDOStatement 对象
     *
     * @param string $sql sql 语句
     * @param array $params 参数
     * @return \PDOStatement
     */
    public function prepareStatement($sql, $params = null) {
        if(null === $params) {
            $this->pdoStatement = $this->pdo->query($sql);
        
        } else if(is_array($params)){
            $this->pdoStatement = $this->pdo->prepare($sql);
            $this->pdoStatement->execute($params);
        }
        
        return $this->pdoStatement;
    }
    
    /**
     * 执行 sql 语句
     *
     * @param string $sql sql 语句
     * @return array 结果数组
     */
    public function querySql($sql) {
        $this->trigger(self::EVENT_BEFORE_QUERY, $this);
        $this->prepareStatement($sql);
        $data = $this->pdoStatement->fetchAll();
        $this->closeStatement();
        $this->trigger(self::EVENT_AFTER_QUERY, $this);
        
        return $data;
    }
    
    /**
     * 执行 sql 语句
     *
     * @param string $sql sql 语句
     * @return int 影响行数
     */
    public function executeSql($sql) {
        $this->trigger(self::EVENT_BEFORE_QUERY, $this);
        $ret = $this->pdo->exec($sql);
        $this->trigger(self::EVENT_AFTER_QUERY, $this);
        
        return $ret;
    }
    
}
