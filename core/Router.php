<?php
/**
 * @author yu
 * @license MIT
 */
namespace y\core;

class Router {
    
    /**
     * 解析正则路由
     *
     * @param string $pattern 路由模式
     *
     * pattern: /abc/{id:\d+} -> /abc/(\d+) -> abc\/(\d+)
     * pattern: /abc/{id:} -> /abc/() -> abc\/(\w+)
     * pattern: /abc/{\d+} -> /abc/(\d+) -> abc\/(\d+)
     * pattern: /abc/def -> abc\/def
     *
     */
    public static function parse($pattern) {
        $ret = null;
        
        $pattern = str_replace(['{', '}'], ['(', ')'], $pattern);
        
        // replace
        if(preg_match_all('/\(\w+:/', $pattern, $matches) > 0) {
            $ret = [];
            
            for($i=0,$len=count($matches[0]); $i<$len; $i++) {
                $pattern = str_replace($matches[0][$i], '(', $pattern);
                $pattern = str_replace('()', '(\w+)', $pattern);
                
                // (xx:
                $ret[] = substr($matches[0][$i], 1, -1);
            }
        }
        
        $pattern = trim($pattern, '/');
        $pattern = str_replace('/', '\\/', $pattern);
        
        return [
            'pattern' => $pattern,
            'params' => $ret
        ];
    }
    
}
