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
     * Executes the query and returns a single column of row
     *
     * @return string | boolean 结果集的第一行第一列记录 如果没有记录则返回 false
     */
    public function getColumn();

    /**
     * Executes the query and returns a single row of result
     *
     * @return array | boolean 结果集的第一行记录 没有记录时返回 false
     */
    public function getOne();

    /**
     * Executes the query and returns all results as an array
     *
     * @return array | boolean @return array | boolean 包含所有结果的数组 如果没有记录则返回一个空数组 发生错误时返回 false
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
     * 分组
     *
     * @param string $column
     */
    public function groupBy($column);

    /**
     * 筛选
     *
     * @param string $condition
     */
    public function having($condition);

    /**
     * Sets the ORDER BY condition of a query
     *
     * @param string $columns
     * @return $this
     */
    public function orderBy($columns);

}
