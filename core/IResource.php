<?php
/**
 * @author
 * @license MIT
 */
namespace fate\core;

use Fate;

interface IResource {

    /**
     * 声明资源过滤器
     */
    public function filters();

    /**
     * 执行
     */
    public function run();

}
