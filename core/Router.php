<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\core;

class Router extends Object {
    private $_routes = [];
    
    public function add($route) {
        $this->_routes[] = $route;
    }
    
}
