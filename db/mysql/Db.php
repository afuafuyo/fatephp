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
 * eg.
 * 
 * $db = Db::instance('xxx');
 *
 * $data = $db->prepareSql('select name from xxx where id = :id limit 0, 1')->bindValue(':id', 1)->queryOne();
 *
 * $data = $db->prepareSql('select title from xxx')->queryAll();
 *
 * $n = $db->prepareSql('update xxx set username = :name')->bindValue(':name', "li's")->execute();
 *
 * 查询生成器使用
 *
 * $data = $db->createQuery()->select('id,title')->from('xxx')->getAll();
 *
 * $data = $db->createQuery()->select('id,title')->from('xxx')->where('id = :id', [':id' => 1])->getOne();
 *
 * $n = $db->createQuery()->from('t_nav')->where('id > 2')->count('id');
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
   
    private function closeStatement() {
        $this->pdoStatement->closeCursor();
        $this->pdoStatement = null;
        
        $this->bindingParams = [];
    }
    
    private function closePdo() {
        $this->pdo = null;
    }
    
    /**
     * Prepares a sql for execution
     *
     * @param string $sql
     * @return $this
     */
    public function prepareSql($sql) {
        $this->sqlString = $sql;
        
        $this->pdoStatement = $this->pdo->prepare($sql);
        
        return $this;
    }
    
    /**
     * 绑定一个参数 只能用于绑定命名参数
     *
     * @param string $param
     * @param string $value
     * @return $this
     */
    public function bindValue($param, $value) {
        $this->bindingParams[$param] = $value;
        
        return $this;
    }
    
    /**
     * 绑定多个参数 可以用于绑定命名参数和占位符参数
     *
     * @param array $params
     * @return $this
     */
    public function bindValues($params) {
        foreach($params as $i => $v) {
            $this->bindingParams[$i] = $v;
        }
        
        return $this;
    }
    
    /**
     * 获取所有数据
     *
     * @return array 结果数组
     */
    public function queryAll() {
        $this->trigger(self::EVENT_BEFORE_QUERY, $this);
        
        if( empty($this->bindingParams) ) {
            $this->pdoStatement->execute();
            
        } else {
            $this->pdoStatement->execute($this->bindingParams);
        }
     
        $data = $this->pdoStatement->fetchAll();
        
        $this->closeStatement();
        
        $this->trigger(self::EVENT_AFTER_QUERY, $this);
        
        return $data;
    }
    
    /**
     * 获取一条数据
     *
     * @return array 结果数组
     */
    public function queryOne() {
        $this->trigger(self::EVENT_BEFORE_QUERY, $this);
        
        if( empty($this->bindingParams) ) {
            $this->pdoStatement->execute();
            
        } else {
            $this->pdoStatement->execute($this->bindingParams);
        }
        
        $data = $this->pdoStatement->fetch();
        
        // make sure fetch end so can fetch other result
        // while(false !== $this->pdoStatement->fetch()) {}
        
        $this->closeStatement();
        
        $this->trigger(self::EVENT_AFTER_QUERY, $this);
        
        return $data;
    }
    
    /**
     * 执行 sql 修改语句
     *
     * @return int 影响行数
     */
    public function execute() {
        $rows = 0;
        
        $this->trigger(self::EVENT_BEFORE_EXECUTE, $this);
        
        if( empty($this->bindingParams) ) {
            $rows = $this->pdo->exec($this->sqlString);
            
        } else {
            $this->pdoStatement->execute($this->bindingParams);
            
            $rows = $this->pdoStatement->rowCount();
        }
        
        $this->closeStatement();
        
        $this->trigger(self::EVENT_AFTER_EXECUTE, $this);
        
        return $rows;
    }
    
    /**
     * {@inheritdoc}
     * @see \fate\db\AbstractDb::queryColumn()
     */
    public function queryColumn() {
        if( empty($this->bindingParams) ) {
            $this->pdoStatement->execute();
            
        } else {
            $this->pdoStatement->execute($this->bindingParams);
        }
        
        $data = $this->pdoStatement->fetchColumn();
        
        $this->closeStatement();
        
        return $data;
    }
    
    /**
     * 转换 Query 对象
     *
     * @param Query $query
     * @return $this
     */
    public function buildQuery($query) {
        $this->prepareSql($query->sqlString)->bindValues($query->params);
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     * @see \fate\db\AbstractDb::getLastSql()
     */
    public function getLastSql() {
        return $this->sqlString;
    }
}
