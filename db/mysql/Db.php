<?php
namespace y\db\mysql;

use PDO;

class Db extends \y\db\ImplDb {

    public function __construct($dsn, $username, $password, $options = []) {
        $options = array_merge($options, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        $this->pdo = new PDO($dsn, $username, $password, $options);
    }
    
    public function initConnection(& $config) {
        if(isset($config['charset'])) {
            $this->pdo->exec('SET NAMES \''. $config['charset'] .'\'');
        }
        if(isset($config['prefix'])) {
            $this->tablePrefix = $config['prefix'];
        }
    }
    
    private function initSql() {
        $fields = $this->fields()
    }
    
    /**
     * 指定查询表
     *
     * @param string $table 要查询的表
     */
    public function table($table) {
        
    }
    
    /**
     * 指定查询条件
     *
     * @param string $condition 查询条件
     */
    public function where($condition) {
        
    }
    
    /**
     * 指定排序条件
     *
     * @param string $order 排序条件
     */
    public function orderBy($order) {
        
    }
    
    /**
     * 获取记录
     */
    public function getAll() {
        $this->initSql();
        
    }
    
    /**
     * 获取一条记录
     *
     * @return array 结果集
     */
    public function getOne() {
        
    }
    
    /**
     * 插入记录
     *
     * @param array 数据
     * @return int insertId
     */
    public function insert(& $data) {
        
    }
    
    /**
     * 删除记录
     *
     * @return int 影响行数
     */
    public function delete() {
        
    }
    
    /**
     * 修改记录
     *
     * @param array 数据
     * @return int 影响行数
     */
    public function update(& $data) {
        
    }
    
    /**
     * 获取记录数
     *
     * @return int 结果
     */
    public function count() {
        
    }
}
