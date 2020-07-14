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
     * @property mixed 数据
     */
    public $data = null;
    /**
     * @property boolean 状态
     */
    public $valid = true;
}
