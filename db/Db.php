<?php
/**
 * @author
 * @license MIT
 */
namespace fate\db;

use Fate;
use fate\core\InvalidConfigException;

/**
 * 数据库
 *
 * 'db' => [
 *      'xxx' => [
 *          'dsn' => 'schema:host=HOST;dbname=DBNAME',
 *          'username' => '',
 *          'password' => '',
 *          'charset' => 'utf8'
 *      ],
 *      
 *      'slaves' => [
 *          [ 'dsn' => 'dsn for slave server 1', 'username' => 'slave1', 'password' => '' ],
 *          [ 'dsn' => 'dsn for slave server 2', 'username' => 'slave2', 'password' => '' ]
 *      ]
 * ]
 *
 */
final class Db {
    
    /**
     * @var string 数据库命名空间
     */
    public static $_dbNamespace = 'fate\\db';
    
    /**
     * @var Db the current active master db
     */
    private $_master = null;
    
    /**
     * @var Db the current active slave db
     */
    private $_slave = null;
    
    private function __construct(){}
    
    /**
     * 连接数据库
     *
     * @param string $flag 数据库配置名
     */
    public static function instance($flag = '') {
        if(!isset(Fate::$app->db) || !isset(Fate::$app->db[$flag])) {
            throw new InvalidConfigException('Db config not found: ' . $flag);
        }
        
        if('slaves' === $flag) {
            return $this->getSlave($flag);
        }
        
        return $this->getMaster($flag);
    }
    
    /**
     * 获取一个主库连接
     *
     * @return Db
     */
    public function getMaster($flag) {
        if(null === $this->_master) {
            $config = Fate::$app->db[$flag];
            
            $this->_master = $this->connect($config);
            
            if(null === $this->_master) {
                throw new DbException('Failed to connect to database');
            }
        }

        return $this->_master;
    }
    
    /**
     * 获取一个从库连接
     *
     * @return Db
     */
    public function getSlave($flag) {
        if(null === $this->_slave) {
            $this->_slave = $this->openFromPool(Fate::$app->db[$flag]);
        }
        
        return $this->_slave;
    }
    
    /**
     * 根据配置打开一个连接
     *
     * @param array $pool
     */
    public function openFromPool(array $pool) {
        shuffle($pool);
        
        $db = null;
        
        foreach($pool as $conf) {
            $db = $this->connect($conf);
            
            if(null !== $db) {
                break;
            }
        }
        
        return $db;
    }
    
    /**
     * 打开一个连接
     *
     * @param array $config
     * @return \fate\db\AbstractDb | null
     */
    public function connect($config) {
        $db = null;
        
        $dsn = $config['dsn'];
        $driver = $this->getDriverName($dsn);
        
        $DbClass = static::$_dbNamespace . '\\' . $driver . '\\Db';
        $dbFile = Fate::namespaceToNormal($DbClass);
        
        if(!is_file($dbFile)) {
            throw new DbException('The driver class not found: ' . $dbFile);
        }

        try {
            $db = new $DbClass(
                $dsn
                ,$config['username']
                ,$config['password']);
            
            $db->initConnection($config);

        } catch(\PDOException $e) {
            $db = null;
        }
        
        return $db;
    }
    
    /**
     * 得到驱动名
     *
     * @return string name of the DB driver
     */
    public function getDriverName($dsn = '') {
        $driverName = '';
        
        if('' !== $dsn) {
            if(false !== ($pos = strpos($dsn, ':'))) {
                $driverName = strtolower(substr($dsn, 0, $pos));
            }
        }
        
        return $driverName;
    }
    
}
