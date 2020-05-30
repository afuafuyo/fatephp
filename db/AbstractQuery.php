<?php
/**
 * @author
 * @license MIT
 */
namespace fate\db;

/**
 * SQL 生成器抽象层
 */
abstract class AbstractQuery implements IQuery {

    /**
     * @var string the columns being selected
     */
    public $select = '';

    /**
     * @var string the table to be selected from. For example, 'user'
     */
    public $from = '';

    /**
     * @var string the condition of a query
     */
    public $where = '';

    /**
     * @var string the column of group by
     */
    public $groupBy = '';

    /**
     * @var string the condition of a query
     */
    public $having = '';

    /**
     * @var string the sort condition
     */
    public $orderBy = '';

    /**
     * @var array other sql information
     */
    public $options = [];

    /**
     * @var array list of query parameter values. For example, [':name' => 'li', ':age' => 20]
     */
    public $parameters = [];

    /**
     * Set parameters
     *
     * @param array $parameters
     */
    public function addParameters($parameters) {
        foreach($parameters as $name => $value) {
            if(is_int($name)) {
                $this->parameters[] = $value;

                continue;
            }

            $this->parameters[$name] = $value;
        }
    }

}
