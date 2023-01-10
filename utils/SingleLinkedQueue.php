<?php
namespace fate\util;

/**
 * 链队列
 */
class SingleLinkedQueue implements IQueue {

    private $headNode = null;
    private $tailNode = null;
    private $length = 0;

    /**
     * {@inheritdoc}
     */
    public function size() {
        return $this->length;
    }

    /**
     * {@inheritdoc}
     */
    public function add($data) {
        $node = new LinkedQueueNode($data, null);

        if(0 === $this->length) {
            $this->headNode = $node;

        } else {
            $this->tailNode->next = $node;
        }

        $this->tailNode = $node;
        $this->length++;
    }

    /**
     * {@inheritdoc}
     */
    public function take() {
        // 为空直接返回
        if(0 === $this->length) {
            return null;
        }

        $data = $this->headNode->data;
        $tmpHeadNode = $this->headNode;

        // 从队列去除头节点
        $this->headNode = $tmpHeadNode->next;
        $tmpHeadNode->next = null;
        $tmpHeadNode = null;

        // 没节点了
        if(null === $this->headNode) {
            $this->headNode = $this->tailNode = null;
        }

        $this->length--;

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($data) {
        $current = $this->headNode;
        $previous = null;

        for(; null !== $current; $previous = $current, $current = $current->next) {
            if($data !== $current->data) {
                continue;
            }

            // 删除头结点
            if(null === $previous) {
                $this->headNode = $current->next;
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
        while(0 !== $this->length) {
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

