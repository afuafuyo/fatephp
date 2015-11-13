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
     * 获取记录数
     *
     * @param string $field 列
     * @return int 结果
     */
    public function count($field = '*');
    
}
