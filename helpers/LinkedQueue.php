<?php
namespace y\helpers;

/**
 * 链队列
 */
class LinkedQueue {
    private $_headNode = null;
    private $_tailNode = null;
    private $_size = 0;
    
    public function __construct() {
        $this->_headNode = $this->_tailNode = new LinkedQueueNode(null, null);
    }
    
    /**
     * 队列大小
     *
     * @return int
     */
    public function size() {
        return $this->_size;
    }
    
    /**
     * 出队列
     *
     * @return Object
     */
    public function take() {
        // 为空直接返回
        if($this->_headNode === $this->_tailNode) {
            return null;
        }
        
        // 队列中头节点
        $tmpHeadNode = $this->_headNode->next;
        $data = $tmpHeadNode->data;
        
        // 从队列去除头节点
        $this->_headNode->next = $tmpHeadNode->next;
        $tmpHeadNode->next = null;
        
        // 没节点了 重置 tail
        if(null === $this->_headNode->next) {
            $this->_tailNode = $this->_headNode;
        }
        
        $tmpHeadNode = null;
        $this->_size--;
        
        return $data;
    }
    
    /**
     * 入队列
     */
    public function put($data) {
        $node = new LinkedQueueNode($data, null);
        // 队尾指向新节点
        $this->_tailNode->next = $node;
        // 重新指定尾节点
        $this->_tailNode = $node;
        // 计数
        $this->_size++;
    }
}

class LinkedQueueNode {
    public $data = null;
    public $next = null;
    public function __construct($data, $next) {
        $this->data = $data;
        $this->next = $next;
    }
}
