<?php
/**
 * @author
 * @license MIT
 */
namespace fate\db;

/**
 * 查询操作生成器接口
 */
interface IQuery {
    
    /**
     * Executes the query and returns a single row of result
     *
     * @return array 结果集的第一行记录
     */
    public function getOne();
    
    /**
     * Executes the query and returns all results as an array
     *
     * @return array 所有记录
     */
    public function getAll();
    
    /**
     * Returns the number of records
     *
     * @param string $column
     * @return integer
     */
    public function count($column = '*');
    
    /**
     * Set the columns to select
     *
     * @param string $columns
     * @return $this
     */
    public function select($columns);
    
    /**
     * Set the target to select
     *
     * @param string $table
     * @return $this
     */
    public function from($table);
    
    /**
     * Sets the WHERE condition of a query
     *
     * @param string $condition
     * @param array $params
     * @return $this
     */
    public function where($condition, $params = null);
    
    /**
     * Sets the ORDER BY condition of a query
     *
     * @param string $columns
     * @return $this
     */
    public function orderBy($columns);
    
}
