<?php
namespace y\db;

/**
 * pdo 基类
 */
class ImplDb extends \PDO implements \y\db\IDb {

    public function __construct($dsn, $username, $password, $options = []){
        parent::__construct($dsn, $username, $password, $options);
    }
    
    public function initConnection(){}
}
