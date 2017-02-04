<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\web;

class Router {
    
    /**
     * 解析路由
     *
     * @param object $app 应用实例
     * @param string $route 路由
     */
    public static function resolve($app, $route) {
        // 优先解析自定义路由
        $ret = static::resolveUserroute($app, $route);
        
        if(null !== $ret) {
            return $ret;
        }
        
        return static::resolveRoute($route);
    }
    
    public static function resolveUserroute($app, $route) {
        $moduleId = '';
        $controllerId = '';
        $routePrefix = '';
        
        if(null !== $app->routes) {
            $matches = null;
            foreach($app->routes as $regularRoute => $mapping) {
                if(1 === preg_match('/' . str_replace('/', '\\/', trim($regularRoute, '/')) . '/', $route, $matches)) {
                    if(isset($mapping['moduleId'])) {
                        $moduleId = $mapping['moduleId'];
                    }
                    if(isset($mapping['controllerId'])) {
                        $controllerId = $mapping['controllerId'];
                    }
                    if(isset($mapping['prefix'])) {
                        $routePrefix = $mapping['prefix'];
                    }
                    
                    // 用户自定义路由需要处理参数
                    if(isset($mapping['params'])) {
                        if(is_array($mapping['params']) &&
                            isset($mapping['params']['key']) &&
                            isset($mapping['params']['segment'])) {
                            
                            if(is_array($mapping['params']['key'])) {
                                for($j=0,$len=count($mapping['params']['key']); $j<$len; $j++) {
                                    $_GET[$mapping['params']['key'][$j]] = $matches[$mapping['params']['segment'][$j]];
                                }
                            
                            } else {
                                $_GET[$mapping['params']['key']] = $matches[$mapping['params']['segment']];
                            }
                        }
                    }
                    
                    break;
                }
            }
            
            return ('' !== $moduleId || '' !== $controllerId) ?
                [$moduleId, $controllerId, $routePrefix] :
                null;
        }
        
        return null;
    }
    
    public static function resolveRoute($route) {
        $moduleId = '';
        $controllerId = '';
        $routePrefix = '';
        
        // 解析路由
        if(false !== strpos($route, '/')) {
            list($moduleId, $controllerId) = explode('/', $route, 2);
        } else {
            $moduleId = $route;
        }
        
        // 解析前缀目录
        $routePrefix = $moduleId;
        if('' !== $controllerId && false !== ($pos = strrpos($controllerId, '/')) ) {
            $routePrefix .= '/' . substr($controllerId, 0, $pos);
            $controllerId = substr($controllerId, $pos + 1);
            $routePrefix = str_replace('/', '\\', $routePrefix);  // namespace path
        }
        
        return [$moduleId, $controllerId, $routePrefix];
    }
}
