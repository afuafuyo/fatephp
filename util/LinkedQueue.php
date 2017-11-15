<?php
namespace y\util;

/**
 * 链队列
 */
class LinkedQueue implements Queue {
    private $_headNode = null;
    private $_tailNode = null;
    private $_size = 0;
    
    /**
     * @inheritdoc
     */
    public function add($data) {
        $node = new LinkedQueueNode($data, null);
        
        if(0 === $this->_size) {
            $this->_headNode = $node;
            
        } else {
            $this->_tailNode->next = $node;
        }
        
        $this->_tailNode = $node;
        $this->_size++;
    }
    
    /**
     * @inheritdoc
     */
    public function take() {
        // 为空直接返回
        if(0 === $this->_size) {
            return null;
        }
        
        $data = $this->_headNode->data;
        $tmpHeadNode = $this->_headNode;
        
        // 从队列去除头节点
        $this->_headNode = $tmpHeadNode->next;
        $tmpHeadNode->next = null;
        $tmpHeadNode = null;
        
        // 没节点了
        if(null === $this->_headNode) {
            $this->_headNode = $this->_tailNode = null;
        }
        
        $this->_size--;
        
        return $data;
    }
    
    /**
     * @inheritdoc
     */
    public function clear() {
        while(0 !== $this->_size) {
            $this->take();
        }
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

