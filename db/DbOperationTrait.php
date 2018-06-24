<?php
/**
 * @author
 * @license MIT
 */
namespace fate\db;

trait DbOperationTrait {
    
    /**
     * @var int 插入
     */
    public static $INSERT = 1;
    
    /**
     * @var int 删除
     */
    public static $DELETE = 2;
    
    /**
     * @var int 更新
     */
    public static $UPDATE = 3;
    
    /**
     * @var int 查询
     */
    public static $SELECT = 4;
    
    /**
     * @var int 查询一条
     */
    public static $SELECTONE = 5;
    
    /**
     * @var int 计数
     */
    public static $COUNT = 6;

}
