<?php
namespace fate\util;

/**
 * 队列接口
 */
interface Queue {
    
    /**
     * 迭代队列
     * 此方法不保证线程安全
     *
     * @return Object | null
     */
    public function iterator();
    
    /**
     * 列表添加元素
     *
     * @param Object $data 数据
     */
    public function add($data);
    
    /**
     * 移除并返回第一个元素
     */
    public function take();
    
    /**
     * 删除一个元素
     *
     * @param Object data 要删除的元素
     */
    public function remove($data);
    
    /**
     * 清空列表
     */
    public function clear();
    
}
