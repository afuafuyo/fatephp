<?php
/**
 * @author
 * @license MIT
 */
namespace fate\db\mysql;

/**
 * Mysql sql 查询生成器
 */
class Query extends \fate\db\AbstractQuery {

    public static $OPERATE_QUERYALL = 1;
    public static $OPERATE_QUERYONE = 2;
    public static $OPERATE_COUNT = 3;
    public static $MAX_LIMIT = 10000;

    /**
     * @var Db
     */
    public $db = null;
    public $op = -1;
    public $data = null;
    public $sqlString = '';

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * @param string $name
     * @return string
     */
    private function quote($name) {
        return false !== strpos($name, '`') || '*' === $name ? $name : "`$name`";
    }

    /**
     * @return string
     */
    private function quoteField($field) {
        if(false === strpos($field, '.')) {
            return $this->quote($field);
        }

        $parts = explode('.', $field);

        foreach($parts as $i => $v) {
            $parts[$i] = $this->quote($v);
        }

        return implode('.', $parts);
    }

    /**
     * 生成 select
     *
     * @return string
     */
    private function buildSelect() {
        $select = 'SELECT';

        if('' === $this->select || '*' === $this->select) {
            return $select . ' *';
        }

        /*
        $fields = explode(',', $this->select);

        // trim & quote
        foreach($fields as $i => $field) {
            $fields[$i] = $this->quoteField( trim($field, ' ') );
        }

        return $select . ' ' . implode(', ', $fields);
        */
        return $select . ' ' . $this->select;
    }

    /**
     * @return string
     */
    private function buildFrom() {
        if('' === $this->from) {
            return '';
        }

        // return 'FROM ' . $this->quoteField($this->from);
        return 'FROM ' . $this->from;
    }

    /**
     * @return string
     */
    private function buildWhere() {
        return '' === $this->where ? '' : 'WHERE ' . $this->where;
    }

    /**
     * @return string
     */
    private function buildGroupBy() {
        if('' === $this->groupBy) {
            return '';
        }

        /*
        $fields = explode(',', $this->groupBy);

        // trim & quote
        foreach($fields as $i => $field) {
            $fields[$i] = $this->quoteField( trim($field, ' ') );
        }

        return 'GROUP BY ' . implode(', ', $fields);
        */
        return 'GROUP BY ' . $this->groupBy;
    }

    /**
     * @return string
     */
    private function buildHaving() {
        if('' === $this->having) {
            return '';
        }

        return 'HAVING ' . $this->having;
    }

    /**
     * @return string
     */
    private function buildOrderBy() {
        if('' === $this->orderBy) {
            return '';
        }

        return 'ORDER BY ' . $this->orderBy;
    }

    /**
     * @return string
     */
    private function buildLimit() {
        if( !isset($this->options['limit']) ) {
            return 'LIMIT 0, ' . self::$MAX_LIMIT;
        }

        return self::$OPERATE_QUERYONE === $this->op ?
            'LIMIT 0, 1' :
            'LIMIT ' . $this->options['limit'];
    }

    public function buildSql() {
        $sql = '';

        switch($this->op) {
            case self::$OPERATE_QUERYONE :
            case self::$OPERATE_QUERYALL :
                // select * from t
                // where x
                // group by x
                // having x
                // order by x
                // limit x
                $parts = [
                    $this->buildSelect(),
                    $this->buildFrom(),
                    $this->buildWhere(),
                    $this->buildGroupBy(),
                    $this->buildHaving(),
                    $this->buildOrderBy(),
                    $this->buildLimit()
                ];

                $sql = implode(' ', $parts);

                break;

            case self::$OPERATE_COUNT :
                $field = $this->select;
                $from = $this->buildFrom();
                $where = $this->buildWhere();

                $sql = "SELECT COUNT(`{$field}`) {$from} {$where}";

            default :
                break;
        }

        return $sql;
    }

    /**
     * {@inheritdoc}
     * @see \fate\db\IQuery::getAll()
     */
    public function getAll() {
        $this->op = self::$OPERATE_QUERYALL;

        $this->sqlString = $this->buildSql();

        return $this->db->buildQuery($this)->queryAll();
    }

    /**
     * {@inheritdoc}
     * @see \fate\db\IQuery::getOne()
     */
    public function getOne() {
        $this->op = self::$OPERATE_QUERYONE;

        $this->sqlString = $this->buildSql();

        return $this->db->buildQuery($this)->queryOne();
    }

    /**
     * {@inheritdoc}
     * @see \fate\db\IQuery::getColumn()
     */
    public function getColumn() {
        $this->op = self::$OPERATE_QUERYONE;

        $this->sqlString = $this->buildSql();

        return $this->db->buildQuery($this)->queryColumn();
    }

    /**
     * {@inheritdoc}
     * @see \fate\db\IQuery::count()
     */
    public function count($column = '*') {
        $this->op = self::$OPERATE_COUNT;

        $this->select = $column;

        $this->sqlString = $this->buildSql();

        return $this->db->buildQuery($this)->queryColumn();
    }

    /**
     * {@inheritdoc}
     * @see \fate\db\IQuery::select()
     */
    public function select($columns) {
        $this->select = $columns;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see \fate\db\IQuery::from()
     */
    public function from($table) {
        $this->from = $table;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see \fate\db\IQuery::where()
     */
    public function where($condition, $params = null) {
        $this->where = $condition;

        if(null !== $params) {
            $this->addParams($params);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see \fate\db\IQuery::groupBy()
     */
    public function groupBy($column) {
        $this->groupBy = $column;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see \fate\db\IQuery::having()
     */
    public function having($condition) {
        $this->having = $condition;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see \fate\db\IQuery::orderBy()
     */
    public function orderBy($columns) {
        $this->orderBy = $columns;

        return $this;
    }

    /**
     * 条数限制
     *
     * @param string $limit
     */
    public function limit($limit) {
        $this->options['limit'] = $limit;

        return $this;
    }

}
