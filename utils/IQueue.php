<?php
namespace fate\util;

/**
 * 队列接口
 */
interface IQueue {

    /**
     * 返回队列大小
     */
    public function size();

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
