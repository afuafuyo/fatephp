<?php
namespace y\db\mysql;

use PDO;

class Db extends \y\db\ImplDb {

    public function __construct($dsn, $username, $password, $options = []) {
        $o = array_merge($options, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        parent::__construct($dsn, $username, $password, $o);
    }
    
    public function initConnection(){}
}