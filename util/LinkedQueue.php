<?php
namespace fate\util;

/**
 * 链队列
 */
class LinkedQueue implements Queue {
    
    private $_headNode = null;
    
    private $_tailNode = null;
    
    private $_size = 0;
    
    private $currentIteratorNode = null;
    
    /**
     * {@inheritdoc}
     */
    public function iterator() {
        if(null === $this->currentIteratorNode) {
            $this->currentIteratorNode = $this->_headNode;
            
        } else {
            $this->currentIteratorNode = $this->currentIteratorNode->next;
        }
        
        if(null === $this->currentIteratorNode) {
            $this->currentIteratorNode = null;
            
            return null;
        }
        
        return $this->currentIteratorNode->data;
    }
    
    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function remove($data) {
        $current = $this->_headNode;
        $previous = null;
        
        for(; null !== $current; $previous = $current, $current = $current->next) {
            if($data !== $current->data) {
                continue;
            }
            
            // 删除头结点
            if(null === $previous) {
                $this->_headNode = $current->next;
            }
            
            // 非头结点需要移动 previous
            if(null !== $previous) {
                $previous->next = $current->next;
            }
            
            // 尾节点
            if(null === $current->next) {
                $this->tailNode = $previous;
            }
            
            // 清除当前节点
            $current->next = null;
            $current = null;
            
            $this->size--;
            
            break;
        }
    }
    
    /**
     * {@inheritdoc}
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

