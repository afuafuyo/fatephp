<?php
/**
 * @author
 * @license MIT
 */
namespace fate\core;

/**
 * 过滤器接口
 */
interface IFilter {

    /**
     * The filter method
     *
     * 该方法需要手动调用 `filterChain.doFilter(request, response)` 来执行下一个过滤器
     *
     * 如果想终止请求而不调用下一个过滤器 那么需要手动执行 `response.end()` 结束请求
     */
    public function doFilter($filterChain);

}
