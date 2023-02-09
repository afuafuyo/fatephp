<?php
/**
 * @author
 * @license MIT
 */
namespace fate\core;

use Fate;


/**
 * 过滤链静态工厂
 */
class FilterFactory {

    private function __construct() {}

    /**
     * 创建过滤连
     *
     * @param mixed $resource 资源
     * @return FilterChain
     */
    public static function createFilterChain($resource) {
        $filterChain = new FilterChain();
        $filterChain->setResource($resource);

        $filters = $resource->filters();
        if(null === $filters) {
            return $filterChain;
        }

        foreach($filters as $filter) {
            if(!method_exists($filter, 'doFilter'))
                $filter = Fate::createObject($filter);
            }

            $this->filterChain->addFilter($filter);
        }

        return filterChain;
    }

}
