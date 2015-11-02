<?php
namespace y\db;

/**
 * 数据库接口类
 */
interface IDb {

    public function __construct($dsn, $username, $password, $options = []);
    
    public function initConnection();
}
