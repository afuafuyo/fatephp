<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\db;

/**
 * 数据库接口类
 * 本类不考虑安全检查 下层确保数据的安全性
 */
interface IDb {

    /**
     * 指定查询字段
     *
     * @param string $fields 要查询的字段
     */
    public function fields($fields);
    
    /**
     * 指定查询表
     *
     * @param string $table 要查询的表
     */
    public function table($table);
    
    /**
     * 指定查询条件
     *
     * @param string $condition 查询条件
     */
    public function where($condition);
    
    /**
     * 指定排序条件
     *
     * @param string $order 排序条件
     */
    public function orderBy($order);
    
    /**
     * 限制条数
     *
     * @param string | int $limit
     */
    public function limit($limit);
    
    /**
     * 插入记录
     *
     * @param array $data 数据
     * @return int insertId
     */
    public function insert(& $data);
    
    /**
     * 删除记录
     *
     * @return int 影响行数
     */
    public function delete();
    
    /**
     * 修改记录
     *
     * @param array $data 数据
     * @return int 影响行数
     */
    public function update(& $data);
    
    /**
     * 获取记录
     *
     * @return array 结果集
     */
    public function getAll();
    
    /**
     * 获取一条记录
     *
     * @return array 结果集
     */
    public function getOne();
    
    /**
     * 获取上一次执行的 sql 语句
     *
     * @return string
     */
    public function getLastSql();
    
    /**
     * 获取记录数
     *
     * @param string $field 列
     * @return int
     */
    public function count($field);
    
    /**
     * 执行 sql 语句
     *
     * @param string $sql sql 语句
     * @return array 结果数组
     */
    public function querySql($sql);
    
    /**
     * 执行 sql 语句
     *
     * @param string $sql sql 语句
     * @return int 影响行数
     */
    public function executeSql($sql);
}
