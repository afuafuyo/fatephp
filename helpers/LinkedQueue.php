<?php
namespace y\helpers;

/**
 * 链队列
 */
class LinkedQueue {
    private $headNode = null;
    private $tailNode = null;
    private $size = 0;
    
    public function __construct() {
        $this->headNode = $this->tailNode = new LinkedQueueNode(null, null);
    }
    
    /**
     * 队列大小
     *
     * @return int
     */
    public function size() {
        return $this->size;
    }
    
    /**
     * 出队列
     *
     * @return Object
     */
    public function take() {
        // 为空直接返回
        if($this->headNode === $this->tailNode) {
            return null;
        }
        
        // 队列中头节点
        $tmpHeadNode = $this->headNode->next;
        $data = $tmpHeadNode->data;
        
        // 从队列去除头节点
        $this->headNode->next = $tmpHeadNode->next;
        $tmpHeadNode->next = null;
        
        // 没节点了 重置 tail
        if(null === $this->headNode->next) {
            $this->tailNode = $this->headNode;
        }
        
        $tmpHeadNode = null;
        $this->size--;
        
        return $data;
    }
    
    /**
     * 入队列
     */
    public function put($data) {
        $node = new LinkedQueueNode($data, null);
        // 队尾指向新节点
        $this->tailNode->next = $node;
        // 重新指定尾节点
        $this->tailNode = $node;
        // 计数
        $this->size++;
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
