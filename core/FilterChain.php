<?php
/**
 * @author
 * @license MIT
 */
namespace fate\core;

/**
 * 处理请求和响应的责任链
 *
 * ```
 * -- req --> |         | -- req --> |         | -- req --> |     |
 *            | filter1 |            | filter2 |            | RES |
 * <-- res -- |         | <-- res -- |         | <-- res -- |     |
 * ```
 */
class FilterChain implements \fate\core\IFilterChain {

    private $resource = null;

    /**
     * The current position of the filter chain
     */
    private $position = 0;

    /**
     * The filter collection
     */
    private $filters = [];

    /**
     * {@inheritdoc}
     */
    public function doFilter() {
        if($this->position >= count($this.filters)) {
            $this->resource->run();
            $this->clearFilters();
            return;
        }

        $filter = $this->filters[$this->position++];
        $filter->doFilter($this);
    }

    /**
     * 添加过滤器
     */
    public function addFilter($filter) {
        $this->filters[] = $filter;
    }

    /**
     * 清空过滤器
     */
    public function clearFilters() {
        $this->filters = [];
        $this->position = 0;
        $this->resource = null;
    }

    /**
     * 设置资源
     */
    public function setResource($resource) {
        $this->resource = $resource;
    }

}
