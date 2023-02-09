<?php
/**
 * @author
 * @license MIT
 */
namespace fate\core;

/**
 * 过滤链接口
 */
interface IFilterChain {

    /**
     * Invoked the next filter or the resource
     */
    public function doFilter();

}
