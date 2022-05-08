<?php
/**
 * @author
 * @license MIT
 */
namespace fate\core;

/**
 * 控制器动作事件
 */
class ActionEvent extends Event {
    /**
     * @var mixed 数据
     */
    public $data = null;

    /**
     * @var boolean 状态
     */
    public $valid = true;
}
