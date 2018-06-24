<?php
/**
 * @author
 * @license MIT
 */
namespace fate\db;

use PDOException;
use Fate;
use fate\core\InvalidConfigException;
use fate\core\FileNotFoundException;

/**
 * 数据库
 */
final class Db {

    /**
     * @var array 数据库连接
     */
    private static $_links = [];

    /**
     * @var string 数据库命名空间
     */
    private static $_dbNamespace = 'y\\db';

    private function __construct(){}
    
    /**
     * 连接数据库
     *
     * @param string $dbFlag 数据库配置命名空间
     */
    public static function instance($dbFlag = '') {
        if(empty($dbFlag)) {
            throw new DbException('Empty param: dbFlag');
        }

        if(!isset(Fate::$app->db) || !isset(Fate::$app->db[$dbFlag])) {
            throw new InvalidConfigException('Unknow db config: ' . $dbFlag);
        }

        if( !isset(static::$_links[$dbFlag]) || null === static::$_links[$dbFlag] ){
            $config = Fate::$app->db[$dbFlag];
            $dsn = $config['dsn'];
            $driver = static::getDriverName($dsn);

            $dbClass = static::$_dbNamespace . '\\' . $driver . '\\Db';
            $dbFile = Fate::namespaceToNormal($dbClass);
            
            if(!is_file($dbFile)) {
                throw new FileNotFoundException('The Classfile: ' . $dbFile . ' not found');
            }

            try {
                static::$_links[$dbFlag] = new $dbClass(
                    $dsn
                    ,$config['username']
                    ,$config['password']);
                
                static::$_links[$dbFlag]->initConnection($config);    

            } catch(PDOException $e) {
                static::$_links[$dbFlag] = null;
                
                throw new DbException('Failed to connect to database');
            }
        }

        return static::$_links[$dbFlag];
    }

    /**
     * 得到驱动名
     *
     * @return string name of the DB driver
     */
    public static function getDriverName($dsn = '') {
        $driverName = '';
        if('' !== $dsn) {
            if(false !== ($pos = strpos($dsn, ':'))) {
                $driverName = strtolower(substr($dsn, 0, $pos));
            }
        }
        
        return $driverName;
    }
    
}
