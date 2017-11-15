<?php
namespace y\util;

/**
 * 队列接口
 */
interface Queue {
	
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
     * 清空列表
     */
    public function clear();
    
}
