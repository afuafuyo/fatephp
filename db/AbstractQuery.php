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
     * @var string the columns being selected.
     */
    public $select = '';
    
    /**
     * @var string the table to be selected from. For example, 'user'
     */
    public $from = '';
    
    /**
     * @var string the condition of a query.
     */
    public $where = '';
    
    /**
     * @var string the sort condition.
     */
    public $orderBy = '';
    
    /**
     * @var array other sql information.
     */
    public $options = [];
    
    /**
     * @var array list of query parameter values. For example, [':name' => 'li', ':age' => 20]
     */
    public $params = [];
    
    /**
     * Set params
     *
     * @param array $params
     */
    public function addParams($params) {
        foreach($params as $name => $value) {
            if(is_int($name)) {
                $this->params[] = $value;
                
                continue;
            }
            
            $this->params[$name] = $value;
        }
    }
    
}