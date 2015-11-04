<?php
namespace y\db;

use PDOException;
use Y;
use y\db\DbException;
use y\core\InvalidConfigException;
use y\core\FileNotFoundException;

final class DbFactory {

    /**
     * @var array 数据库连接
     */
    private static $_links = [];

    /**
     * @var string 数据源
     */
    private static $_dsn = null;
    
    /**
     * @var array PDO attributes [name => value]
     * [PHP manual](http://www.php.net/manual/en/function.PDO-setAttribute.php) for
     * details about available attributes.
     */
    public static $attributes = [];
    
    /**
     * @var string 数据库命名空间
     */
    private static $_dbNamespace = 'y\db';

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

        if(!isset(Y::$app->db[$dbFlag])) {
            throw new InvalidConfigException('Unknow db config:' . $dbFlag);
        }

        if( !isset(static::$_links[$dbFlag]) || null === static::$_links[$dbFlag] ){
            $config = Y::$app->db[$dbFlag];
            static::$_dsn = $config['dsn'];
            $driver = static::getDriverName();

            $dbClass = static::$_dbNamespace . '\\' . $driver . '\\Db';
            $dbFile = Y::namespaceToFile($dbClass);

            try {
                if(!is_file($dbFile)) {
                    throw new FileNotFoundException('The class File: ' . $dbFile . ' not found');
                }

                static::$_links[$dbFlag] = new $dbClass($config['dsn'] 
                    ,$config['username']
                    ,$config['password']
                    /*,static::$attributes*/);
                
                static::$_links[$dbFlag]->initConnection($config);    

            } catch(FileNotFoundException $e) {
                echo 'Connection failed: ' . $e->getMessage();

            } catch(PDOException $e) {
                echo 'PDOException: ' . $e->getMessage();
            }
        }

        return self::$_links[$dbFlag];
    }
	
	/**
     * 得到驱动名
     *
     * @return string name of the DB driver
     */
    public static function getDriverName() {
        $driverName = '';
        if(null !== static::$_dsn) {
            if(false !== ($pos = strpos(static::$_dsn, ':'))) {
                $driverName = strtolower(substr(static::$_dsn, 0, $pos));
            }
        }
        
        return $driverName;
    }
}
