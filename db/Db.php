<?php
namespace y\db;

use Y;
use PDO;
use y\db\DbException;
use y\core\InvalidConfigException;
use PDOException;

final class Db {
	
	/**
	 * @var array 数据库连接
	 */
	protected static $_links = [];
	
	/**
	 * 连接数据库
	 *
	 * @param string $dbNamespace 数据库配置命名空间
	 */
	public static function instance($dbNamespace = '') {
		if(empty($dbNamespace)) {
			throw new DbException('Empty param: dbNamespace');
		}
		
		if(!isset(Y::$app->db[$dbNamespace])) {
			throw new InvalidConfigException('Unknow db config:' . $dbNamespace);
		}
		
		$config = Y::$app->db[$dbNamespace];
		$options = [
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \''. $config['charset'] .'\'',
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		];
		
		if( !isset(self::$_links[$dbNamespace]) || null === self::$$_links[$dbNamespace] ){
			try {
				self::$_links[$dbNamespace] = new PDO($config['dsn'], 
					$config['username'], 
					$config['password'], 
					$options);
					
			} catch(PDOException $e) {
				echo 'Connection failed: ' . $e->getMessage();
			}
		}
		
		return self::$_links[$dbNamespace];
	}
}

















